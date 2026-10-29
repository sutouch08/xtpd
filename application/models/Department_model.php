<?php
class Department_model extends CI_Model
{
  private $tb = 'department';
  private $tableID = 'OCRD';
  private $fieldID = 43;

  public function __construct()
  {
    parent::__construct();
  }


  public function get_sap_data()
  {
    $rs = $this->ms
    ->select('FldValue AS id, Descr AS name')
    ->where('TableID', $this->tableID)
    ->where('FieldID', $this->fieldID)
    ->order_by('IndexID', 'ASC')
    ->get('UFD1');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
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


  public function get_all()
  {
    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_id($name)
  {
    $rs = $this->db->where('name', $name)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
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


  public function count_rows(array $ds = array())
  {
    $this->db->where('id >=', 0, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    $this->db->where('id >=', 0, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    $this->db->order_by('id', 'ASC')->limit($limit, $offset);

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_id($id)
  {
    $count = $this->db->where('id', $id)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }

} //--- end class
 ?>
