<?php
class Order_approval_model extends CI_Model
{
  private $tb = "orders";
  private $td = "order_details"; 

  public function __construct()
  {
    parent::__construct();
  }

  public function get_approver_by_user_id($user_id)
  {
    $rs = $this->db
    ->where('user_id', $user_id)
    ->where('status', 1)
    ->get('approver');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_approver_conditions($user_id)
  {
    $condition = [0];
    $rs = $this->db->where('user_id', $user_id)->get('approver_condition');    

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $row)
      {
        $condition[] = $row->condition_id;
      }
    }

    return $condition;
  }  


  public function count_rows(array $ds = array())
  {
    $this->db
      ->where('status', 0)
      ->where('must_approve', 1)
      ->where('Approved', 'P')
      ->where('credit_issue', 0)     
      ->where_in('condition_id', $ds['conditions'])      
      ->where('DocTotal <=', $ds['max_amount']);

    if( ! empty($ds['min_amount_filter']) && $ds['min_amount_filter'] === 'Y')
    {
      $this->db->where('DocTotal >=', $ds['min_amount']);
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['po']))
    {
      $this->db->like('NumAtCard', $ds['po']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('CardCode', $ds['customer'])
      ->or_like('CardName', $ds['customer'])
      ->group_end();
    }

    if( ! empty($ds['user_id']) && $ds['user_id'] !== 'all')
    {
      $this->db->where('user_id', $ds['user_id']);
    }

    if( ! empty($ds['fromDate']) )
    {
      $this->db->where('date_add >=', from_date($ds['fromDate']));
    }

    if( ! empty($ds['toDate']) )
    {
      $this->db->where('date_add <=', to_date($ds['toDate']));
    }

    if( isset($ds['is_discount_sales']) && $ds['is_discount_sales'] !== 'all')
    {
      $this->db->where('is_discount_sales', $ds['is_discount_sales']);
    }

    if( ! empty($ds['con_id']) && $ds['con_id'] !== 'all')
    {
      $this->db->where('condition_id', $ds['con_id']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->where('status', 0)
    ->where('must_approve', 1)
    ->where('Approved', 'P')
    ->where('credit_issue', 0)    
    ->where_in('condition_id', $ds['conditions'])    
    ->where('DocTotal <=', $ds['max_amount']);

    if( ! empty($ds['min_amount_filter']) && $ds['min_amount_filter'] === 'Y')
    {
      $this->db->where('DocTotal >=', $ds['min_amount']);
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['po']))
    {
      $this->db->like('NumAtCard', $ds['po']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('CardCode', $ds['customer'])
      ->or_like('CardName', $ds['customer'])
      ->group_end();
    }

    if( ! empty($ds['user_id']) && $ds['user_id'] !== 'all')
    {
      $this->db->where('user_id', $ds['user_id']);
    }

    if( ! empty($ds['fromDate']) )
    {
      $this->db->where('date_add >=', from_date($ds['fromDate']));
    }

    if( ! empty($ds['toDate']) )
    {
      $this->db->where('date_add <=', to_date($ds['toDate']));
    }

    if( isset($ds['is_discount_sales']) && $ds['is_discount_sales'] !== 'all')
    {
      $this->db->where('is_discount_sales', $ds['is_discount_sales']);
    }

    if( ! empty($ds['con_id']) && $ds['con_id'] !== 'all')
    {
      $this->db->where('condition_id', $ds['con_id']);
    }

    $rs = $this->db->limit($perpage, $offset)->get($this->tb);
    
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
