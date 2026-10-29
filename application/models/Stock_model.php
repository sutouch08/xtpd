<?php
class Stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_stock($itemCode, $whsCode = NULL)
  {
    $this->ms
    ->select_sum('OnHand')
    ->select_sum('IsCommited')
    ->select_sum('OnOrder')
    ->select_sum('StockValue')
    ->where('ItemCode', $itemCode);
    if($whsCode !== NULL && $whsCode !== "")
    {
      $this->ms->where('WhsCode', $whsCode);
    }

    $rs = $this->ms->get('OITW');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_committed_stock($itemCode, $whsCode = NULL)
  {
    $rs = $this->ms
    ->select_sum('IsCommited', 'committed')
    ->where('ItemCode', $itemCode)
    ->where('WhsCode', $whsCode)
    ->get('OITW');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->committed;
    }

    return 0;
  }


  public function get_stock_each_warehouse($itemCode, $whList = NULL)
  {
    $this->ms
    ->select('WhsCode')
    ->select('(OnHand - IsCommited) AS OnHandQty')
    ->where('ItemCode', $itemCode);

    if(!empty($whList) && is_array($whList))
    {
      $this->ms->where_in('WhsCode', $whList);
    }

    $this->ms->order_by('WhsCode', 'ASC');

    $rs = $this->ms->get('OITW');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_stock_zone_qty($itemCode, $WhsCode)
  {
    $rs = $this->ms
    ->select('OBIN.SL1Code AS zone_code')
    ->select('OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry')
    ->where('OIBQ.ItemCode', $itemCode)
    ->where('OIBQ.WhsCode', $WhsCode)
    ->order_by('OIBQ.OnHandQty', 'DESC')
    ->limit(1)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


}
?>
