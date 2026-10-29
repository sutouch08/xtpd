<?php
class Credit_approver_model extends CI_Model
{
  private $tb = "credit_approver";

  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_user_id($user_id)
  {
    $rs = $this->db->where('user_id', $user_id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_active_by_user_id($user_id)
  {
    $rs = $this->db->where('user_id', $user_id)->where('status', 1)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_active_by_amount($amount)
  {
    $rs = $this->db->where('status', 1)->where('amount >=', $amount)->order_by('amount', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_by_uname($uname)
  {
    $rs = $this->db->where('uname', $uname)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
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


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function is_exists($user_id)
  {
    return $this->db->where('user_id', $user_id)->count_all_results($this->tb) > 0;    
  }


  public function count_rows(array $ds = array())
  {
    if($ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if($ds['emp_name'] !="")
    {
      $this->db->like('emp_name', $ds['emp_name']);
    }
    
    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if ($ds['can_approve'] != 'all')
    {
      $this->db->where('can_approve', $ds['can_approve']);
    }

    if ($ds['can_review'] != 'all')
    {
      $this->db->where('can_review', $ds['can_review']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if($ds['uname'] != "")
    {
      $this->db->like('uname', $ds['uname']);
    }

    if($ds['emp_name'] !="")
    {
      $this->db->like('emp_name', $ds['emp_name']);
    }    

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if($ds['can_approve'] != 'all')
    {
      $this->db->where('can_approve', $ds['can_approve']);
    }

    if($ds['can_review'] != 'all')
    {
      $this->db->where('can_review', $ds['can_review']);
    }

    $rs = $this->db->order_by('date_add', 'DESC')->limit($limit, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_active_approver()
  {
    $rs = $this->db->where('status', 1)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_approver()
  {
    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
  
} //--- end class

