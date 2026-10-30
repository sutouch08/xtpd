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
    return $rs->num_rows() === 1 ? $rs->row() : NULL;
  }


  public function get_id($name)
  {
    $rs = $this->db->where('name', $name)->get($this->table);
    return $rs->num_rows() === 1 ? $rs->row()->id : NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get($this->table);
    return $rs->num_rows() === 1 ? $rs->row()->name : NULL;
  }


  public function get_all()
  {
    $rs = $this->db->get($this->table);
    return $rs->num_rows() > 0 ? $rs->result() : NULL;
  }


  public function add(array $ds = array())
  {
    return !empty($ds) ? ($this->db->insert($this->table, $ds) ? $this->db->insert_id() : FALSE) : FALSE;
  }


  public function update($id, array $ds = array())
  {
    return !empty($ds) ? ($this->db->where('id', $id)->update($this->table, $ds) ? TRUE : FALSE) : FALSE;
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

    return $rs->num_rows() > 0 ? $rs->result() : NULL;
  }


  public function is_exists_name($name, $id = NULL)
  {    
    if($id !== NULL) $this->db->where('id !=', $id);  
    return $this->db->where('name', $name)->count_all_results($this->table) > 0;
  }
}