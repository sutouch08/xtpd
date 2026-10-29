<?php
class Special_price_list_model extends CI_Model
{
  private $tb = "special_price_list";
  private $td = "special_price_item";
  private $tr = "special_price_item_detail";
  private $tc = "special_price_list_customer_group";

  public function __construct()
  {
    parent::__construct();
  }

  public function get_all()
  {
    $rs = $this->db->order_by('name', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_active()
  {
    $rs = $this->db->where('active', 1)->order_by('name', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_active_by_type($type_id)
  {
    $rs = $this->db
    ->where('active', 1)
    ->where('type_id', $type_id)
    ->where('start_date <=', date('Y-m-d 00:00:00'))
    ->where('end_date >=', date('Y-m-d 23:59:59'))
    ->order_by('name', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_active_customer_list_by_type($type_id, array $groupIds = array())
  {
    $rs = $this->db
    ->select('sp.id, sp.name')
    ->from($this->tb.' AS sp')
    ->join($this->tc.' AS c', 'sp.id = c.price_list_id', 'left')
    ->where('sp.active', 1)
    ->where('sp.type_id', $type_id)
    ->where('sp.start_date <=', date('Y-m-d 00:00:00'))
    ->where('sp.end_date >=', date('Y-m-d 23:59:59'))
    ->group_start()
    ->where_in('c.customer_group_id', $groupIds)
    ->or_where('sp.all_customer', 1)
    ->group_end()
    ->order_by('sp.name', 'ASC')
    ->get();

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


  //--- special price item
  public function get_item_by_id($id)
  {
    $rs = $this->db
    ->select('i.*, p.name AS price_list_name')
    ->from('special_price_item AS i')
    ->join('special_price_list AS p', 'i.price_list_id = p.id', 'left')
    ->where('i.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  //--- special price item
  public function get_item_by_code($itemCode, $price_list_id)
  {
    $rs = $this->db
    ->where('ItemCode', $itemCode)
    ->where('price_list_id', $price_list_id)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_item_id($itemCode, $price_list_id)
  {
    $rs = $this->db
    ->select('id')
    ->where('ItemCode', $itemCode)
    ->where('price_list_id', $price_list_id)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }

  public function get_item_details($id)
  {
    $rs = $this->db->where('step_id', $id)->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_by_item($price_list_id, $itemCode)
  {
    $rs = $this->db
    ->select('d.*')
    ->from($this->tr.' AS d')
    ->join($this->td.' AS i', 'd.step_id = i.id', 'left')
    ->where('i.price_list_id', $price_list_id)
    ->where('i.ItemCode', $itemCode)
    ->order_by('d.id', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_details($id)
  {
    $rs = $this->db
    ->where('price_list_id', $id)
    ->order_by('ItemName', 'ASC')
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_price_list_customer_groups($price_list_id)
  {
    $rs = $this->db->where('price_list_id', $price_list_id)->get($this->tc);    

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add_price_list_customer_group(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tc, $ds);
    }

    return FALSE;
  }


  public function delete_price_list_customer_groups($price_list_id)
  {
    return $this->db->where('price_list_id', $price_list_id)->delete($this->tc);
  }
  

  //--- add price list
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


  //--- add item to priced list
  public function add_item(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  //--- add step to item
  public function add_item_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert($this->tr, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  //--- update price list
  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  //--- update item in price list
  public function update_item($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  //--- delete price list
  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  //--- delete item
  public function delete_item($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  //--- delete all step in item
  public function delete_details($step_id)
  {
    return $this->db->where('step_id', $step_id)->delete($this->tr);
  }


  public function count_step($step_id)
  {
    $count = $this->db->where('step_id', $step_id)->count_all_results($this->tr);

    return $count;
  }


  public function count_items($id)
  {
    $count = $this->db->where('price_list_id', $id)->count_all_results($this->td);

    return $count;
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( isset($ds['type']) && $ds['type'] != "all")
    {
      $this->db->where('type_id', $ds['type']);
    }

    if(isset($ds['from_date']) && !empty($ds['from_date']))
    {
      $this->db->where('start_date >=', from_date($ds['from_date']));
    }

    if(isset($ds['to_date']) && !empty($ds['to_date']))
    {
      $this->db->where('end_date <=', to_date($ds['to_date']));
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
    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('active', $ds['status']);
    }

    if(isset($ds['type']) && $ds['type'] != "all")
    {
      $this->db->where('type_id', $ds['type']);
    }

    if(isset($ds['from_date']) && !empty($ds['from_date']))
    {
      $this->db->where('start_date >=', from_date($ds['from_date']));
    }

    if(isset($ds['to_date']) && !empty($ds['to_date']))
    {
      $this->db->where('end_date <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function is_exists_name($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_item($itemCode, $price_list_id)
  {
    $count = $this->db->where('price_list_id', $price_list_id)->where('ItemCode', $itemCode)->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_name($id)
  {
    $rs = $this->db->select('name')->where('id', $id)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }
}
?>
