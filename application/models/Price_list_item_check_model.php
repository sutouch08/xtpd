<?php
class Price_list_item_check_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_user_price_list($user_id)
  {
    $rs = $this->db
    ->select('pl.id, pl.name, upl.list_id')
    ->from('price_list AS pl')
    ->join('user_price_list AS upl', 'pl.id = upl.list_id', 'left')
    ->where('upl.user_id', $user_id)
    ->where('pl.active', 1)
    ->order_by('pl.position', 'ASC')
    ->get();
    
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //-- end class