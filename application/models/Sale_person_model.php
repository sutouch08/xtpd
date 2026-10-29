<?php
class Sale_person_model extends CI_Model
{
  private $table = 'sale_person';

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



  public function get_id($name)
  {
    $rs = $this->db->where('name', $name)->get($this->table);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_all()
  {
    $rs = $this->db->get($this->table);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
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



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->table);
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
