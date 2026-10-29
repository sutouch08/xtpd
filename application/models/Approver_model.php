<?php
class Approver_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('approver');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_user_id($user_id)
  {
    $rs = $this->db->where('user_id', $user_id)->get('approver');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_uname($uname)
  {
    $rs = $this->db->where('uname', $uname)->get('approver');

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
      if($this->db->insert('approver', $ds))
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
      return $this->db->where('id', $id)->update('approver', $ds);
    }

    return FALSE;
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('approver');
  }


  public function drop_approver_condition($user_id)
  {
    return $this->db->where('user_id', $user_id)->delete('approver_condition');
  }


  public function is_exists($user_id)
  {
    $rs = $this->db->where('user_id', $user_id)->count_all_results('approver');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
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

    return $this->db->count_all_results('approver');
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

    $rs = $this->db->order_by('date_add', 'DESC')->limit($limit, $offset)->get('approver');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_active_approver()
  {
    $rs = $this->db->where('status', 1)->get('approver');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_approver()
  {
    $rs = $this->db->get('approver');

    if($rs->num_rows(0 > 0))
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_condition_sign($condition)
  {
    $sign = "";

    switch($condition)
    {
      case "Less Than" :
        $sign = "<";
        break;
      case "Less or Equal" :
        $sign = "<=";
        break;
      case "Greater Than" :
        $sign = ">";
        break;
      case "Greate or Equal" :
        $sign = ">=";
        break;
      default :
        $sign = "";
        break;
    }

    return $sign;
  }

} //--- end class

 ?>
