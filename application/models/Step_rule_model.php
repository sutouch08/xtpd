<?php
class Step_rule_model extends CI_Model
{
  private $tb = "step_rule";
  private $td = "step_rule_details";

  public function __construct()
  {
    parent::__construct();
  }


  public function get($PriceList)
  {
    $rs = $this->db->where('PriceList', $PriceList)->get($this->tb);

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


  public function get_details($PriceList)
  {
    $rs = $this->db
    ->where('PriceList', $PriceList)
    ->order_by('position', 'ASC')
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_active_details($PriceList)
  {
    $rs = $this->db
    ->select('d.*')
    ->from('step_rule_details AS d')
    ->join('step_rule AS r', 'd.PriceList = r.PriceList', 'left')
    ->where('r.PriceList', $PriceList)
    ->where('r.active', 1)
    ->where('d.active', 1)
    ->order_by('d.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_active_price_list()
  {
    $rs = $this->db
    ->where('active', 1)
    ->get($this->tb);

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
      return $this->db->insert($this->tb, $ds);
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
      return $this->db->where('PriceList', $id)->update($this->tb, $ds);
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


  public function delete($PriceList)
  {
    return $this->db->where('PriceList', $PriceList)->delete($this->tb);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($PriceList)
  {
    return $this->db->where('PriceList', $PriceList)->delete($this->td);
  }


  public function delete_detail_by_ids(array $ds = array())
  {
    return $this->db->where_in('id', $ds)->delete($this->td);
  }


  public function is_exists_price_list($price_list)
  {
    $count = $this->db->where('PriceList', $price_list)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function count_step($PriceList)
  {
    $count = $this->db->where('PriceList', $PriceList)->count_all_results($this->td);

    return $count;
  }


  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if(isset($ds['price_list']) && $ds['price_list'] != 'all')
    {
      $this->db->where('PriceList', $ds['price_list']);
    }

    if(isset($ds['name']) && $ds['name'] != "")
    {
      $this->db->like('name', $ds['name']);
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
    if(isset($ds['price_list']) && $ds['price_list'] != 'all')
    {
      $this->db->where('PriceList', $ds['price_list']);
    }

    if(isset($ds['name']) && $ds['name'] != "")
    {
      $this->db->like('name', $ds['name']);
    }


    if(isset($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('active', $ds['status']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_user_price_list($user_id)
  {
    $rs = $this->db
    ->select('pl.id, pl.name')
    ->from('price_list AS pl')
    ->join('user_price_list AS upl', 'upl.list_id = pl.id', 'left')
    ->join('step_rule AS sr', 'sr.PriceList = pl.id', 'left')
    ->where('upl.user_id', $user_id)
    ->where('pl.active', 1)
    ->where('sr.active', 1)    
    ->where('upl.list_id IS NOT NULL')
    ->where('pl.active', 1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }  
}
?>
