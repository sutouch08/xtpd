<?php
class Item_step_price_model extends CI_Model
{
  private $tb = "item_step_price";
  private $td = "item_step_price_detail";

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


  public function get_by_code($ItemCode)
  {
    $rs = $this->db->where('ItemCode', $ItemCode)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($id)
  {
    $rs = $this->db
    ->where('step_id', $id)
    ->order_by('position', 'ASC')
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_by_code($ItemCode)
  {
    $rs = $this->db
    ->where('ItemCode', $ItemCode)
    ->order_by('position', 'ASC')
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_details_by_id($id)
  {
    $rs = $this->db
    ->where('step_id', $id)
    ->order_by('position', 'ASC')
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
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
    if(!empty($ds))
    {
      if($this->db->insert($this->td, $ds))
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


  public function update_detail($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
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


  public function delete_details($step_id)
  {
    return $this->db->where('step_id', $step_id)->delete($this->td);
  }


  public function count_step($step_id)
  {
    $count = $this->db->where('step_id', $step_id)->count_all_results($this->td);

    return $count;
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if( ! empty($ds['item']))
    {
      $this->db
      ->group_start()
      ->like('ItemCode', $ds['item'])
      ->or_like('ItemName', $ds['item'])
      ->group_end();
    }

    if(isset($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('active', $ds['status']);
    }

    $this->db->limit($limit, $offset);

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['item']))
    {
      $this->db
      ->group_start()
      ->like('ItemCode', $ds['item'])
      ->or_like('ItemName', $ds['item'])
      ->group_end();
    }

    if(isset($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('active', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);
  }


}
?>
