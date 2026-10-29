<?php
class Price_list_item_check extends PS_Controller
{
  public $menu_code = 'PDSPLCHK';
  public $menu_group_code = 'ORDER';
  public $title = 'ตรวจสอบรายการสินค้าตาม Price List';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('price_list_item_check_model');    
    $this->load->model('price_list_type_model');
    $this->load->model('special_price_list_model');
    $this->load->model('customer_model');
    $this->load->model('customer_group_model');
    $this->load->model('item_model');
    $this->load->helper('price_list');
    $this->load->helper('special_price_list');
  }

  public function index()
  {
    $ds = array(
      'priceList' => NULL,
      'customer' => NULL
    );

    if ($this->isAdmin)
    {
      $ds['customer'] = $this->customer_model->get_all_user_customer_list("all");
    }
    else
    {
      $ds['customer'] = $this->customer_model->get_user_customer_list($this->_user->area_id, "all");
    }

    $PL = [];    

    $type = $this->price_list_type_model->get_all();

    if (! empty($type))
    {
    	foreach ($type as $tp)
    	{
    		$tpd = $this->special_price_list_model->get_active_by_type($tp->id);

    		if (! empty($tpd))
    		{
    			foreach ($tpd as $sp)
    			{
    				$PL[$tp->name][] = (object) array(
    					'id' => 'x',
    					'spid' => $sp->id,
    					'name' => $sp->name
    				);
    			}
    		}
    	}
    }

    $ds['priceList'] = $PL;
    //$data['priceList'] = $this->price_list_item_check_model->get_user_price_list($this->_user->id);
    $this->load->view('price_list_item_check/price_list_check', $ds);
  }

  public function get_price_list_by_type()
  {
    $sc = TRUE;
    $cardCode = $this->input->post('CardCode');
    $type = $this->input->post('type');
    $html = "<option value=\"\">Select</option>";
    $PL = [];
    $groupIds = [0];
    $tp = [];   

    if( ! empty($cardCode))
    {
      $customerGroups = $this->customer_group_model->get_by_customer($cardCode);

      if (! empty($customerGroups))
      {
        foreach ($customerGroups as $cg)
        {
          $groupIds[] = $cg->group_id;
        }
      }
    }
    
    $tp = [];
    if($type === 'all')
    {
      $tp = $this->price_list_type_model->get_all();
    }
    else
    {
      $tp[] = $this->price_list_type_model->get($type);
    }

    if( ! empty($tp))
    {
      foreach($tp as $t)
      {
        $tpd = [];

        if( ! empty($cardCode))
        {
          $tpd = $this->special_price_list_model->get_active_customer_list_by_type($t->id, $groupIds);
        }
        else
        {
          $tpd = $this->special_price_list_model->get_active_by_type($t->id);
        }

        if (! empty($tpd))
        {
          foreach ($tpd as $sp)
          {
            $PL[$t->name][] = (object) array(
              'id' => 'x',
              'spid' => $sp->id,
              'name' => $sp->name
            );
          }
        }
      }
    }   

    if (! empty($PL))
    {
      foreach ($PL as $label => $list)
      {
        $html .= '<optgroup label="' . $label . '">';

        foreach ($list as $pl)
        {
          $html .= '<option value="' . $pl->id . '" data-spid="' . $pl->spid . '">' . $pl->name . '</option>';
        }

        $html .= '</optgroup>';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Price list not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'priceList' => $html,
    );

    echo json_encode($arr);
  }


  public function get_item_template()
  {
    $sc = TRUE;

    $ds = '<option value="0">Select</option>';

    $PriceList = $this->input->post('priceList');
    $customer_type = $this->input->post('customer_type');
    $spid = $this->input->post('spid');

    $item_type = empty($customer_type) ? [] : item_type_array($customer_type); //--tools_helper  return item_type as array like ['01', '02', '09'];

    if (! is_null($PriceList))
    {
      if ($PriceList != 'x')
      {
        $items = $this->item_model->get_items_by_price_list($PriceList, $item_type);

        if (! empty($items))
        {
          $ds = '<option value="0">Select items (' . count($items) . ')</option>';
          foreach ($items as $rs)
          {
            $ds .= '<option value="' . $rs->code . '" data-name="' . $rs->name . '">' . $rs->name . '</option>';
          }
        }
        else
        {
          $ds = '<option value="0">No Items</option>';
        }
      }
      else
      {
        if ($spid > 0)
        {
          $rs = $this->db
            ->select('spi.ItemCode, spi.ItemName')
            ->from('special_price_item AS spi')
            ->join('special_price_list AS spl', 'spi.price_list_id = spl.id', 'left')
            ->where('spi.price_list_id', $spid)
            ->where('spi.active', 1)
            ->where('spl.active', 1)
            ->order_by('spi.ItemName', 'ASC')
            ->get();

          if ($rs->num_rows() > 0)
          {
            $items = array();

            foreach ($rs->result() as $rd)
            {
              $items[] = $rd->ItemCode;
            }

            if (! empty($items))
            {
              $this->ms
                ->select('ItemCode, ItemName, U_TPD_DrugType')
                ->where('SellItem', 'Y')
                ->where('validFor', 'Y')
                ->where_in('ItemCode', $items);
              
              if( ! empty($item_type))
              {
                $this->ms->where_in('U_TPD_DrugType', $item_type);
              }

              $ro = $this->ms->order_by('ItemName', 'ASC')->get('OITM');

              if ($ro->num_rows() > 0)
              {
                $ds = '<option value="0">Select items (' . $ro->num_rows() . ')</option>';
                
                foreach ($ro->result() as $rd)
                {
                  $ds .= '<option value="' . $rd->ItemCode . '" data-name="' . $rd->ItemName . '">' . $rd->ItemName . '</option>';
                }
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid special price list";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'template' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_data()
  {
    $sc = TRUE;
    $ds = array();
    $code = trim($this->input->post('item'));
    $PriceList = $this->input->post('priceList');    
    $spid = $this->input->post('spid');
    

    if (! empty($PriceList) && ! empty($code))
    {      
      $item = $this->item_model->get_item($code);

      if (! empty($item))
      {
        if ($spid > 0)
        {
          $step = $this->special_price_list_model->get_details_by_item($spid, $code);
          $no = 1;

          if (! empty($step))
          {
            foreach ($step as $rs)
            {             
              $ds[] = array(
                'no' => $no,                
                'description' => $rs->name,
                'Price' => number($rs->SellPrice, 2),
                'Qty' => $rs->Qty,
                'freeQty' => $rs->freeQty
              );

              $no++;
            }
          }
        }
        else
        {
          $ds[] = array('nodata' => 'ไม่พบ step ราคา');
        }              
      }
      else
      {
        $ds[] = array('nodata' => 'ไม่พบรายการสินค้า');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }

}
