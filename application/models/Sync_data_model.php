<?php
class Sync_data_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['docType'] !== 'all')
    {
      $this->db->where('sync_code', $ds['docType']);
    }

    if(!empty($ds['docNum']))
    {
      $this->db->like('get_code', $ds['docNum']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results('sync_logs');
  }



  public function get_list(array $ds = array(), $limit = 20, $offset = 0)
  {
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if($ds['docType'] !== 'all')
    {
      $this->db->where('sync_code', $ds['docType']);
    }

    if(!empty($ds['docNum']))
    {
      $this->db->like('get_code', $ds['docNum']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('date_upd', 'DESC')->limit($limit, $offset)->get('sync_logs');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }





  public function add_logs(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('sync_logs', $ds);
    }
    return FALSE;
  }


  public function clear_logs($days = 7)
  {
    $date = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

    return $this->db->where('date_upd <', $date)->delete('sync_logs');
  }


  public function clear_all_logs()
  {
    return $this->db->where('id >', 0)->delete('sync_logs');
  }

}//--- end class

?>
