<?php
class Customer_status_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get_customer_list()
  {
    $date = date('Y-m-d', strtotime("-3 month"));

    $rs = $this->db
    ->select('id, CardCode, CardName, CreateDate')
    ->where('CardType', 'C')    
    ->where('isRegular', 0)
    ->where('CreateDate <=', $date)
    ->get('customer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_customer_invoice($cardCode)
  {
    return $this->ms->where('CardCode', $cardCode)->where('CANCELED', 'N')->count_all_results('OINV');
  }

  public function get_user_customer_list($area_id)
  {
    $date = date('Y-m-d', strtotime("-3 month"));
    $rs = $this->db
      ->select('id, CardCode, CardName, CreateDate')
      ->where('CardType', 'C')      
      ->where('isRegular', 0)
      ->where('CreateDate <=', $date)
      ->where('U_TPD_BI_AreaName', $area_id)
      ->get('customer');    

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_user_customer_list()
  {
    $date = date('Y-m-d', strtotime("-3 month"));
    $rs = $this->db
      ->select('id, CardCode, CardName, CreateDate')
      ->where('CardType', 'C')      
      ->where('isRegular', 0)
      ->where('CreateDate <=', $date)
      ->get('customer');

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class

