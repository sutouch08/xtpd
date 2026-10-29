<?php
class Item_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get_items_by_price_list($priceList, array $item_type = array())
  {
    if($priceList)
    {
      $this->ms
      ->select('OITM.ItemCode AS code, OITM.ItemName AS name')
      ->select('OITM.SalUnitMsr AS uom, OITM.U_TPD_DiscSale, ITM1.Price AS price')
      ->select('OITM.DfltWH AS dfWhsCode, OITM.U_TPD_DrugType')
      ->select('OVTG.Code AS VatCode, OVTG.Rate')
      ->from('OITM')
      ->join('ITM1', 'ITM1.ItemCode = OITM.ItemCode', 'left')
      ->join('OVTG', 'OVTG.Code = OITM.VatGourpSa', 'left')
      ->where('OITM.ItemType', 'I')
      ->where('OITM.SellItem', 'Y')
      ->where('OITM.validFor', 'Y')
      ->where('ITM1.PriceList', $priceList)
      ->where('ITM1.Price >', 0);

      if( ! empty($item_type))
      {
        $this->ms->where_in('OITM.U_TPD_DrugType', $item_type);
      }

      $rs = $this->ms->order_by('OITM.ItemName', 'ASC')->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

  public function get($code, $priceList = NULL)
  {
    if($priceList !== NULL)
    {
      $rs = $this->ms
      ->select('OITM.ItemCode AS code, OITM.ItemName AS name')
      ->select('OITM.SalUnitMsr AS uom, OITM.U_TPD_DiscSale, ITM1.Price AS price')
      ->select('OITM.DfltWH AS dfWhsCode, OITM.U_BEX_Controll, OITM.U_TPD_DrugType')
      ->select('OVTG.Code AS VatCode, OVTG.Rate')
      ->from('OITM')
      ->join('ITM1', 'ITM1.ItemCode = OITM.ItemCode', 'left')
      ->join('OVTG', 'OVTG.Code = OITM.VatGourpSa', 'left')
      ->where('OITM.ItemCode', $code)
      ->where('ITM1.PriceList', $priceList)
      ->where('ITM1.Price >', 0)
      ->get();

      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return NULL;
  }



  public function get_item($code)
  {
    $rs = $this->ms
    ->select('OITM.ItemCode AS code, OITM.ItemName AS name')
    ->select('OITM.SalUnitMsr AS uom, OITM.DfltWH AS dfWhsCode, OITM.U_TPD_DiscSale, OITM.U_BEX_Controll, OITM.U_TPD_DrugType')
    ->select('OVTG.Code AS VatCode, OVTG.Rate')
    ->from('OITM')
    ->join('OVTG', 'OVTG.Code = OITM.VatGourpSa', 'left')
    ->where('OITM.ItemCode', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_item_list()
  {
    $rs = $this->ms
    ->select('ItemCode AS code, ItemName AS name, SalUnitMsr AS UoM')
    ->where('ItemType', 'I')
    ->where('SellItem', 'Y')
    ->where('validFor', 'Y')
    ->order_by('ItemName', 'ASC')
    ->get('OITM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_items_by_range($pdFrom, $pdTo, $group = NULL)
  {
    $this->ms
    ->select('ItemCode AS code, ItemName AS name, SalUnitMsr AS UoM')
    ->where('ItemCode >=', $pdFrom)
    ->where('ItemCode <=', $pdTo);

    if(!empty($group) && is_array($group))
    {
      $this->ms->where_in('ItmsGrpCod', $group);
    }

    $this->ms->order_by('ItemCode', 'ASC');

    $rs = $this->ms->get('OITM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return  NULL;
  }


  public function get_item_group_list()
  {
    $rs = $this->ms
    ->select('ItmsGrpCod AS code, ItmsGrpNam AS name')
    ->order_by('ItmsGrpCod', 'ASC')
    ->get('OITB');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_average_cost($code)
	{
		$rs = $this->ms
    ->select('LstEvlPric AS cost')
    ->where('ItemCode', $code)
    ->get('OITM');

		if($rs->num_rows() === 1)
    {
      return $rs->row()->cost;
    }

    return NULL;
	}


  public function get_item_cost($code)
	{
		$rs = $this->ms
    ->select('Price AS cost')
    ->where('ItemCode', $code)
    ->where('PriceList', $this->cost_list)
    ->get('ITM1');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->cost;
    }

    return 0;
	}


  public function getValidDetail($ItemCode)
  {
    $rs = $this->ms->select('SellItem, validFor')->where('ItemCode', $ItemCode)->get('OITM');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

} //---- End class

 ?>
