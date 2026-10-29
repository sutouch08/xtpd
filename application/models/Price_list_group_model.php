<?php
class Price_list_group_model extends CI_Model
{
  private $tb = 'price_list_group';
  private $td = 'price_list_group_detail';

  public function __construct()
  {
    parent::__construct();
  }

  
  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_all($active = TRUE)
  {
    if ($active === TRUE)
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->get($this->tb);

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($group_id)    
  {
    $rs = $this->db->select('pgd.*, pl.name AS price_list_name')
    ->from($this->td . ' AS pgd')
    ->join('price_list AS pl', 'pl.id = pgd.price_list_id', 'left')
    ->where('pgd.group_id', $group_id)
    ->get();

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if ($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if (!empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if (!empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_details(array $ds = array())
  {
    if (!empty($ds))
    {
      return $this->db->insert_batch($this->td, $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if (!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }

  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }

  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($group_id)  
  {
    return $this->db->where('group_id', $group_id)->delete($this->td);
  }
  

  public function count_rows(array $ds = array())
  {    
    if (!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if (isset($ds['active']) && $ds['active'] !== 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if (!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if (isset($ds['active']) && $ds['active'] !== 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $this->db      
      ->order_by('name', 'ASC')
      ->limit($limit, $offset);

    $rs = $this->db->get($this->tb);

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists($id)
  {
    return $this->db->where('id', $id)->count_all_results($this->tb) > 0;
  }


  public function is_exists_name($name, $id = NULL)
  {
    if (!empty($id))
    {
      $this->db->where('id !=', $id);
    }

    return $this->db->where('name', $name)->count_all_results($this->tb) > 0;
  }   

} //--- end class
