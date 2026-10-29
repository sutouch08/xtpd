<?php
class Promotion_model extends CI_Model
{
  private $tb = "promotion";
  private $td = "promotion_detail";

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


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail_by_item($promotion_id, $item_code)
  {
    $rs = $this->db
    ->where('promotion_id', $promotion_id)
    ->where('ItemCode', $item_code)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details($promotion_id)
  {
    $rs = $this->db->where('promotion_id', $promotion_id)->get($this->td);

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


  public function delete_details($promotion_id)
  {
    return $this->db->where('promotion_id', $promotion_id)->delete($this->td);
  }



  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if(isset($ds['code']) && $ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }


    if(isset($ds['name']) && $ds['name'] != "")
    {
      $this->db->like('name', $ds['name']);
    }


    if(isset($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['start_date']) && $ds['start_date'] != "")
    {
      $this->db->where('start_date >=', from_date($ds['start_date']));
    }

    if(isset($ds['end_date']) && $ds['end_date'] != "")
    {
      $this->db->where('end_date <=', to_date($ds['end_date']));
    }

    $this->db->order_by('code', 'DESC')->limit($limit, $offset);

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return NULL;
  }




  public function count_rows(array $ds = array())
  {
    if(isset($ds['code']) && $ds['code'] != "")
    {
      $this->db->like('code', $ds['code']);
    }


    if(isset($ds['name']) && $ds['name'] != "")
    {
      $this->db->like('name', $ds['name']);
    }


    if(isset($ds['status']) && $ds['status'] != "all")
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['start_date']) && $ds['start_date'] != "")
    {
      $this->db->where('start_date >=', from_date($ds['start_date']));
    }

    if(isset($ds['end_date']) && $ds['end_date'] != "")
    {
      $this->db->where('end_date <=', to_date($ds['end_date']));
    }


    return $this->db->count_all_results($this->tb);
  }




  public function get_all_active_promotion()
  {
    $today = date('Y-m-d');

    $rs = $this->db
    ->where('status', 1)
    ->where('start_date <=', $today)
    ->where('end_date >=', $today)
    ->get('promotion');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }





  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    return $rs->row()->code;
  }


}
?>
