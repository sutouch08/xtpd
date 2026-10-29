<?php
class Approve_rule_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('approve_rule');

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
      if($this->db->insert('approve_rule', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_rule_approver(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('rule_approver', $ds);
    }

    return FALSE;
  }


  public function get_rule_approver($rule_id)
  {
    $rs = $this->db
    ->select('rule_approver.*')
    ->select('user.uname, user.emp_name')
    ->from('rule_approver')
    ->join('user', 'rule_approver.user_id = user.id', 'left')
    ->where('rule_id', $rule_id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_rule_approver($rule_id)
  {
    return $this->db->where('rule_id', $rule_id)->delete('rule_approver');
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('approve_rule', $ds);
    }

    return FALSE;
  }


  public function delete($id)
  {
    //--- delete rule approver
    return $this->db->where('id', $id)->delete('approve_rule');
  }


  public function is_exists($user_id)
  {
    $rs = $this->db->where('user_id', $user_id)->count_all_results('approve_rule');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_approver($rule_id, $user_id)
  {
    if($this->db->where('rule_id', $rule_id)->where('user_id', $user_id)->count_all_results('rule_approver') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function count_rows(array $ds = array())
  {
    if($ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['conditions'] != "all" OR $ds['amount'] != "")
    {
      if($ds['conditions'] != "all" && $ds['amount'] != "")
      {
        $sign = $this->get_condition_sign($ds['conditions']);

        if($sign !== "")
        {
          $this->db->where("amount {$sign}", $ds['amount']);
        }
      }
      else
      {
        if($ds['conditions'] != "all")
        {
          $this->db->where('conditions', $ds['conditions']);
        }
        else
        {
          $this->db->like('amount', $ds['amount']);
        }
      }
    }

    if($ds['sale_team'] != "all")
    {
      $this->db->where('sale_team', $ds['sale_team']);
    }

    if($ds['is_price_list'] != "all")
    {
      $this->db->where('is_price_list', $ds['is_price_list']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    return $this->db->count_all_results('approve_rule');
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    $this->db
    ->select('ar.*, sc.name AS team_name')
    ->from('approve_rule AS ar')
    ->join('sales_team_condition AS sc', 'ar.sale_team = sc.id', 'left');

    if($ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['conditions'] != "all" OR $ds['amount'] != "")
    {
      if($ds['conditions'] != "all" && $ds['amount'] != "")
      {
        $sign = $this->get_condition_sign($ds['conditions']);

        if($sign !== "")
        {
          $this->db->where("amount {$sign}", $ds['amount']);
        }
      }
      else
      {
        if($ds['conditions'] != "all")
        {
          $this->db->where('conditions', $ds['conditions']);
        }
        else
        {
          $this->db->like('amount', $ds['amount']);
        }
      }
    }

    if($ds['sale_team'] != "all")
    {
      $this->db->where('sale_team', $ds['sale_team']);
    }

    if($ds['is_price_list'] != "all")
    {
      $this->db->where('is_price_list', $ds['is_price_list']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($limit, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_exception_rule($team_id, $docTotal, $priceEdit = FALSE)
  {
    $qr  = "SELECT * FROM approve_rule ";
    $qr .= "WHERE sale_team = {$team_id} ";
    $qr .= "AND status = 1 ";

    if($priceEdit === TRUE)
    {
      $qr .= "AND ((conditions = 'Less Than' AND amount > {$docTotal} AND is_price_list = 1)
                  OR (conditions = 'Less or Equal' AND amount >= {$docTotal} AND is_price_list = 1)
                  OR (conditions = 'Greater Than' AND amount < {$docTotal} AND is_price_list = 1)
                  OR (conditions = 'Greater or Equal' AND amount <= {$docTotal} AND is_price_list = 1)
              )";
    }
    else
    {
      $qr .= "AND ((conditions = 'Less Than' AND amount > {$docTotal} AND is_price_list = 0)
                  OR (conditions = 'Less or Equal' AND amount >= {$docTotal} AND is_price_list = 0)
                  OR (conditions = 'Greater Than' AND amount < {$docTotal} AND is_price_list = 0)
                  OR (conditions = 'Greater or Equal' AND amount <= {$docTotal} AND is_price_list = 0)
              )";
    }

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
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
      case "Greater or Equal" :
        $sign = ">=";
        break;
      default :
        $sign = "";
        break;
    }

    return $sign;
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('approve_rule');

    return $rs->row()->code;
  }


} //--- end class

 ?>
