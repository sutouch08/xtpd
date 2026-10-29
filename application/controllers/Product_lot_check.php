<?php
class Product_lot_check extends PS_Controller
{
  public $menu_code = 'PDLOTCHK';
  public $menu_group_code = 'ORDER';
  public $title = 'ตรวจสอบ Lot. สินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('product_lot_check_model');
    $this->load->model('item_model');
    $this->load->model('price_list_model');
    $this->load->model('price_list_type_model');
    $this->load->model('special_price_list_model');
  }

  public function index()
  {
    $html = "";
    $PL = [];
    $priceList = $this->user_model->get_user_price_list($this->_user->id);

    if (! empty($priceList))
    {
      foreach ($priceList as $pl)
      {
        $PL['Standard'][] = (object) array(
          'id' => $pl->id,
          'spid' => 0,
          'name' => $pl->name
        );
      }
    }

    $tp = $this->price_list_type_model->get_all();

    if (! empty($tp))
    {
      foreach ($tp as $t)
      {
        $tpd = $this->special_price_list_model->get_active_by_type($t->id);

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

    $ds = array(
      'items' => NULL,
      'priceList' => $html
    );

    $this->load->view('product_lot_check/product_lot_check', $ds);
  }


  public function get_item_template()
  {
    $sc = TRUE;
    $ds = '<option value="">Select Items</option>';
    $PriceList = $this->input->post('priceList');   
    $spid = $this->input->post('spid');
    

    if (! is_null($PriceList))
    {
      if($PriceList != 'x')
      {
        $items = $this->item_model->get_items_by_price_list($PriceList);

        if (! empty($items))
        {
          $ds = '<option value="">Select items (' . count($items) . ')</option>';

          foreach ($items as $rs)
          {
            $ds .= '<option value="' . $rs->code . '" data-name="' . $rs->name . '">' . $rs->name . '</option>';
          }
        }
        else
        {
          $ds = '<option value="">No Items</option>';
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
            $ds = '<option value="">Select items (' . $rs->num_rows() . ')</option>';

            foreach ($rs->result() as $rd)
            {
              $ds .= '<option value="' . $rd->ItemCode . '" data-name="' . $rd->ItemName . '">' . $rd->ItemName . '</option>';
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
    $itemCode = $this->input->post('item');
    $ds = [];

    if (! empty($itemCode))
    {
      $item = $this->item_model->get_item($itemCode);

      if( ! empty($item))
      {
        $limit = getConfig('LIMIT_LOT_CHECK');
        $data = $this->product_lot_check_model->get_data($itemCode, $limit);

        if( ! empty($data))
        {
          $no = 1;

          foreach($data as $rs)
          {
            $ds[] = array(
              'no' => $no,
              'description' => $rs->ItemName,
              'whsCode' => $rs->WhsCode,
              'lotNo' => $rs->BatchNum,
              'expDate' => thai_date($rs->ExpDate),
              'mfdDate' => thai_date($rs->PrdDate),
              'qty' => number($rs->qty, 2)
            );

            $no++;
          }          
        }
        else
        {
          $ds[] = array('nodata' => 'No data found');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'Invalid item code';
      }      
    }
    else
    {
      $sc = FALSE;
      $this->error = get_error_message('required');
    }

    echo json_encode(array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? '' : $this->error,
      'data' => ! empty($ds) ? $ds : NULL
    ));
  }


} //--- end class
	