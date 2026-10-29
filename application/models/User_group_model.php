<?php
class User_group_model extends CI_Model
{
  private $table = 'user_group';

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


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->table, $ds);
    }

    return FALSE;
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
    return $this->db->where('ugroup_id', $id)->count_all_results('user');
  }


  public function is_exists_name($name, $id = NULL)
  {
    $this->db->select('id')->where('name', $name);

    if(!empty($id))
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
