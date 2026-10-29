<?php
class Product_lot_check_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_data($ItemCode, $limit = 0)
  {
    $whsCode = getConfig('DEFAULT_WAREHOUSE');

    $this->ms
    ->select('ItemCode, ItemName, BatchNum, ExpDate, PrdDate, WhsCode')
    ->select_sum('Quantity', 'qty')
    ->where('WhsCode', $whsCode)
    ->where('ItemCode', $ItemCode)
    ->where('Quantity >', 0)
    ->group_by('ItemCode, ItemName, BatchNum, ExpDate, PrdDate, WhsCode')
    ->order_by('BatchNum', 'ASC');

    if($limit > 0)
    {
      $this->ms->limit($limit);
    }

    $rs = $this->ms->get('OIBT');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


} //-- end class