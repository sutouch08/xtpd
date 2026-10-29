<?php
class Payment_request_model extends CI_Model
{
  private $tb = "payment_request_order";

  public function __construct()
  {
    parent::__construct();
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


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_by_id($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_payment_request($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_reply_status($id)
  {
    $rs = $this->db->select('reply_status')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->reply_status;
    }

    return NULL;
  }

  public function get_reply_status_by_code($code)
  {
    $rs = $this->db->select('reply_status')->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->reply_status;
    }

    return NULL;
  }

  public function count_rows(array $ds = array())
  {
    if (! $this->_SuperAdmin && ! $this->isGM && ! $this->isAdmin)
    {
      $this->db->where('uname', $this->_user->uname);
    }    

    if (!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if (!empty($ds['customer']))
    {
      $this->db
        ->group_start()
        ->like('CardCode', $ds['customer'])
        ->or_like('CardName', $ds['customer'])
        ->group_end();
    }

    if (!empty($ds['status']) && $ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if (isset($ds['request_by']) && $ds['request_by'] !== 'all')
    {
      $this->db->where('add_by', $ds['request_by']);
    }

    if (isset($ds['has_document']) && $ds['has_document'] !== 'all')
    {
      $this->db->where('has_document', $ds['has_document']);
    }

    if (!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! $this->_SuperAdmin && ! $this->isGM && ! $this->isAdmin)
    {
      $this->db->where('uname', $this->_user->uname);
    }    

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('CardCode', $ds['customer'])
      ->or_like('CardName', $ds['customer'])
      ->group_end();
    }    

    if(!empty($ds['status']) && $ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['request_by']) && $ds['request_by'] !== 'all')
    {
      $this->db->where('add_by', $ds['request_by']);
    }

    
    if(isset($ds['has_document']) && $ds['has_document'] !== 'all')
    {
      $this->db->where('has_document', $ds['has_document']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('date_add', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
