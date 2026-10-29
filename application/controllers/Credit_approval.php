<?php
class Credit_approval extends PS_Controller
{
  public $menu_code = 'CREDIT_APPROVAL';
  public $menu_group_code = 'APPROVAL';
  public $title = 'Credit Approval';
  public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'credit_approval';
    $this->load->model('credit_approval_model');
    $this->load->model('credit_approver_model');
    $this->load->model('special_price_list_model');
    $this->load->model('customer_model');
    $this->load->model('orders_model');
    $this->load->helper('credit_approval');
    $this->load->helper('orders');
  }


  public function index()
  {
    $this->title = "Credit Approval - List";
    $filter = array(
      'WebCode' => get_filter('WebCode', 'capv_code', ''),
      'CardCode' => get_filter('CardCode', 'capv_customer', ''),
      'credit_approval' => get_filter('credit_approval', 'capv_credit_approval', 'all'),
      'status' => get_filter('status', 'capv_status', 'all'),     
      'user_id' => get_filter('user_id', 'capv_user', 'all'),
      'is_overdue' => get_filter('is_overdue', 'capv_overdue', 'all'),
      'from_date' => get_filter('from_date', 'capv_from_date', ''),
      'to_date' => get_filter('to_date', 'capv_to_date', '')
    );

    $apv = $this->credit_approver_model->get_active_by_user_id($this->_user->id);

    $perpage = get_rows();
    $rows = empty($apv) ? 0 : $this->credit_approval_model->count_rows($filter);
    $filter['data'] = empty($apv) ? NULL : $this->credit_approval_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
    $filter['can_approve'] = empty($apv) ? FALSE : $apv->can_approve;
    $filter['can_review'] = empty($apv) ? FALSE : $apv->can_review;

    if(!empty($filter['data']))
    {
      foreach($filter['data'] as $rs)
      {
        $rs->creditBalance = $this->get_credit_balance($rs->CardCode);
      }
    }

    $init  = pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
    $this->pagination->initialize($init);
    $this->load->view('credit_approval/credit_approval_list', $filter);
  }

  private function get_file_list($code)
  {
    $list = array();    
    $file_path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/';
    

    if (is_dir($file_path))
    {
      if ($handle = opendir($file_path))
      {
        $no = 1;

        while (FALSE !== ($entry = readdir($handle)))
        {
          if ($entry !== '.' && $entry !== '..')
          {
            $f = $file_path . $entry;

            if (is_file($f))
            {
              $file = (object) array(
                'no' => $no,
                'orderCode' => $code,
                'name' => $entry,
                'size' => number(ceil((filesize($f) / 1024))) . " KB",
                'date_modify' => date('d-m-Y H:i:s', filemtime($f))
              );

              $list[] = $file;
              $no++;
            }
          }
        }

        closedir($handle);
      }
    }

    return $list;
  }

  public function open_file($code, $filename)
  {    
    $path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/' . $filename;

    if (!file_exists($path))
    {
      show_404();
      return;
    }

    // Detect MIME type automatically
    $mime = mime_content_type($path);

    // Force browser to display file inline
    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($path));

    readfile($path);
  }


  public function download_file($code, $filename)
  {
    // ป้องกันการโจมตีด้วย ../
    $filename = basename($filename);

    // โฟลเดอร์เก็บไฟล์    
    $path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/' . $filename;

    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'xlsx', 'xls', 'zip', 'txt'];

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed))
    {
      show_error("File type not allowed");
      return;
    }

    // เช็คว่ามีไฟล์จริงไหม
    if (!file_exists($path))
    {
      show_404();
      return;
    }

    // โหลด helper ของ CI
    $this->load->helper('download');

    // อ่านไฟล์
    $data = file_get_contents($path);

    // สั่งดาวน์โหลด
    force_download($filename, $data);
  }

  public function get_credit_balance($CardCode)
  {
    $credit = $this->get_credit_data($CardCode);
    return $credit->CreditBalance;
  }

  public function get_credit_data($CardCode)
  {   
    $res = array(      
      'CreditLine' => 0.00,           
      'CreditBalance' => 0.00
    );
    
    $card = $this->customer_model->get_by_code($CardCode);

    if (! empty($card))
    {
      $credit = empty($card->CustCode) ? $this->customer_model->get_credit_details($card->CardCode) : $this->customer_model->get_credit_details_by_hCode($card->CustCode);
      $CreditLine = $card->isRegular == 1 ? floatval($credit->CreditLine) : floatval(getConfig('GLOBAL_CREDIT_LINE'));      

      if (! empty($credit))
      {
        $orderUsed = $this->orders_model->get_credit_used_amount($card->CardCode, $card->CustCode);
        $creditBalance = $CreditLine - ($credit->Balance + $credit->DNotesBal + $credit->OrdersBal + $orderUsed);

        $res = array(          
          'CreditLine' => $CreditLine,        
          'CreditBalance' => $creditBalance
        );
      }
    }

    return (object) $res;
  }

  public function get_order_detail()
  {
    $sc = TRUE;
    $code = $this->input->get('code');
    $doc = $this->credit_approval_model->get($code);

    if (!empty($doc))
    {
      $this->load->model('payment_term_discount_model');      
      $apv = $this->credit_approver_model->get_active_by_user_id($this->_user->id);
      $can_approve = empty($apv) ? FALSE : is_true($apv->can_approve);
      $can_review = empty($apv) ? FALSE : is_true($apv->can_review);
      $credit = $this->get_credit_data($doc->CardCode);      
      
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
        'PriceList' => order_price_list_name($doc->PriceList, $doc->SpecialPriceList),
        'termName' => term_name($doc->term_id),
        'billOption' => $doc->BillDate == 1 ? 'Y' : 'N',
        'requiredSQ' => $doc->requireSQ == 1 ? 'Y' : 'N',
        'isExport' => $doc->isExport == 1 ? 'Export' : 'Local',
        'remark' => $doc->Comments,
        'approval_status' => $doc->credit_approval,
        'is_overdue' => is_true($doc->is_over_due),
        'credit_review' => is_true($doc->credit_review),
        'can_approve' => $can_approve,
        'can_review' => $can_review,
        'items' => array(),
        'logs' => array(),
        'doc_total' => number($doc->DocTotal, 2),
        'credit_diff' => number($doc->credit_diff, 2),
        'credit_limit' => number($credit->CreditLine, 2),
        'credit_balance' => number($credit->CreditBalance, 2),
        'case_id' => $doc->credit_case_id,
        'overdue_amount' => $doc->is_over_due ? number($this->get_overdue_amount($doc->CardCode, $doc->CustCode), 2) : 0,
        'has_document' => FALSE,
        'promotionCode' => $doc->promotion_code,       
        'subTotal' => NULL
      );


      if($doc->credit_case_id)
      {
        $this->load->model('payment_request_model');
        $req = $this->payment_request_model->get($doc->code);
        $ds['has_document'] = !empty($req) && $req->has_document ? TRUE : FALSE;

        if($ds['has_document'])
        {          
          $ds['files'] = $this->get_file_list($doc->code);
        }
      }
      
      $details = $this->credit_approval_model->get_details($code);
      $totalBefDi = 0;

      if (!empty($details))
      {
        $no = 1;
        foreach ($details as $rs)
        {
          $totalBefDi += $rs->LineTotal;

          if($rs->free_item == 0)
          {
            $open_qty = (!empty($doc->DocNum) ? $this->orders_model->get_open_qty($doc->code, $rs->ItemCode) : ($rs->freeQty + $rs->Qty));
            $ds['items'][] = array(
              'no' => $no,
              'id' => $rs->id,
              //'itemCode' => $rs->ItemCode,
              'itemName' => $rs->ItemName,
              'qty' => number($rs->Qty, 2),
              'free' => number($rs->freeQty, 2),
              'openQty' => number($open_qty, 2),
              'uom' => $rs->UomCode,
              'stdPrice' => number($rs->stdPrice, 2),
              'sellPrice' => number($rs->SellPrice, 2),
              'amount' => number($rs->LineTotal, 2),
              'dis' => $rs->discount_sales == 1 ? '<i class="fa fa-check blue"></i>' : '',
              'lineText' => $rs->LineText,
              'checkbox' => get_checkbox($rs->id, $rs->status, $can_approve, $no), //--- orders_helper
              'rejectbox' => ($rs->status == 'P' ? get_rejectbox($rs->id, $rs->status, $can_approve, $no) : $rs->reject_text)
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

      $logs = $this->credit_approval_model->get_logs($code);

      if (!empty($logs))
      {
        foreach ($logs as $log)
        {
          $action = $log->action == 'A' ? 'Approved' : ($log->action == 'R' ? 'Rejected' : ($log->action == 'P' ? 'Accepted' : 'Unknown'));
          $logx = "<p class=\"log-text\">{$action} by " . uname($log->user_id) . " @ " . thai_date($log->date_upd, TRUE) . "</p>";

          $ds['logs'][] = array(
            'logx' => $logx
          );
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('Order not found');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_order_data()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $doc = $this->credit_approval_model->get($code);

    if (!empty($doc))
    {      
      $ds = array(
        'date' => thai_date($doc->date_add),
        'code' => $doc->code,
        'user' => $doc->uname,
        'emp_name' => emp_name($doc->uname),
        'customer' => $doc->CardCode . ' | ' . $doc->CardName,
        'doc_total' => number($doc->DocTotal, 2),
        'diff' => number($doc->credit_diff, 2),        
        'overdue' => $doc->is_over_due ? number($this->get_overdue_amount($doc->CardCode, $doc->CustCode), 2) : 0
      );
    }
    else
    {
      $sc = FALSE;
      set_error('Order not found');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }

  public function get_request_payment_data()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $this->load->model('payment_request_model');

      $doc = $this->payment_request_model->get($code);

      if (!empty($doc))
      {
        $apv = $this->credit_approver_model->get_active_by_user_id($this->_user->id);
        $can_approve = empty($apv) ? FALSE : $apv->can_approve;
        $can_review = empty($apv) ? FALSE : $apv->can_review;

        $ds = array(
          'date' => thai_date($doc->date_add),
          'code' => $doc->code,          
          'customer' => $doc->CardCode . ' | ' . $doc->CardName,
          'request_by' => emp_name_by_id($doc->add_by),
          'request_message' => $doc->message,
          'reply_by' => emp_name($doc->uname),
          'reply_message' => $doc->reply_message,
          'request_date' => thai_date($doc->request_date, TRUE),
          'reply_date' => empty($doc->reply_date) ? NULL : thai_date($doc->reply_date, TRUE),
          'doc_total' => number($doc->DocTotal, 2),
          'credit_balance' => number($this->get_credit_balance($doc->CardCode), 2),
          'overdue' => number($this->get_overdue_amount($doc->CardCode, $doc->CustCode), 2),
          'can_approve' => is_true($can_approve),
          'can_review' => is_true($can_review),
          'status' => $doc->status,
          'has_document' => $doc->has_document,
          'files' => $doc->has_document == 1 ? $this->get_file_list($code) : NULL,
          'logs' => array()
        );

        $logs = $this->credit_approval_model->get_logs($code);

        if (!empty($logs))
        {
          foreach ($logs as $log)
          {
            $action = $log->action == 'A' ? 'Approved' : ($log->action == 'R' ? 'Rejected' : ($log->action == 'P' ? 'Accepted' : 'Unknown'));
            $logx = "<p class=\"log-text\">{$action} by " . uname($log->user_id) . " @ " . thai_date($log->date_upd, TRUE) . "</p>";

            $ds['logs'][] = array(
              'logx' => $logx
            );
          }
        }
      }
      else
      {
        $sc = FALSE;
        set_error('Order not found');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('Invalid order code');
    }    

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function do_accept()
  {    
    $sc = TRUE;
    $code = $this->input->post('code');
    $doc = $this->orders_model->get($code);

    if (! empty($doc))
    {      
      $this->load->model('payment_request_model');

      $arr = array(
        'credit_approval' => 'P',
        'credit_review' => 1,        
        'credit_case_status' => 'C'
      );  

      $this->db->trans_begin();

      if (! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        set_error('Failed to update order');
      }

      if($sc === TRUE && ! empty($doc->credit_case_id))
      {
        $arr = array(
          'status' => 'A',
          'update_by' => $this->_user->id,
          'date_upd' => now()
        );

        if( ! $this->payment_request_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('Failed to update request payment');
        }
      }

      if ($sc === TRUE)
      {
        $log = array(
          'order_code' => $code,
          'user_id' => $this->_user->id,
          'action' => 'P', //-- P = preview, A = approve, R = reject
          'date_upd' => now()
        );

        $this->credit_approval_model->add_log($log);
      }

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else 
      {
        $this->db->trans_rollback();
      }      
    }
    else
    {
      $sc = FALSE;
      set_error('Order not found');
    }

    $this->_response($sc);
  }


  public function do_approve()
  {    
    $this->load->model('sales_team_condition_model');
    $sc = TRUE;
    $code = NULL;
    $doc = NULL;
    $ds = json_decode($this->input->post('data'));

    if(empty($ds))
    {
      $sc = FALSE;
      set_error('Invalid data');
    }

    if($sc === TRUE)
    {
      $code = $ds->code;
      $doc = $this->orders_model->get($code);
    }    

    if (! empty($doc) && ! empty($ds->items))
    {
      $approval_status = 'F'; //-- F = final approval, P = pending approval
      $this->db->trans_begin();

      foreach($ds->items as $item)
      {
        if($item->status == 'A')
        {
          $this->orders_model->approve_detail($item->id);
        }

        if($item->status == 'R')
        {
          $this->orders_model->reject_detail($item->id, get_null($item->reject_text));
          $approval_status = 'P';
        }        
      }

      $arr = array(
        'Approved' => 'A',
        'Approver' => $this->_user->uname,
        'ApproveDate' => now(),
        'Approval_status' => $approval_status,
        'credit_approval' => 'A',
        'credit_approver' => $this->_user->id,
        'credit_case_status' => 'C'
      );

      if (! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        set_error('Failed to update order');
      }

      if($sc === TRUE && ! empty($doc->credit_case_id) && $doc->credit_case_status != 'C')
      {
        $this->load->model('payment_request_model');

        $arr = array(
          'status' => 'A',
          'update_by' => $this->_user->id,
          'date_upd' => now()
        );

        if( ! $this->payment_request_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('Failed to update request payment');
        }
      }

      if($sc === TRUE)
      {
        $log = array(
          'order_code' => $code,
          'user_id' => $this->_user->id,
          'action' => 'A', //-- P = preview, A = approve, R = reject
          'date_upd' => now()
        );

        $this->credit_approval_model->add_log($log);
      }      

      if ($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }

      if ($sc === TRUE)
      {
        $this->load->library('export');
        $this->export->export_order($code);
      }
    }
    else
    {
      $sc = FALSE;
      set_error('Order not found');
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
      set_error('Invalid data');
    }

    if($sc === TRUE)
    {
      $code = $ds->code;
      $doc = $this->orders_model->get($code);
    }

    if($sc === TRUE && ! empty($doc))
    {
      $arr = array(
        'Approved' => 'R',
        'Approver' => $this->_user->uname,
        'ApproveDate' => now(),
        'Approval_status' => 'R',
        'credit_approval' => 'R',
        'credit_approver' => $this->_user->id,
        'credit_case_status' => 'C'
      );

      $this->db->trans_begin();

      if (! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        set_error('Failed to update order');
      }

      if($sc === TRUE && ! empty($ds->items) && count($ds->items) > 0)
      {
        if(! $this->orders_model->reject_details($code))
        {
          $sc = FALSE;
          set_error('Failed to reject order details');
        }

        if($sc === TRUE)
        {
          $batch = array();

          foreach ($ds->items as $item)
          {            
            $batch[] = array(
              'id' => $item->id,              
              'reject_text' => get_null($item->reject_text)
            );
          }

          if(! empty($batch))
          {
            if(! $this->orders_model->update_details_by_batch($batch))
            {
              $sc = FALSE;
              set_error('Failed to update order details');
            }
          }
        }              
      }      

      if($sc === TRUE && ! empty($doc->credit_case_id) && $doc->credit_case_status != 'C')
      {
        $this->load->model('payment_request_model');

        $arr = array(
          'status' => 'R',
          'update_by' => $this->_user->id,
          'date_upd' => now()
        );

        if( ! $this->payment_request_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('Failed to update request payment');
        }
      }

      if ($sc === TRUE)
      {
        $log = array(
          'order_code' => $code,
          'user_id' => $this->_user->id,
          'action' => 'R', //-- P = preview, A = approve, R = reject
          'date_upd' => now()
        );

        $this->credit_approval_model->add_log($log);
      }

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }
    else
    {
      $sc = FALSE;
      set_error('notfound');
    }

    $this->_response($sc);
  }

  public function get_approver()
  {
    $amount = $this->input->post('diff');
    $apv = $this->credit_approver_model->get_active_by_amount($amount);
    $list = [];

    if (!empty($apv))
    {
      foreach ($apv as $rs)
      {
        $list[] = array(
          'id' => $rs->user_id,
          'uname' => $rs->uname,
          'emp_name' => $rs->emp_name,
          'amount' => number($rs->amount, 2)
        );
      }
    }
    else
    {
      $list[] = ['nodata' => 'No authorizer'];
    }


    echo json_encode($list);
  }


  public function can_approve($user_id, $credit_diff)
  {
    $apv = $this->credit_approver_model->get_active_by_user_id($user_id);

    if (!empty($apv))
    {
      return $credit_diff <= $apv->amount;
    }

    return FALSE;
  }


  public function get_overdue_amount($CardCode, $CustCode = NULL)
  {
    $this->load->model('orders_model');
    return $this->orders_model->get_overdue_amount($CardCode, $CustCode);
  }


  public function must_approve($con_id, $docTotal, $priceEdit = FALSE, $priceList = NULL)
  {
    $this->load->model('approve_rule_model');
    $mapl = [];

    if (! empty($priceList))
    {
      $mustApprovePriceList = getConfig('MUST_APPROVE_PRICE_LIST');

      if (! empty($mustApprovePriceList))
      {
        $map = explode(',', $mustApprovePriceList);

        if (count($map) > 0)
        {
          foreach ($map as $pid)
          {
            $pid = trim($pid);
            $mapl[$pid] = $pid;
          }
        }

        if (! empty($mapl[$priceList]))
        {
          return TRUE;
        }
      }
    }

    $rule = empty($con_id) ? NULL : $this->approve_rule_model->get_exception_rule($con_id, $docTotal, $priceEdit);
    //---- order must approve by default
    //--- if exception rule exists order no need to approve
    if (! empty($rule))
    {
      return FALSE;
    }

    return TRUE;
  }


  public function send_request_payment()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $message = get_null(trim($this->input->post('message')));
    $doc = $this->orders_model->get($code);

    if(!empty($doc))
    {
      $this->load->model('payment_request_model');
      $id = NULL;
      $req = $this->payment_request_model->get($code);

      $this->db->trans_begin();

      if(empty($req))
      {
        $arr = array(
          'code' => $doc->code,
          'CardCode' => $doc->CardCode,
          'CardName' => $doc->CardName,
          'CustCode' => $doc->CustCode,
          'DocTotal' => $doc->DocTotal,
          'credit_diff' => $doc->credit_diff,
          'message' => $message,
          'uname' => $doc->uname,
          'add_by' => $this->_user->id
        );

        $id = $this->payment_request_model->add($arr);

        if($id)
        {
          $arr = array(
            'credit_case_id' => $id,
            'request_date' => now(),
            'credit_case_status' => 'O'
          );

          if(! $this->orders_model->update($code, $arr))
          {
            $sc = FALSE;
            set_error('Failed to update order request payment date');
          }
        }
        else 
        {
          $sc = FALSE;
          set_error('Failed to create payment request');
        }
      }
      else 
      {
        $id = $req->id;

        $arr = array(
          'reply_status' => 'N',
          'message' => $message,
          'request_date' => now(),
          'update_by' => $this->_user->id
        );

        if(! $this->payment_request_model->update_by_id($id, $arr))
        {
          $sc = FALSE;
          set_error('Failed to update payment request');
        }
      }          

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else 
      {
        $this->db->trans_rollback();
      }      
    }
    else
    {
      $sc = FALSE;
      set_error('Order not found');
    }

    $this->_response($sc);
  }


  public function clear_filter()
  {
    $filter = array('capv_code', 'capv_customer', 'capv_credit_approval', 'capv_status', 'capv_user', 'capv_overdue', 'capv_from_date', 'capv_to_date');
    return clear_filter($filter);
  }

} // endclass 
