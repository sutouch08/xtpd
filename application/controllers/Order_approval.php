<?php
class Order_approval extends PS_Controller
{
  public $menu_code = 'ORDER_APPROVAL';
  public $menu_group_code = 'APPROVAL';
  public $menu_sub_group_code = '';
  public $title = 'Order Approval';
  public $segment = 3;
  public $disSale = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'order_approval';
    $this->load->model('order_approval_model');
    $this->load->model('orders_model');
    $this->load->helper('sales_team_condition');
    $this->load->helper('orders');

    $this->disSale = getConfig('USE_DISCSALE') == 1 ? TRUE : FALSE;
  }

  public function index()
  {
    $this->title = "Order Approval - List";
    $filter = array(
      'con_id' => get_filter('con_id', 'ap_con_id', 'all'),
      'code' => get_filter('code', 'ap_code', ''),     
      'po' => get_filter('po', 'ap_po', ''),
      'customer' => get_filter('customer', 'ap_customer', ''),
      'user_id' => get_filter('user_id', 'ap_user_id', 'all'),            
      'fromDate' => get_filter('fromDate', 'ap_fromDate', ''),
      'toDate' => get_filter('toDate', 'ap_toDate', ''),
      'is_discount_sales' => get_filter('is_discount_sales', 'is_discount_sales', 'all'),
      'min_amount_filter' => get_filter('min_amount_filter', 'ap_min_amount', 'Y')
    );    

    if ($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $apv = $this->order_approval_model->get_approver_by_user_id($this->_user->id);
      $condition = empty($apv) ? [0] : $this->order_approval_model->get_approver_conditions($this->_user->id);
      $filter['conditions'] = $condition;
      $filter['min_amount'] = empty($apv) ? 0 : $apv->min_amount;
      $filter['max_amount'] = empty($apv) ? 0 : $apv->amount;
      $rows = empty($apv) ? 0 : $this->order_approval_model->count_rows($filter);
      $filter['data'] = empty($apv) ? NULL : $this->order_approval_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init  = pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_approval/order_approval_list', $filter);
    }
  }

  
  public function do_approve()
  {
    $sc = TRUE;
    $code = NULL;
    $doc = NULL;
    $ds = json_decode($this->input->post('data'));
    
    if(empty($ds))
    {
      $sc = FALSE;
      set_error('required');
    }

    if($sc === TRUE)
    {
      $code = $ds->code;
      $doc = $this->orders_model->get($code);
    }

    if(empty($doc))
    {
      $sc = FALSE;
      set_error('not_found');
    }

    if($sc === TRUE && ($doc->must_approve != 1 || $doc->Approved != 'P'))
    {
      $sc = FALSE;
      set_error('status');
    }

    if($sc === TRUE && !$this->can_approve($doc))
    {
      $sc = FALSE;
      set_error('permission');
    }

    if($sc === TRUE)
    {
      $approval_status = 'F'; //--- F = full, P = partial
      
      if(!empty($ds->items))
      {
        foreach($ds->items as $item)
        {
          if($item->status == "A")
          {
            $this->orders_model->approve_detail($item->id);
          }

          if($item->status == "R")
          {
            $this->orders_model->reject_detail($item->id, get_null($item->reject_text));
            $approval_status = 'P';
          }
        }

        //--- approve order
        $arr = array(
          'Approved' => 'A',
          'Approver' => $this->_user->uname,
          'ApproveDate' => now(),
          'Approval_status' => $approval_status
        );

        if( ! $this->orders_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('update');
        }
        else
        {
          $this->doExport($code);
        }
      }
    }

    $this->_response($sc);
  }

  
  public function do_reject()
  {
    $sc = TRUE;
    $code = NULL;
    $doc = NULL;
    $ds = json_decode($this->input->post('data'));

    if(empty($ds))
    {
      $sc = FALSE;
      set_error('required');
    }

    if($sc === TRUE)
    {
      $code = $ds->code;
      $doc = $this->orders_model->get($code);
    }

    if(empty($doc))
    {
      $sc = FALSE;
      set_error('not_found');
    }

    if($sc === TRUE)
    {
      if($doc->must_approve == 1 && $doc->Approved == 'P')
      {
        if($this->can_approve($doc))
        {
          $this->orders_model->reject_details($code);

          //--- approve order
          $arr = array(
            'Approved' => 'R',
            'Approver' => $this->_user->uname,
            'ApproveDate' => now(),
            'Approval_status' => 'R'
          );

          $this->orders_model->update($code, $arr);

          $items = $ds->items;

          if(!empty($items))
          {
            foreach($items as $item)
            {
              $reject_text = get_null($item->reject_text);

              if(!empty($reject_text))
              {
                $arr = array('reject_text' => $item->reject_text);
                $this->orders_model->update_detail($item->id, $arr);
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('permission');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('status');
      }
    }
    
    $this->_response($sc);
  }


  public function get_detail()
  {
    $sc = TRUE;

    $code = $this->input->get('code');

    if (! empty($code))
    {
      $doc = $this->orders_model->get($code);

      if (! empty($doc))
      {
        $this->load->model('payment_term_discount_model');

        $can_approve = $this->can_approve($doc);

        $ds = array(
          'orderCode' => $doc->code,
          'user' => $doc->uname,         
          'customerName' => $doc->CardCode . ' | ' . $doc->CardName,
          'billToCode' => $doc->PayToCode,
          'billToAddress' => $doc->Address,
          'shipToCode' => $doc->ShipToCode,
          'shipToAddress' => $doc->Address2,
          'exShipTo' => $doc->Address3,
          'currency' => $doc->DocCur,
          'currencyRate' => $doc->DocRate,
          'docDate' => thai_date($doc->DocDate, FALSE),
          'dueDate' => thai_date($doc->DocDueDate, FALSE),
          'PoNo' => $doc->NumAtCard,
          'fileName' => $doc->has_file ? '&nbsp;&nbsp; <span class="label label-info label-white pointer" onclick="openFile(\'' . $doc->file_name . '\')"><i class="fa fa-paperclip"></i>' . $doc->file_name . '</span>' : NULL,
          'PriceList' => order_price_list_name($doc->PriceList, $doc->SpecialPriceList),
          'termName' => term_name($doc->term_id),
          'billOption' => $doc->BillDate == 1 ? 'Y' : 'N',
          'requiredSQ' => $doc->requireSQ == 1 ? 'Y' : 'N',
          'isExport' => $doc->isExport == 1 ? 'Export' : 'Local',
          'remark' => $doc->Comments,          
          'CanApprove' => $can_approve,
          'items' => [],
          'subTotal' => NULL
        );        

        $details = $this->orders_model->get_details($code);
        $totalBefDi = 0;

        if (! empty($details))
        {
          $no = 1;
          foreach ($details as $rs)
          {
            $totalBefDi += $rs->LineTotal;

            if ($rs->free_item == 0)
            {
              $open_qty = (!empty($doc->DocNum) ? $this->orders_model->get_open_qty($doc->code, $rs->ItemCode) : ($rs->freeQty + $rs->Qty));
              
              $ds['items'][] = array(
                'id' => $rs->id,
                'no' => $no,
                'itemName' => $rs->ItemName,
                'qty' => number($rs->Qty, 2),
                'free' => number($rs->freeQty, 2),
                'uom' => $rs->UomCode,
                'stdPrice' => number($rs->stdPrice, 2),
                'sellPrice' => number($rs->SellPrice, 2),
                'amount' => number($rs->LineTotal, 2),
                'dis' => $rs->discount_sales == 1 ? '<i class="fa fa-check blue"></i>' : '',
                'lineText' => $rs->LineText,
                'openQty' => number($open_qty, 2),                
                'checkbox' => get_checkbox($rs->id, $rs->status, $can_approve, $no), //--- orders_helper
                'rejectbox' => get_rejectbox($rs->id, $rs->status, $can_approve, $no)
              );              

              $no++;
            }
          }

          $ds['subTotal'] = array(
            'totalBefDi' => number($totalBefDi, 2),
            'DiscPrcnt' => $doc->DiscPrcnt,
            'DiscSum' => number($doc->DiscSum, 2),
            'totalBefVat' => number($doc->DocTotal - $doc->VatSum, 2),
            'totalVat' => number($doc->VatSum, 2),
            'docTotal' => number($doc->DocTotal, 2)
          );
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Order Code : {$code}";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : Code";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function can_approve($order)
  {
    if ($this->isGM)
    {
      return TRUE;
    }

    $this->load->model('sales_team_condition_model');   
    //---- อยู่ในรายชื่อที่มีสิทธิ์ อนุมัติมั้ย
    $approver = $this->sales_team_condition_model->get_approver($order->Condition_id, $this->_user->id);

    if (! empty($approver))
    {
      if ($approver->amount >= $order->DocTotal)
      {
        return  TRUE;
      }
    }

    return FALSE;
  }

  public function doExport($code)
  {
    $sc = TRUE;

    $this->load->library('export');

    if (!$this->export->export_order($code))
    {
      $sc = FALSE;
      $this->error = $this->export->error;
    }

    return $sc;
  }


  public function clear_filter()
  {
    $filter = array('ap_con_id', 'ap_code', 'ap_po', 'ap_customer', 'ap_user_id', 'ap_fromDate', 'ap_toDate', 'is_discount_sales', 'ap_min_amount');
    clear_filter($filter);
  }  

} // end class 
