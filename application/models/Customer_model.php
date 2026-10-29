<?php
class Customer_model extends CI_Model
{
  private $tb = 'customer';  

  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->ms->where('CardCode', $code)->get('OCRD');

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_by_code($code)
  {
    $rs = $this->db->where('CardCode', $code)->get($this->tb);

    if( $rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_id($code)
  {
    $rs = $this->db->select('id')->where('CardCode', $code)->get($this->tb);

    if ($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }

  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_sap_data($last_sync = NULL)
  {
    $this->ms
      ->select('c.DocEntry, c.CardCode, c.CardName, c.GroupCode, c.GroupNum')
      ->select('c.ListNum, c.SlpCode, c.ECVatGroup, c.CreditLine, c.validFor, c.CreateDate, c.UpdateDate')
      ->select('c.Currency, c.U_TPD_DrugCon, c.U_TPD_RA_DrugType')
      ->select('c.U_TPD_BI_SalesTeam, c.U_TPD_BI_AreaName')
      ->select('c.U_SALE_PERSON, c.U_TPD_BI_Department, c.U_TPD_CUST_HCode')
      ->select('c.U_TPD_FirstCus, c.U_TPD_CusRegis AS registerDate')
      ->select('v.Rate')
      ->from('OCRD AS c')
      ->join('OVTG AS v', 'c.ECVatGroup = v.Code', 'left')
      ->where('c.CardType', 'C');

      if( ! is_null($last_sync))
      {
        $this->ms
          ->group_start()
          ->where('c.CreateDate >=', from_date($last_sync))
          ->or_where('c.UpdateDate >=', from_date($last_sync))
          ->group_end();
      }

    $rs = $this->ms->get();      

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_last_sync_date()
  {
    $rs = $this->db
      ->select('last_sync')
      ->where('code', 'CUSTOMER')      
      ->get('last_sync');

    if ($rs->num_rows() === 1)
    {
      return $rs->row()->last_sync;
    }

    return NULL;
  }
  
  public function get_credit_balance_by_hCode($hCode)
  {
    $rs = $this->ms
      ->select_sum('CreditLine')
      ->select_sum('Balance')
      ->select_sum('DNotesBal')
      ->select_sum('OrdersBal')
      ->where('U_TPD_CUST_HCode', $hCode)
      ->group_by('U_TPD_CUST_HCode')
      ->get('OCRD');

    if ($rs->num_rows() === 1)
    {
      $balance = $rs->row()->CreditLine - ($rs->row()->Balance + $rs->row()->DNotesBal + $rs->row()->OrdersBal);      
      return $balance;
    }

    return 0.00;
  }


  public function get_credit_balance($cardCode)
  {
    $rs = $this->ms
      ->select('CreditLine, Balance, DNotesBal, OrdersBal')
      ->where('CardCode', $cardCode)
      ->get('OCRD');      

    if ($rs->num_rows() === 1)
    {
      $balance = $rs->row()->CreditLine - ($rs->row()->Balance + $rs->row()->DNotesBal + $rs->row()->OrdersBal);
      return $balance;
    }

    return 0.00;
  }


  public function get_credit_details($cardCode)
  {
    $rs = $this->ms
      ->select('CreditLine, Balance, DNotesBal, OrdersBal')
      ->where('CardCode', $cardCode)
      ->get('OCRD');

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_credit_details_by_hCode($hCode)
  {
    $rs = $this->ms
      ->select_sum('CreditLine')
      ->select_sum('Balance')
      ->select_sum('DNotesBal')
      ->select_sum('OrdersBal')
      ->where('U_TPD_CUST_HCode', $hCode)
      ->group_by('U_TPD_CUST_HCode')
      ->get('OCRD');

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function update_last_sync_date()
  {
    return $this->db->where('code', 'CUSTOMER')->update('last_sync', array('last_sync' => now()));
  }

  
  public function is_exists_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if ($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }

  public function get_all()
  {
    $rs = $this->db
    ->select('id, CardCode, CardName')
    ->order_by('CardCode', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_in_array()
  {
    $rs = $this->db
    ->select('id, CardCode, CardName')
    ->order_by('CardCode', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      $arr = array();
      foreach($rs->result() as $row)
      {
        $arr[$row->id] = $row;
      }

      return $arr;
    }

    return NULL;
  }


  public function get_user_customer_list($area_id, $type = 'V')
  {
    $this->db
    ->select('CardCode, CardName, GroupNum, SlpCode, ECVatGroup, Currency, U_TPD_DrugCon AS isControl')
    ->select('U_TPD_RA_DrugType AS customer_type, U_TPD_BI_SalesTeam AS saleTeam, U_TPD_BI_AreaName AS areaId')
    ->select('U_SALE_PERSON AS salePerson, U_TPD_BI_Department AS department, vatRate AS Rate, CustCode, isRegular')
    ->where('validFor', 'Y')
    ->where('SlpCode >', 0)
    ->where('U_TPD_BI_AreaName IS NOT NULL', NULL, FALSE)
    ->where('U_TPD_BI_AreaName', $area_id)
    ->where('U_SALE_PERSON IS NOT NULL', NULL, FALSE);

    if($type !== 'all')
    {
      $this->db->where('custType', $type);
    }

    $rs = $this->db
    ->order_by('CardCode', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_user_customer_list($type = 'V')
  {
    $this->db
    ->select('CardCode, CardName, GroupNum, SlpCode, ECVatGroup, Currency, U_TPD_DrugCon AS isControl')
    ->select('U_TPD_RA_DrugType AS customer_type, U_TPD_BI_SalesTeam AS saleTeam, U_TPD_BI_AreaName AS areaId')
    ->select('U_SALE_PERSON AS salePerson, U_TPD_BI_Department AS department, vatRate AS Rate, CustCode, isRegular')
    ->where('validFor', 'Y')
    ->where('SlpCode >', 0)
    ->where('U_TPD_BI_AreaName IS NOT NULL', NULL, FALSE)
    ->where('U_SALE_PERSON IS NOT NULL', NULL, FALSE);

    if($type !== 'all')
    {
      $this->db->where('custType', $type);
    }

    $rs = $this->db
    ->order_by('CardCode', 'ASC')
    ->get($this->tb);    

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
  

  public function count_rows(array $ds = array())
  {
    if(isset($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('CardCode', $ds['code']);
    }

    if(isset($ds['name']) && $ds['name'] != '')
    {
      $this->db->like('CardName', $ds['name']);
    }

    if (isset($ds['cust_code']) && $ds['cust_code'] != '')
    {
      $this->db->like('CustCode', $ds['cust_code']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('validFor', $ds['active']);
    }

    if (isset($ds['department']) && $ds['department'] != 'all')
    {
      $this->db->where('U_TPD_BI_Department', $ds['department']);
    }

    if (isset($ds['area']) && $ds['area'] != 'all')
    {
      $this->db->where('U_TPD_BI_AreaName', $ds['area']);
    }

    if (isset($ds['sales_team']) && $ds['sales_team'] != 'all')
    {
      $this->db->where('U_TPD_BI_SalesTeam', $ds['sales_team']);
    }

    if (isset($ds['sales_person']) && $ds['sales_person'] != 'all')
    {
      $this->db->where('SlpCode', $ds['sales_person']);
    }

    if (isset($ds['isRegular']) && $ds['isRegular'] != 'all')
    {
      $this->db->where('isRegular', $ds['isRegular']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(isset($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('CardCode', $ds['code']);
    }

    if(isset($ds['name']) && $ds['name'] != '')
    {
      $this->db->like('CardName', $ds['name']);
    }

    if(isset($ds['cust_code']) && $ds['cust_code'] != '')
    {
      $this->db->like('CustCode', $ds['cust_code']);
    }

    if(isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('validFor', $ds['active']);
    }

    if(isset($ds['department']) && $ds['department'] != 'all')
    {
      $this->db->where('U_TPD_BI_Department', $ds['department']);
    }

    if(isset($ds['area']) && $ds['area'] != 'all')
    {
      $this->db->where('U_TPD_BI_AreaName', $ds['area']);
    }

    if(isset($ds['sales_team']) && $ds['sales_team'] != 'all')
    {
      $this->db->where('U_TPD_BI_SalesTeam', $ds['sales_team']);
    }

    if(isset($ds['sales_person']) && $ds['sales_person'] != 'all')
    {
      $this->db->where('SlpCode', $ds['sales_person']);
    }

    if(isset($ds['isRegular']) && $ds['isRegular'] != 'all')
    {
      $this->db->where('isRegular', $ds['isRegular']);
    }

    $rs = $this->db
    ->order_by($ds['order_by'], $ds['sort_by'])
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_address_ship_to($CardCode, $address = '00000')
  {
    $rs = $this->ms
      ->select('CRD1.*, OCRY.Name AS countryName')
      ->from('CRD1')
      ->join('OCRY', 'CRD1.Country = OCRY.Code', 'left')
      ->where('CRD1.AdresType', 'S')
      ->where('CRD1.CardCode', $CardCode)
      ->where('CRD1.Address', $address)
      ->get();

    if ($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_address_ship_to_code($CardCode)
  {
    $rs = $this->ms
      ->select('Address')
      ->where('AdresType', 'S')
      ->where('CardCode', $CardCode)
      ->order_by('CardCode', 'ASC')
      ->order_by('Address', 'ASC')
      ->get('CRD1');

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_address_bill_to($CardCode, $address = '00000')
  {
    $rs = $this->ms
      ->select('CRD1.*, OCRY.Name AS countryName')
      ->from('CRD1')
      ->join('OCRY', 'CRD1.Country = OCRY.Code', 'left')
      ->where('CRD1.AdresType', 'B')
      ->where('CRD1.CardCode', $CardCode)
      ->where('CRD1.Address', $address)
      ->get();

    if ($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_address_bill_to_code($CardCode)
  {
    $rs = $this->ms
      ->select('Address')
      ->where('AdresType', 'B')
      ->where('CardCode', $CardCode)
      ->get('CRD1');

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sale_name_by_customer($CardCode)
  {
    $rs = $this->ms
      ->select('OSLP.SlpName')
      ->from('OCRD')
      ->join('OSLP', 'OCRD.SlpCode = OSLP.SlpCode', 'left')
      ->where('OCRD.CardCode', $CardCode)
      ->get();

    if ($rs->num_rows() === 1)
    {
      return $rs->row()->SlpName;
    }

    return NULL;
  }
} //--- end model
