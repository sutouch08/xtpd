<?php
class Sale_team_model extends CI_Model
{
  private $table = 'sale_team';

  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->table);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_all_team()
  {
    $rs = $this->db->get($this->table);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_team_customer_group($id)
  {
    $rs = $this->db->where('team_id', $id)->get('team_customer_group');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_team_approver($id)
  {
    $rs = $this->db
    ->select('ta.*')
    ->select('ap.uname, ap.emp_name, ap.amount')
    ->from('team_approver AS ta')
    ->join('approver AS ap', 'ta.user_id = ap.user_id', 'left')
    ->where('ta.team_id', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function add_team_approver(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('team_approver', $ds);
    }

    return FALSE;
  }



  public function drop_team_approver($team_id)
  {
    return $this->db->where('team_id', $team_id)->delete('team_approver');
  }


  public function get_approver($team_id, $user_id)
  {
    $rs = $this->db
    ->select('ta.*, ap.amount')
    ->from('team_approver AS ta')
    ->join('approver AS ap', 'ta.user_id = ap.user_id', 'left')
    ->where('ta.team_id', $team_id)
    ->where('ta.user_id', $user_id)
    ->where('ap.status', 1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->db->insert($this->table, $ds);

      if($rs)
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_team_customer_group(array $ds = array())
  {
    return $this->db->insert('team_customer_group', $ds);
  }



  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->table, $ds);
    }

    return FALSE;
  }



  public function drop_team_customer_group($id)
  {
    return $this->db->where('team_id', $id)->delete('team_customer_group');
  }



  public function drop_user_team($id)
  {
    return $this->db->where('team_id', $id)->delete('user_team');
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('sale_team');
  }


  public function count_rows(array $ds = array())
  {
    $this->db->where('id >=', 0, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    return $this->db->count_all_results($this->table);
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    $this->db->where('id >=', 0, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    $this->db->order_by('id', 'DESC')->limit($limit, $offset);

    $rs = $this->db->get($this->table);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_members($id)
  {
    return $this->db->where('team_id', $id)->count_all_results('user_team');
  }



  public function get_members($team_id)
  {
    $rs = $this->db
    ->select('user.uname, user.emp_name, user_team.user_role')
    ->from('user_team')
    ->join('user', 'user_team.user_id = user.id', 'left')
    ->where('user_team.team_id', $team_id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_name($name, $id = NULL)
  {
    $this->db->select('id')->where('name', $name);

    if($id !== NULL)
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->get($this->table);

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

} //--- end class
 ?>
