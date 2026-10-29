<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Export
{
  protected $ci;
  public $error;

	public function __construct()
	{
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
	}


  public function export_order($code)
  {
    $this->ci->load->model('orders_model');

    $sc = TRUE;

		$order = $this->ci->orders_model->get($code);

		if(!empty($order))
		{
			$details = $this->ci->orders_model->get_details($code);

			if(!empty($details))
			{

				$is_exists = $this->ci->orders_model->is_exists_sap_order($code);

				if(! $is_exists)
				{
					$temp = $this->ci->orders_model->get_temp_order($code);

		      if(!empty($temp))
		      {
		        foreach($temp as $rows)
		        {
		          if(! $this->ci->orders_model->drop_temp_exists_data($rows->DocEntry))
		          {
		            $sc = FALSE;
		            $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
		          }
		        }
		      }


					if($sc === TRUE)
					{
            $price_list = $this->ci->orders_model->price_list_name($order->PriceList, $order->SpecialPriceList);
						//--- header data
						$arr = array(
							'U_WEB_ORNO' => $order->code,
							'DocType' => 'I',
							'CANCELED' => 'N',
							'DocDate' => sap_date($order->DocDate, TRUE),
							'DocDueDate' => sap_date($order->DocDueDate, TRUE),
							'CardCode' => $order->CardCode,
							'CardName' => $order->CardName,
							'PayToCode' => $order->PayToCode,
							'Address' => $order->Address,
							'ShipToCode' => $order->ShipToCode,
							'Address2' => $order->Address2,
							'NumAtCard' => get_null($order->NumAtCard),
							'DocCur' => $order->DocCur,
							'DocRate' => $order->DocRate,
              'DiscPrcnt' => $order->DiscPrcnt,
              'DiscSum' => $order->DiscSum,
							'DocTotal' => $order->DocTotal,
							'VatSum' => $order->VatSum,
							'GroupNum' => $order->GroupNum,
							'SlpCode' => $order->SlpCode,
							'OwnerCode' => $order->OwnerCode,
							'U_BEX_TYPE' => $order->CardType,
							'U_SQ_BILLDATE' => $order->BillDate == 1 ? 'Y' : 'N',
							'U_SQ_REQ_SQ' => $order->requireSQ == 1 ? 'Y' : 'N',
							'U_SQ_Shipto' => $order->Address3,
							'U_BEX_WEBORDERREMARK' => $order->Comments,
              'U_Pricelist' => $price_list,
              'U_Approval' => $order->Approver,
							'F_Web' => 'A',
							'F_WebDate' => now(),
              'U_TPD_HDiscSale' => $order->is_discount_sales == 1 ? 'Y' : NULL,
							'TPD_OrdType' => $order->isExport == 1 ? 'E' : 'L'
						);

						//--- start transection
						$this->ci->mc->trans_begin();

						$docEntry = $this->ci->orders_model->add_temp_order($arr);

						if($docEntry)
						{
							$LineNum = 0;

							foreach($details as $rs)
							{
								if($sc === FALSE)
								{
									break;
								}

								if($rs->status == 'A')
								{
									$price = 	remove_vat($rs->SellPrice, $rs->VatRate);

									if($rs->free_item == 1 && !empty($rs->link_id))
									{
										$line = $this->ci->orders_model->get_detail($rs->link_id);

										if(!empty($line))
										{
											$price = remove_vat($line->SellPrice, $rs->VatRate);
										}
									}

									$arr = array(
										'DocEntry' => $docEntry,
										'LineNum' => $LineNum,
										'ItemCode' => $rs->ItemCode,
										'Dscription' => $rs->ItemName,
										'Quantity' => $rs->Qty,
										'UnitMsr' => $rs->UomCode,
										'PriceBefDi' => remove_vat($rs->stdPrice, $rs->VatRate),
										'LineTotal' => remove_vat($rs->LineTotal, $rs->VatRate),
										'Currency' => $order->DocCur,
										'Rate' => $order->DocRate,
										'DiscPrcnt' => $rs->DiscPrcnt,
										'Price' => $price,
										'VatPrcnt' => $rs->VatRate,
										'VatGroup' => $rs->VatGroup,
										'PriceAfVAT' => $rs->SellPrice,
										'GTotal' => $rs->LineTotal,
										'VatSum' => $rs->VatAmount,
										'WhsCode' => $rs->WhsCode,
                    'FreeTxt' => $rs->FreeText,
										'OwnerCode' => $order->OwnerCode,
										'U_WEB_ORNO' => $order->code,
										'U_BEX_WEBREMARK' => $rs->LineText,
                    'U_Pricelist' => $price_list,
                    'U_Approval' => $order->Approver,
                    'U_TPD_DDiscSale' => $rs->discount_sales == 1 ? 'Y' : NULL
									);

									if(!$this->ci->orders_model->add_temp_detail($arr))
									{
										$sc = FALSE;
										$this->error = "Insert Temp detail failed : {{$rs->ItemCode}}";
									}

									$LineNum++;
								}
							}


							if($sc === TRUE)
							{
								$this->ci->mc->trans_commit();
							}
							else
							{
								$this->ci->mc->trans_rollback();
							}


							if($sc === TRUE)
							{
								$arr = array(
                  'DocNum' => NULL,
									'Status' => 1,
									'temp_date' => now()
								);

								$this->ci->orders_model->update($code, $arr);
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Insert Document Failed";
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Document Already in SAP";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "No Items in Order";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Order not found : {$code}";
		}

		return $sc;
  }
} //--- end class
?>
