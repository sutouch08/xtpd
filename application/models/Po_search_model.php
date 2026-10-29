<?php 
class Po_search_model extends CI_Model
{

  private $tb = "orders";
  
  public function __construct()
  {
    parent::__construct();
  }

  public function count_rows(array $ds = array())
  {
    $this->db    
    ->where('NumAtCard IS NOT NULL', NULL, FALSE)
    ->where('NumAtCard !=', '')
    ->where('InvoiceNo IS NOT NULL', NULL, FALSE)
    ->where('InvoiceNo !=', '');

    if( ! empty($ds['inv_code']))
    {
      $this->db->like('InvoiceNo', $ds['inv_code']);
    }

    if( ! empty($ds['po']))
    {
      $this->db->like('NumAtCard', $ds['po']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('DocNum', $ds['so_code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['customer']);
      $this->db->or_like('CardName', $ds['customer']);
      $this->db->group_end();
    }    

    if($ds['has_file'] !== 'all')
    {
      $this->db->where('has_file', $ds['has_file']);
    }

    if( ! empty($ds['fromDate']))
    {
      $this->db->where('InvoiceDate >=', from_date($ds['fromDate']));
    }

    if( ! empty($ds['toDate']))
    {
      $this->db->where('InvoiceDate <=', to_date($ds['toDate']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('code AS webCode, DocNum AS soCode, InvoiceNo, InvoiceDate, NumAtCard AS poCode, CardCode, CardName, user_id, uname, has_file, file_name, file_type')
    ->where('NumAtCard IS NOT NULL', NULL, FALSE)
    ->where('NumAtCard !=', '')
    ->where('InvoiceNo IS NOT NULL', NULL, FALSE)
    ->where('InvoiceNo !=', '');

    if( ! empty($ds['inv_code']))
    {
      $this->db->like('InvoiceNo', $ds['inv_code']);
    }      

    if( ! empty($ds['po']))
    {
      $this->db->like('NumAtCard', $ds['po']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('DocNum', $ds['so_code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['customer']);
      $this->db->or_like('CardName', $ds['customer']);
      $this->db->group_end();
    }    

    if($ds['has_file'] !== 'all')
    {
      $this->db->where('has_file', $ds['has_file']);
    }

    if( ! empty($ds['fromDate']))
    {
      $this->db->where('InvoiceDate >=', from_date($ds['fromDate']));
    }

    if( ! empty($ds['toDate']))
    {
      $this->db->where('InvoiceDate <=', to_date($ds['toDate']));
    }

    $this->db->order_by('InvoiceNo', 'DESC');
    $this->db->limit($perpage, $offset);
    $rs = $this->db->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}// end class