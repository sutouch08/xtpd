<?php 
class Customer_group_model extends CI_Model
{
  private $tb = 'customer_group';
  private $td = 'customer_group_detail';

  public function __construct()
  {
    parent::__construct();
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
      return $this->db->insert($this->td, $ds);
    }

    return FALSE;
  }


  public function add_details(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert_batch($this->td, $ds);
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
    $rs = $this->db->order_by('code', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_active()
  {
    $rs = $this->db->where('active', 1)->order_by('code', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db
    ->select('d.*, c.CardName')
    ->from($this->td . ' d')
    ->join('customer c', 'd.CardId = c.id', 'left')
    ->where('d.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($group_id)
  {
    $rs = $this->db
    ->select('d.*, c.CardName')
    ->from($this->td . ' d')
    ->join('customer c', 'd.CardId = c.id', 'left')
    ->where('d.group_id', $group_id)
    ->order_by('d.CardCode', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_by_customer($CardCode)
  {
    $rs = $this->db
    ->select('d.*, c.name AS group_name')
    ->from($this->td . ' d')
    ->join('customer_group c', 'd.group_id = c.id', 'left')
    ->where('d.CardCode', $CardCode)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      if($ds['code'] != '')
      {
        $this->db->like('code', $ds['code']);
      }

      if($ds['name'] != '')
      {
        $this->db->like('name', $ds['name']);
      }

      if($ds['active'] !== 'all')
      {
        $this->db->where('active', $ds['active']);
      }
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds))
    {
      if($ds['code'] != '')
      {
        $this->db->like('code', $ds['code']);
      }

      if($ds['name'] != '')
      {
        $this->db->like('name', $ds['name']);
      }

      if($ds['active'] !== 'all')
      {
        $this->db->where('active', $ds['active']);
      }
    }

    $rs = $this->db
    ->order_by('code', 'ASC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists($code, $id = NULL)
  {
    if(!empty($id))
    {
      $this->db->where('id !=', $id);
    }

    return $this->db->where('code', $code)->count_all_results($this->tb) > 0;
  }


  public function count_member($group_id)
  {
    return $this->db->where('group_id', $group_id)->count_all_results($this->td);
  }

} //---