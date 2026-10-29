<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends PS_Controller
{
	public $menu_code = 'ORDERS';
	public $menu_group_code = 'ORDER';
	public $title = 'Orders';
	public $disSale = FALSE;

	public function __construct()
	{
		parent::__construct();
		$this->home = base_url() . 'orders';
		$this->load->model('orders_model');
		$this->load->model('item_model');
		$this->load->model('customer_model');
		$this->load->model('customer_group_model');
		$this->load->model('sales_team_condition_model');
		$this->load->model('customer_team_model');
		$this->load->model('price_list_model');
		$this->load->model('price_list_type_model');
		$this->load->model('special_price_list_model');
		$this->load->helper('orders');
		$this->load->helper('sales_team_condition');
		$this->load->helper('price_list');
		$this->load->helper('special_price_list');
		$this->load->helper('approve');
		$this->load->helper('credit_approval');

		$this->disSale = getConfig('USE_DISCSALE') == 1 ? TRUE : FALSE;
	}

	public function index()
	{
		$filter = array(
			'con_id' => get_filter('con_id', 'so_con_id', 'all'),
			'WebCode' => get_filter('WebCode', 'so_WebCode', ''),
			'DocNum' => get_filter('DocNum', 'so_DocNum', ''),
			'DeliveryNo' => get_filter('DeliveryNo', 'so_DeliveryNo', ''),
			'InvoiceNo' => get_filter('InvoiceNo', 'so_InvoiceNo', ''),
			'PoNo' => get_filter('PoNo', 'so_PoNo', ''),
			'CardCode' => get_filter('CardCode', 'so_CardCode', ''),
			'user_id' => get_filter('user_id', 'so_user_id', 'all'),
			'Approver' => get_filter('Approver', 'so_Approver', 'all'),
			'Approved' => get_filter('Approved', 'so_Approved', 'all'),
			'credit_issue' => get_filter('credit_issue', 'so_credit_issue', 'all'),
			'is_over_due' => get_filter('is_over_due', 'so_is_over_due', 'all'),
			'Status' => get_filter('Status', 'doc_status', 'all'),
			'SO_Status' => get_filter('SO_Status', 'SO_Status', 'all'),
			'DO_Status' => get_filter('DO_Status', 'DO_Status', 'all'),
			'INV_Status' => get_filter('INV_Status', 'INV_Status', 'all'),
			'fromDate' => get_filter('fromDate', 'so_fromDate', ''),
			'toDate' => get_filter('toDate', 'so_toDate', ''),
			'is_discount_sales' => get_filter('is_discount_sales', 'is_discount_sales', 'all'),
			'is_export' => get_filter('is_export', 'so_is_export', 'all')
		);

		if ($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_filter('set_rows', 'rows', 20);
			//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
			if ($perpage > 300)
			{
				$perpage = get_filter('rows', 'rows', 300);
			}

			$segment = 3; //-- url segment
			$rows = $this->orders_model->count_rows($filter);

			//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
			$init	= pagination_config($this->home . '/index/', $rows, $perpage, $segment);

			$rs = $this->orders_model->get_list($filter, $perpage, $this->uri->segment($segment));

			$filter['data'] = $rs;

			$this->pagination->initialize($init);
			$this->load->view('orders/orders_list', $filter);
		}
	}

	public function get_order_flow()
	{
		$html = "";
		$ds = json_decode(file_get_contents('php://input'));

		if (! empty($ds))
		{
			$html = order_step_flow($ds->currentStep, $ds->creditIssue, $ds->overdue);
		}

		echo $html;
	}

	public function get_customer_invoice()
	{
		$this->load->model('report/customer_status_model');
		$CardCode = $this->input->get('CardCode');
		$ds = [
			'CardCode' => '',
			'CardName' => '',
			'CreateDate' => '',
			'Duration' => '',
			'InvoiceCount' => 0
		];

		$bp = $this->customer_model->get($CardCode);

		if (! empty($bp))
		{
			$invoiceCount = $this->customer_status_model->count_customer_invoice($CardCode);
			$today = date('Y-m-d');
			$ds = [
				'CardCode' => $bp->CardCode,
				'CardName' => $bp->CardName,
				'CreateDate' => thai_date($bp->CreateDate, FALSE),
				'Duration' => number(date_diff(date_create($bp->CreateDate), date_create($today))->days),
				'InvoiceCount' => number($invoiceCount)
			];
		}
		else
		{
			$sc = FALSE;
			$this->error = "Customer not found";
		}

		$arr = array(
			'status' => ! empty($bp) ? 'success' : 'error',
			'message' => ! empty($bp) ? 'success' : $this->error,
			'data' => $ds,
		);
		echo json_encode($arr);
	}

	public function get_credit_data()
	{
		$sc = TRUE;
		$res = array(
			'CustCode' => '',
			'isRegular' => 0,
			'CreditLine' => 0.00,
			'Balance' => 0.00,
			'DNotesBal' => 0.00,
			'OrdersBal' => 0.00,
			'CreditBalance' => 0.00,
			'overdue' => 0
		);

		$CardCode = $this->input->get('CardCode');
		$card = $this->customer_model->get_by_code($CardCode);

		if (! empty($card))
		{
			$credit = empty($card->CustCode) ? $this->customer_model->get_credit_details($card->CardCode) : $this->customer_model->get_credit_details_by_hCode($card->CustCode);
			$CreditLine = $card->isRegular == 1 ? floatval($credit->CreditLine) : floatval(getConfig('GLOBAL_CREDIT_LINE'));
			$overdue = $this->orders_model->is_over_due($card->CardCode, $card->CustCode) ? 1 : 0;

			if (! empty($credit))
			{
				$orderUsed = $this->orders_model->get_credit_used_amount($card->CardCode, $card->CustCode);
				$creditBalance = $CreditLine - ($credit->Balance + $credit->DNotesBal + $credit->OrdersBal + $orderUsed);

				$res = array(
					'CardCode' => empty($card->CustCode) ? $card->CardCode : $card->CardCode . ' (' . $card->CustCode . ')',
					'isRegular' => $card->isRegular,
					'CreditLine' => $CreditLine,
					'Balance' => $credit->Balance,
					'DNotesBal' => $credit->DNotesBal,
					'OrdersBal' => $credit->OrdersBal,
					'OrderUsed' => $orderUsed,
					'CreditBalance' => $creditBalance,
					'overdue' => $overdue
				);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Customer not found";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'error',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'data' => $res,
		);

		echo json_encode($arr);
	}

	public function get_available_credit()
	{
		$creditBalance = 0.00;
		$CardCode = $this->input->get('CardCode');
		$card = $this->customer_model->get_by_code($CardCode);

		if (! empty($card))
		{
			$credit = empty($card->CustCode) ? $this->customer_model->get_credit_details($card->CardCode) : $this->customer_model->get_credit_details_by_hCode($card->CustCode);
			$CreditLine = $card->isRegular == 1 ? floatval($credit->CreditLine) : floatval(getConfig('GLOBAL_CREDIT_LINE'));
			//$CreditLine = $CreditLine <= 0 ? floatval(getConfig('GLOBAL_CREDIT_LINE')) : $CreditLine;

			if (! empty($credit))
			{
				$orderUsed = $this->orders_model->get_credit_used_amount($card->CardCode, $card->CustCode);
				$creditBalance = $CreditLine - ($credit->Balance + $credit->DNotesBal + $credit->OrdersBal + $orderUsed);
			}
		}

		echo $creditBalance;
	}

	public function get_price_list_by_type()
	{
		$sc = TRUE;
		$cardCode = $this->input->post('CardCode');
		$type = $this->input->post('type');
		$html = "<option value=''>Select</option>";
		$PL = [];

		$priceList = $this->user_model->get_user_price_list($this->_user->id);

		if (! empty($priceList))
		{
			foreach ($priceList as $pl)
			{
				$PL['Standard'][] = (object) array(
					'id' => $pl->id,
					'spid' => 0,
					'name' => $pl->name
				);
			}
		}

		if (! empty($cardCode))
		{
			$groupIds = [0];
			$customerGroups = $this->customer_group_model->get_by_customer($cardCode);

			if (! empty($customerGroups))
			{
				foreach ($customerGroups as $cg)
				{
					$groupIds[] = $cg->group_id;
				}
			}

			if ($type === 'all')
			{
				$tp = $this->price_list_type_model->get_all();

				if (! empty($tp))
				{
					foreach ($tp as $t)
					{
						$tpd = $this->special_price_list_model->get_active_customer_list_by_type($t->id, $groupIds);

						if (! empty($tpd))
						{
							foreach ($tpd as $sp)
							{
								$PL[$t->name][] = (object) array(
									'id' => 'x',
									'spid' => $sp->id,
									'name' => $sp->name
								);
							}
						}
					}
				}
			}
			else
			{
				$tp = $this->price_list_type_model->get($type);

				if (! empty($tp))
				{
					$tpd = $this->special_price_list_model->get_active_customer_list_by_type($tp->id, $groupIds);

					if (! empty($tpd))
					{
						foreach ($tpd as $sp)
						{
							$PL[$tp->name][] = (object) array(
								'id' => 'x',
								'spid' => $sp->id,
								'name' => $sp->name
							);
						}
					}
				}
			}
		}

		if (! empty($PL))
		{
			foreach ($PL as $label => $list)
			{
				$html .= '<optgroup label="' . $label . '">';

				foreach ($list as $pl)
				{
					$html .= '<option value="' . $pl->id . '" data-spid="' . $pl->spid . '">' . $pl->name . '</option>';
				}

				$html .= '</optgroup>';
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Price list not found";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'error',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'priceList' => $html,
		);

		echo json_encode($arr);
	}

	public function add_new()
	{
		$this->title = "Order Add";

		if ($this->pm->can_add)
		{
			$this->load->helper('currency');

			$ds['code'] = NULL; 

			if ($this->isAdmin)
			{
				$ds['customer'] = $this->customer_model->get_all_user_customer_list("V");
			}
			else
			{
				$ds['customer'] = $this->customer_model->get_user_customer_list($this->_user->area_id, "V");
			}

			$PL = [];

			$priceList = $this->user_model->get_user_price_list($this->_user->id);

			if (! empty($priceList))
			{
				foreach ($priceList as $pl)
				{
					$PL['Standard'][] = (object) array(
						'id' => $pl->id,
						'spid' => 0,
						'name' => $pl->name
					);
				}
			}

			$ds['priceList'] = $PL;
			$this->load->view('orders/orders_add', $ds);
		}
		else
		{
			$this->deny_page();
		}
	}

	public function get_currency_rate($code)
	{
		$date = date('Y-m-d 00:00:00');
		$rate = $this->orders_model->get_currency_rate($code, $date);

		if (! empty($rate))
		{
			echo $rate;
		}
		else
		{
			echo "notfound";
		}
	}

	public function get_user_customer_list($type = 'V')
	{
		if ($this->isAdmin)
		{
			$list = $this->customer_model->get_all_user_customer_list($type);
		}
		else
		{
			$list = $this->customer_model->get_user_customer_list($this->_user->area_id, $type);
		}

		if (! empty($list))
		{
			$ds = array();

			foreach ($list as $rs)
			{
				$arr = array(
					'CardCode' => $rs->CardCode,
					'CardName' => $rs->CardName,
					'CustCode' => $rs->CustCode,
					'GroupNum' => $rs->GroupNum,
					'Currency' => $rs->Currency,
					'ECVatGroup' => $rs->ECVatGroup,
					'SlpCode' => $rs->SlpCode,
					'Rate' => $rs->Rate,
					'customerType' => $rs->customer_type, //--- U_TPD_RA_DrugType, 1 - 4
					'isControl' => $rs->isControl == '1' ? 'Y' : 'N',
					'saleTeam' => $rs->saleTeam, //-- U_TPD_BI_SalesTeam
					'areaId' => $rs->areaId, //--- U_TPD_BI_AreaName
					'salePerson' => $rs->salePerson, //-- U_SALE_PERSON
					'department' => $rs->department, //-- U_TPD_BI_Department
					'regular' => $rs->isRegular
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
		else
		{
			echo "Customer relate {$type} not found";
		}
	}

	public function get_address_ship_to_code()
	{
		$code = trim($this->input->get('CardCode'));
		$ds = array();

		if (! empty($code))
		{
			$addr = $this->customer_model->get_address_ship_to_code($code);

			if (! empty($addr))
			{
				$ds = array();

				$i = 1;

				foreach ($addr as $adr)
				{
					$arr = array(
						'code' => get_empty_text($adr->Address),
						'is_default' => $i === 1 ? 'Y' : 'N'
					);

					array_push($ds, $arr);
					$i++;
				}
			}
			else
			{
				$arr = array(
					'code' => "",
					'is_default' => 'Y'
				);

				array_push($ds, $arr);
			}
		}

		echo json_encode($ds);
	}

	public function get_address_ship_to()
	{
		$code = trim($this->input->get('CardCode'));
		$adr_code = trim($this->input->get('Address'));
		if (! empty($code))
		{
			$adr = $this->customer_model->get_address_ship_to($code, $adr_code);

			if (! empty($adr))
			{
				$arr = array(
					'code' => get_empty_text($adr->Address),
					'address' => get_empty_text($adr->Street),
					'street' => get_empty_text($adr->StreetNo),
					'sub_district' => get_empty_text($adr->Block),
					'district' => get_empty_text($adr->County),
					'province' => get_empty_text($adr->City),
					'country' => get_empty_text($adr->Country),
					'countryName' => get_empty_text($adr->countryName),
					'postcode' => get_empty_text($adr->ZipCode)
				);
			}
			else
			{
				$arr = array(
					'code' => "",
					'address' => "",
					'street' => "",
					'sub_district' => "",
					'district' => "",
					'province' => "",
					'country' => "",
					'countryName' => "",
					'postcode' => ""
				);
			}

			echo json_encode($arr);
		}
	}

	public function get_address_bill_to_code()
	{
		$code = trim($this->input->get('CardCode'));
		$ds = array();

		if (! empty($code))
		{
			$addr = $this->customer_model->get_address_bill_to_code($code);

			if (! empty($addr))
			{
				$ds = array();
				foreach ($addr as $adr)
				{
					$arr = array(
						'code' => get_empty_text($adr->Address)
					);

					array_push($ds, $arr);
				}
			}
			else
			{
				$arr = array(
					'code' => ""
				);

				array_push($ds, $arr);
			}
		}

		echo json_encode($ds);
	}

	public function get_address_bill_to()
	{
		$code = trim($this->input->get('CardCode'));
		$adr_code = trim($this->input->get('Address'));
		if (! empty($code))
		{
			$adr = $this->customer_model->get_address_bill_to($code, $adr_code);

			if (! empty($adr))
			{
				$arr = array(
					'code' => get_empty_text($adr->Address),
					'address' => get_empty_text($adr->Street),
					'street' => get_empty_text($adr->StreetNo),
					'sub_district' => get_empty_text($adr->Block),
					'district' => get_empty_text($adr->County),
					'province' => get_empty_text($adr->City),
					'country' => get_empty_text($adr->Country),
					'countryName' => get_empty_text($adr->countryName),
					'postcode' => get_empty_text($adr->ZipCode)
				);
			}
			else
			{
				$arr = array(
					'code' => "",
					'address' => "",
					'street' => "",
					'sub_district' => "",
					'district' => "",
					'province' => "",
					'country' => "",
					'countryName' => "",
					'postcode' => ""
				);
			}

			echo json_encode($arr);
		}
	}

	public function get_item_code_and_name($priceList, $sp_id = 0, $isControl = 'N')
	{
		if (empty($priceList))
		{
			echo json_encode(array("Please select price list"));
		}
		elseif ($priceList == 'x')
		{
			$txt = trim($_REQUEST['term']);

			$this->db
				->select('spi.ItemCode, spi.ItemName')
				->from('special_price_item AS spi')
				->join('special_price_list AS spl', 'spi.price_list_id = spl.id', 'left')
				->where('spi.price_list_id', $sp_id)
				->where('spi.active', 1)
				->where('spl.active', 1);

			if ($txt != '*')
			{
				$this->db
					->group_start()
					->like('spi.ItemCode', $txt)
					->or_like('spi.ItemName', $txt)
					->group_end();
			}

			$rs = $this->db
				->order_by('spi.ItemName', 'ASC')
				->limit(20)
				->get();

			if ($rs->num_rows() > 0)
			{
				$items = array();

				foreach ($rs->result() as $rd)
				{
					$items[] = $rd->ItemCode;
				}

				if (! empty($items))
				{
					$this->ms
						->select('ItemCode, ItemName, U_BEX_Controll')
						->where('SellItem', 'Y')
						->where('validFor', 'Y')
						->where_in('ItemCode', $items);

					if ($isControl == 'Y')
					{
						$this->ms
							->group_start()
							->where('U_BEX_Controll IS NULL', NULL, FALSE)
							->or_where('U_BEX_Controll !=', 'Controlled')
							->group_end();
					}

					$ro = $this->ms->order_by('ItemName', 'ASC')->limit(20)->get('OITM');

					if ($ro->num_rows() > 0)
					{
						foreach ($ro->result() as $rp)
						{
							$sc[] = $rp->ItemName . ' | ' . $rp->ItemCode;
						}
					}
					else
					{
						$sc[] = "Not found";
					}
				}
				else
				{
					$sc[] = "Not found";
				}
			}
			else
			{
				$sc[] = "Not found";
			}

			echo json_encode($sc);
		}
		else
		{
			$txt = trim($_REQUEST['term']);

			$qr  = "SELECT OITM.ItemCode, OITM.ItemName, ITM1.PriceList, ITM1.Price ";
			$qr .= "FROM OITM INNER JOIN ITM1 ON OITM.ItemCode = ITM1.ItemCode ";
			$qr .= "WHERE OITM.ItemType = 'I' ";
			$qr .= "AND OITM.SellItem = 'Y' ";
			$qr .= "AND OITM.validFor = 'Y' ";
			$qr .= "AND OITM.ItemCode LIKE 'FG%' ";
			$qr .= ($isControl == 'Y' ? "AND (OITM.U_BEX_Controll IS NULL OR OITM.U_BEX_Controll != 'Controlled') " : "");
			$qr .= "AND ITM1.PriceList = {$priceList} ";
			$qr .= "AND ITM1.Price > 0 ";

			if ($txt != '*')
			{
				$qr .= "AND (OITM.ItemName LIKE N'%{$this->ms->escape_str($txt)}%') ";
			}

			$qr .= "ORDER BY OITM.ItemName ASC ";
			$qr .= "OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

			$rs = $this->ms->query($qr);

			if ($rs->num_rows() > 0)
			{
				foreach ($rs->result() as $rd)
				{
					$sc[] = $rd->ItemName . ' | ' . $rd->ItemCode;
				}
			}
			else
			{
				$sc[] = "Not found";
			}

			echo json_encode($sc);
		}
	}

	public function get_step_template()
	{
		$sc = TRUE;

		$ds = '<option value="0" data-stepqty="0" data-limit="0" data-freeqty="0" data-force="1">Choose Step</option>';

		$PriceList = $this->input->post('PriceList');

		if (! is_null($PriceList))
		{
			if ($PriceList != 'x')
			{
				$this->load->model('step_rule_model');

				$step = $this->step_rule_model->get_active_details($PriceList);

				if (! empty($step))
				{
					foreach ($step as $rs)
					{
						$hi = $rs->highlight == 1 ? 'highlight' : '';

						$ds .= '<option value="' . $rs->id . '"
						class="' . $hi . '"
						data-force="' . $rs->is_force . '"
						data-stepqty="' . $rs->stepQty . '"
						data-limit="' . $rs->limitQty . '"
						data-freeqty="' . $rs->freeQty . '">'
							. $rs->labelText
							. '</option>';
					}
				}
				else
				{
					$ds = '<option value="0" data-stepqty="0" data-limit="0" data-freeqty="0" data-force="0">Choose Step</option>';
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = get_error_message('required');
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'template' => $sc === TRUE ? $ds : NULL
		);

		echo json_encode($arr);
	}

	public function get_item_template()
	{
		$sc = TRUE;

		$ds = '<option value="0">Select</option>';

		$PriceList = $this->input->post('priceList');
		$customer_type = $this->input->post('customer_type');
		$spid = $this->input->post('spid');

		$item_type = item_type_array($customer_type); //--tools_helper  return item_type as array like ['01', '02', '09'];

		if (! is_null($PriceList))
		{
			if ($PriceList != 'x')
			{
				$items = $this->item_model->get_items_by_price_list($PriceList, $item_type);

				if (! empty($items))
				{
					foreach ($items as $rs)
					{
						$ds .= '<option value="' . $rs->code . '" data-name="' . $rs->name . '">' . $rs->name . '</option>';
					}
				}
				else
				{
					$ds = '<option value="0">No Items</option>';
				}
			}
			else
			{
				if ($spid > 0)
				{
					$rs = $this->db
						->select('spi.ItemCode, spi.ItemName')
						->from('special_price_item AS spi')
						->join('special_price_list AS spl', 'spi.price_list_id = spl.id', 'left')
						->where('spi.price_list_id', $spid)
						->where('spi.active', 1)
						->where('spl.active', 1)
						->order_by('spi.ItemName', 'ASC')
						->get();

					if ($rs->num_rows() > 0)
					{
						$items = array();

						foreach ($rs->result() as $rd)
						{
							$items[] = $rd->ItemCode;
						}

						if (! empty($items))
						{
							$this->ms
								->select('ItemCode, ItemName, U_TPD_DrugType')
								->where('SellItem', 'Y')
								->where('validFor', 'Y')
								->where_in('ItemCode', $items)
								->where_in('U_TPD_DrugType', $item_type);

							$ro = $this->ms->order_by('ItemName', 'ASC')->get('OITM');

							if ($ro->num_rows() > 0)
							{
								foreach ($ro->result() as $rd)
								{
									$ds .= '<option value="' . $rd->ItemCode . '" data-name="' . $rd->ItemName . '">' . $rd->ItemName . '</option>';
								}
							}
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid special price list";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = get_error_message('required');
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'template' => $sc === TRUE ? $ds : NULL
		);

		echo json_encode($arr);
	}

	public function get_payment_term()
	{
		$sc = TRUE;

		$this->load->model('payment_term_discount_model');

		$priceList = $this->input->post('PriceList');
		$sp_id = $this->input->post('special_price_id');

		$termList = $this->payment_term_discount_model->get_term_by_price_list($priceList, $sp_id);

		$ds  = '<option value="">เลือก</option>';

		if (! empty($termList))
		{
			foreach ($termList as $rs)
			{
				$ds .= '<option value="' . $rs->id . '"
				data-groupnum="' . $rs->GroupNum . '"
				data-pricelist="' . $rs->list_id . '"
				data-disc="' . $rs->DiscPrcnt . '"
				data-change="' . $rs->canChange . '"
				>' . $rs->name . '</option>';
			}
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'options' => $sc === TRUE ? $ds : NULL
		);

		echo json_encode($arr);
	}

	function get_item_data()
	{
		$sc = TRUE;
		$code = trim($this->input->get('code'));
		$PriceList = $this->input->get('priceList');
		$sp_id = $this->input->get('special_price_id');
		$no = $this->input->get('no');

		// $this->load->model('item_step_price_model');

		if (! empty($code))
		{
			if (! empty($PriceList))
			{
				$this->load->model('stock_model');

				if ($PriceList == 'x')
				{
					//---- special price list
					//---- ไปดึงข้อมูลจาก table item_step_price
					$pd = $this->special_price_list_model->get_item_by_code($code, $sp_id);

					if (! empty($pd))
					{
						$item = $this->item_model->get_item($code);

						if (! empty($item))
						{
							$step = $this->special_price_list_model->get_item_details($pd->id);
							$stock = $this->stock_model->get_stock($item->code, $item->dfWhsCode);
							$whsQty = !empty($stock) ? round($stock->OnHand, 2) : 0;
							$commitQty = !empty($stock) ? round($stock->IsCommited, 2) : 0;
							$available = !empty($stock) ? $whsQty - $commitQty : 0;

							$arr = array(
								'uom' => $pd->UomCode,
								'price' => 0.00,
								'vatCode' => $item->VatCode,
								'vatRate' => $item->Rate,
								'inStock' => $whsQty,
								'commit' => $commitQty,
								'available' => $available,
								'whsCode' => $item->dfWhsCode,
								'is_sale_discount' => $item->U_TPD_DiscSale == 'Y' ? 'Y' : 'N',
								'isControl' => $item->U_BEX_Controll == 'Controlled' ? 'Y' : 'N',
								'DrugType' => $item->U_TPD_DrugType,
								'step' => NULL,
								'stepData' => NULL
							);

							if (! empty($step))
							{
								$stx = '<option value="">Choose Step</option>';
								$da = array();

								foreach ($step as $st)
								{
									$name = empty($st->name) ? $st->Qty . ' ' . $st->UomCode . ' @' . number($st->SellPrice, 2) . ($st->freeQty > 0 ? ' free : ' . $st->freeQty : '') : $st->name;

									$stx .= '<option value="' . $st->id . '"
										data-no="' . $no . '"
										data-item="' . $st->ItemCode . '"
										data-force="0"
										data-stepqty="' . $st->Qty . '"
										data-price="' . $st->SellPrice . '"
										data-uom="' . $st->UomCode . '"
										data-freeqty="' . $st->freeQty . '">' . $name . '</option>';

									$da[] = array(
										'stepQty' => $st->Qty,
										'stepPrice' => $st->SellPrice,
										'freeQty' => $st->freeQty
									);
								}

								$arr['step'] = $stx;
								$arr['stepData'] = $da;
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "ItemCode not found! : {$code}";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ItemCode with special price not found! : {$code}";
					}
				}
				else
				{
					$item = $this->item_model->get($code, $PriceList);

					if (! empty($item))
					{
						$stock = $this->stock_model->get_stock($item->code, $item->dfWhsCode);
						$whsQty = !empty($stock) ? round($stock->OnHand, 2) : 0;
						$commitQty = !empty($stock) ? round($stock->IsCommited, 2) : 0;
						$available = !empty($stock) ? $whsQty - $commitQty : 0;

						$arr = array(
							'uom' => $item->uom,
							'price' => round($item->price, 2),
							'vatCode' => $item->VatCode,
							'vatRate' => $item->Rate,
							'inStock' => $whsQty,
							'commit' => $commitQty,
							'available' => $available,
							'whsCode' => $item->dfWhsCode,
							'is_sale_discount' => $item->U_TPD_DiscSale == 'Y' ? 'Y' : 'N',
							'isControl' => $item->U_BEX_Controll == 'Controlled' ? 'Y' : 'N',
							'DrugType' => $item->U_TPD_DrugType
						);
					}
					else
					{
						$sc = FALSE;
						$this->error = "ItemCode incorrect! : {$code}";
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Please Select Price List";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : ItemCode";
		}

		echo $sc === TRUE ? json_encode($arr) : $this->error;
	}

	public function add_po()
	{
		$sc = TRUE;
		$orderCode = $this->input->post('orderCode');
		$poCode = $this->input->post('poCode');
		$file = isset($_FILES['uploadFile']) ? $_FILES['uploadFile'] : NULL;		
		if (! empty($orderCode))
		{			
			if (! empty($file))
			{
				$this->load->library('upload');				
				$path = $this->config->item('user_files_path') . 'order_po/';

				$config = array(
					'upload_path' => $path,
					'allowed_types' => 'jpg|jpeg|png|pdf',
					'file_name' => $orderCode,
					'max_size' => 5120,
					'overwrite' => TRUE
				);

				$this->upload->initialize($config);

				if (! $this->upload->do_upload('uploadFile'))
				{
					$sc = FALSE;					
					$this->error = "Create order success but upload file failed : " . $this->upload->display_errors();
				}
				else
				{
					$arr = array(
						'NumAtCard' => $poCode,
						'has_file' => 1,
						'file_name' => $this->upload->data('file_name'),
						'file_type' => $this->upload->data('file_ext'),
						'file_size' => $this->upload->data('file_size')
					);

					$this->orders_model->update($orderCode, $arr);
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : Header";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'error',
			'message' => $sc === TRUE ? 'Success' : $this->error
		);

		echo json_encode($arr);
	}


	public function add()
	{
		$sc = TRUE;
		$header = json_decode($this->input->post('header'));
		$details = json_decode($this->input->post('details'));
		$file = isset($_FILES['uploadFile']) ? $_FILES['uploadFile'] : NULL;
		$up = 1; //--- 1 = upload file success, 0 = upload file fail
		$dfWhsCode = getConfig('DEFAULT_WAREHOUSE');

		if (! empty($header))
		{
			if (! empty($details))
			{
				$priceEdit = FALSE; //--- หากมีการแก้ไขราคา ตำกว่า stdPrice ตรงนี้จะเป็น TRUE;
				$code = $this->get_new_code();
				$groupNum = $header->GroupNum == '-10' ? $header->CustomerGroupNum : $header->GroupNum; //-- CustomerGroupNum = groupNum by customer
				$team_id = empty($this->_user->team_id) ? $header->saleTeam : $this->_user->team_id;
				$area_id = $this->_user->role == 'salesAdmin' ? NULL : $header->areaId; //--- ถ้าเป็น sales admin ให้ใช้ team_id ในการดึง condition อย่่างเดียว
				$con_id = $this->sales_team_condition_model->get_condition_id($team_id, $area_id);
				$credit_issue = $header->creditIssue == 1 ? 1 : 0;
				$credit_diff = $header->creditDiff;
				$is_over_due = 0;

				$mustApprove = (isset($header->isDefaultShipTo) && $header->isDefaultShipTo == 'N') ? TRUE : FALSE;
				$mustApprove = $mustApprove == TRUE ? TRUE : (empty($header->exShipTo) ? FALSE : TRUE);

				if ($credit_issue)
				{
					$is_over_due = $this->orders_model->is_over_due($header->CardCode, $header->CustCode) ? 1 : 0;
				}

				$arr = array(
					'code' => $code,
					'CardCode' => $header->CardCode,
					'CardName' => $header->CardName,
					'CustCode' => $header->CustCode,
					'CardGroup' => NULL,
					'CardTeam' => $header->saleTeam,
					'Condition_id' => $con_id,
					'CardType' => $header->CardType,
					'isControl' => $header->isControl,
					'VatGroup' => get_null($header->VatGroup),
					'SlpCode' => get_null($header->SlpCode),
					'GroupNum' => $groupNum,
					'Pricelist' => $header->PriceList == 'x' ? -10 : $header->PriceList,
					'SpecialPriceList' => $header->SpecialPriceList,
					'NumAtCard' => get_null($header->NumAtCard),
					'DocCur' => $header->DocCur,
					'DocRate' => $header->DocRate,
					'DiscPrcnt' => $header->DiscPrcnt,
					'DiscSum' => $header->DiscSum,
					'DiscType' => $header->DiscType,
					'DocTotal' => $header->docTotal,
					'VatSum' => $header->totalVat,
					'PayToCode' => $header->PayToCode,
					'ShipToCode' => $header->ShipToCode,
					'isDefaultShipTo' => $header->isDefaultShipTo,
					'Address' => $header->BillTo,
					'Address2' => $header->ShipTo,
					'Address3' => get_null($header->exShipTo),
					'DocDate' => db_date($header->DocDate),
					'DocDueDate' => db_date($header->DocDueDate),
					'OwnerCode' => get_null($this->_user->emp_id),
					'Comments' => get_null($header->comments),
					'BillDate' => $header->billOption == 'Y' ? 1 : 0,
					'requireSQ' => $header->requireSQ == 'Y' ? 1 : 0,
					'isExport' => $header->isExport == 'Y' ? 1 : 0,
					'is_discount_sales' => $this->disSale ? $header->is_discount_sales : 0,
					'date_add' => now(),
					'user_id' => $this->_user->id,
					'uname' => $this->_user->uname,
					'area_id' => $header->areaId,
					'team_id' => $team_id,
					'term_id' => $header->term,
					'credit_issue' => $credit_issue,
					//'credit_approval' => $is_over_due == 1 ? 'O' : 'P',
					'credit_diff' => $credit_diff,
					'is_over_due' => $is_over_due,
					'isRegular' => $header->isRegular
				);

				$this->db->trans_begin();

				if ($this->orders_model->add($arr))
				{
					$row = 1;

					foreach ($details as $rs)
					{
						if ($sc === FALSE)
						{
							break;
						}

						$item = $header->PriceList == 'x' ? $this->item_model->get_item($rs->ItemCode) : $this->item_model->get($rs->ItemCode, $header->PriceList);

						if (! empty($item))
						{

							$sellPrice = $header->PriceList == 'x' ? $rs->SellPrice : ($rs->SellPrice == "" ? $item->price : $rs->SellPrice);
							$stdPrice = $header->PriceList == 'x' ? $rs->stdPrice : $item->price;

							if (! empty($header->VatGroup))
							{
								if ($header->VatRate > 0 && $sellPrice > 0)
								{
									$amount = $rs->Qty * $sellPrice;
									$rs->VatAmount = ($amount * $header->VatRate) / (100 + $header->VatRate);
								}
							}
							else
							{
								if ($item->Rate > 0 && $sellPrice > 0)
								{
									$amount = $rs->Qty * $sellPrice;
									$rs->VatAmount = ($amount * $item->Rate) / (100 + $item->Rate);
								}
							}

							$arr = array(
								'order_code' => $code,
								'LineNum' => $row,
								'ItemCode' => $item->code,
								'ItemName' => $item->name,
								'Qty' => $rs->Qty,
								'freeQty' => $rs->freeQty,
								'UomCode' => $item->uom,
								'stdPrice' => $stdPrice,
								'SellPrice' => $sellPrice,
								'VatGroup' => empty($header->VatGroup) ? $item->VatCode : $header->VatGroup,
								'VatRate' => empty($header->VatGroup) ? $item->Rate : $header->VatRate,
								'VatAmount' => $rs->VatAmount,
								'LineTotal' => $rs->Qty * $sellPrice,
								'isControl' => $rs->isControl,
								'discount_sales' => $this->disSale ? $rs->discount_sales : 0,
								'WhsCode' => empty($rs->WhsCode) ? $dfWhsCode : $rs->WhsCode,
								'step_rule_id' => $header->PriceList == 'x' ? NULL : $rs->step_id,
								'step_price_id' => $header->PriceList == 'x' ? $rs->step_id : NULL,
								'lineText' => get_null($rs->lineText)
							);

							if (! empty($rs->freeTxt))
							{
								$arr['FreeText'] = trim($rs->freeTxt);
							}

							if ($sellPrice < $rs->stdPrice)
							{
								$priceEdit = TRUE;
							}

							$id = $this->orders_model->add_detail($arr);

							if ($id != FALSE)
							{
								$row++;

								if ($rs->freeQty > 0)
								{
									$arr = array(
										'order_code' => $code,
										'LineNum' => $row,
										'ItemCode' => $item->code,
										'ItemName' => $item->name,
										'Qty' => $rs->freeQty,
										'UomCode' => $item->uom,
										'stdPrice' => $stdPrice,
										'SellPrice' => 0.00,
										'DiscPrcnt' => 100,
										'VatGroup' => empty($header->VatGroup) ? $item->VatCode : $header->VatGroup,
										'VatRate' => empty($header->VatGroup) ? $item->Rate : $header->VatRate,
										'VatAmount' => 0.00,
										'LineTotal' => 0.00,
										'isControl' => $rs->isControl,
										'discount_sales' => $this->disSale ? $rs->discount_sales : 0,
										'WhsCode' => empty($rs->WhsCode) ? $dfWhsCode : $rs->WhsCode,
										'lineText' => NULL,
										'free_item' => 1,
										'link_id' => $id
									);

									if ($this->orders_model->add_detail($arr))
									{
										$row++;
									}
									else
									{
										$sc = FALSE;
										$this->error = "Insert Free Item failed : {{$rs->ItemCode}}";
									}
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "Insert Item failed : {{$rs->ItemCode}}";
							}
						}
					} //--- end foreach details

					if($sc === TRUE)
					{
						$mustApprove = $mustApprove == TRUE ? TRUE : $this->must_approve($con_id, $header->docTotal, $priceEdit, $header->PriceList);

						//---- check order approval exception by rule
						if ($mustApprove === FALSE)
						{
							if (! $credit_issue)
							{
								$arr = array(
									"must_approve" => 0,
									'priceEdit' => $priceEdit === TRUE ? 1 : 0,
									'Approved' => 'A',
									'Approver' => 'System',
									'ApproveDate' => now()
								);

								if( ! $this->orders_model->update($code, $arr))
								{
									$sc = FALSE;
									$this->error = "Update Order failed : {{$code}}";
								}

								if($sc === TRUE && ! $this->orders_model->approve_details($code))
								{
									$sc = FALSE;
									$this->error = "Approve Order details failed : {{$code}}";
								}								
							}
							else
							{
								$arr = array(
									'priceEdit' => $priceEdit === TRUE ? 1 : 0
								);

								if( ! $this->orders_model->update($code, $arr))
								{
									$sc = FALSE;
									$this->error = "Update Order failed : {{$code}}";
								}
							}
						}
						else
						{
							$arr = array(
								"priceEdit" => $priceEdit === TRUE ? 1 : 0
							);

							if( ! $this->orders_model->update($code, $arr))
							{
								$sc = FALSE;
								$this->error = "Update Order failed : {{$code}}";
							}
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Insert Order failed";
				}

				if ($sc === TRUE)
				{
					$this->db->trans_commit();

					if($sc === TRUE && $mustApprove === FALSE && ! $credit_issue)
					{
						$this->doExport($code);
					}
				}
				else
				{
					$this->db->trans_rollback();
				}

				if ($sc === TRUE && ! empty($file))
				{
					$this->load->library('upload');					
					$path = $this->config->item('user_files_path') . 'order_po/';

					$config = array(
						'upload_path' => $path,
						'allowed_types' => 'jpg|jpeg|png|pdf',
						'file_name' => $code,
						'max_size' => 5120,
						'overwrite' => TRUE
					);

					$this->upload->initialize($config);

					if (! $this->upload->do_upload('uploadFile'))
					{
						$sc = FALSE;
						$up = 0;
						$this->error = "Create order success but upload file failed : " . $this->upload->display_errors();
					}
					else
					{
						$arr = array(
							'has_file' => 1,
							'file_name' => $this->upload->data('file_name'),
							'file_type' => $this->upload->data('file_ext'),
							'file_size' => $this->upload->data('file_size')
						);

						$this->orders_model->update($code, $arr);
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Details Data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Header Data";
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'code' => $sc === TRUE ? $code : NULL,
			'upload' => $up
		);

		echo json_encode($arr);
	}

	public function get_group_id($customer)
	{
		$max = 16;
		$group_id = 0;

		$i = 5;

		while ($i <= $max)
		{
			$query = "QryGroup" . $i;
			if ($customer->$query == 'Y')
			{
				$group_id = $i;
			}

			if ($group_id == $i)
			{
				break;
			}

			$i++;
		}

		return $group_id;
	}

	public function must_approve($con_id, $docTotal, $priceEdit = FALSE, $priceList = NULL)
	{
		$this->load->model('approve_rule_model');
		$mapl = [];

		if (! empty($priceList))
		{
			$mustApprovePriceList = getConfig('MUST_APPROVE_PRICE_LIST');

			if (! empty($mustApprovePriceList))
			{
				$map = explode(',', $mustApprovePriceList);

				if (count($map) > 0)
				{
					foreach ($map as $pid)
					{
						$pid = trim($pid);
						$mapl[$pid] = $pid;
					}
				}

				if (! empty($mapl[$priceList]))
				{
					return TRUE;
				}
			}
		}

		$rule = empty($con_id) ? NULL : $this->approve_rule_model->get_exception_rule($con_id, $docTotal, $priceEdit);
		//---- order must approve by default
		//--- if exception rule exists order no need to approve
		if (! empty($rule))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function check_approve()
	{
		$message = "pass";
		$docTotal = $this->input->get('docTotal');
		$priceEdit = $this->input->get('priceEdit') == 0 ? FALSE : TRUE;
		$saleTeam = $this->input->get('saleTeam');
		$areaId = $this->input->get('areaId');

		$con_id = $this->sales_team_condition_model->get_condition_id($saleTeam, $areaId);

		$this->load->model('approve_rule_model');

		$rule = empty($con_id) ? NULL : $this->approve_rule_model->get_exception_rule($con_id, $docTotal, $priceEdit);

		if (empty($rule))
		{
			if ($priceEdit)
			{
				$message = "ในกรณีราคาต่ำกว่า Price list ต้องผ่านการอนุมัติ";
			}
			else
			{
				$message = "ในกรณีจำนวนเงินสูงกว่าที่กำหนดต้องผ่านการอนุมัติ";
			}
		}

		echo $message;
	}

	public function get_current_state($doc)
	{
		/*
		# กรณีไม่ติดเครดิต
		------------------------------------------
		2 => ตรวจสอบเงื่อนไข => status = 0, credit_issue = 0, Approved = P
		3 => อนุมัติ => status IN(1, 2, 3) AND credit_issue = 0, Approved IN(A, S)

		# กรณีติดเครดิต แต่ไม่เกินกำหนด
		------------------------------------------
		2 => ตรวจสอบเครดิต => status = 0, credit_issue = 1, is_over_due = 0, credit_approval = O
		3 => พิจารณาเครดิต => status = 0, credit_issue = 1, is_over_due = 0, credit_approval = P
		4 => อนุมัติ => status = IN(1,2,3), credit_issue = 1, is_over_due = 0, credit_approval = A

		# กรณีติดเครดิต และเกินกำหนด
		------------------------------------------
		2 => ตรวจสอบเครดิต => status = 0, credit_issue = 1, is_over_due = 1, credit_approval = O, reply_status IS NULL
		3 => ขอหลักฐานการชำระเงิน => status = 0, credit_issue = 1, is_over_due = 1, credit_approval = O, reply_status = N
		4 => ตรวจสอบหลักฐานการชำระเงิน => status = 0, credit_issue = 1, is_over_due = 1, credit_approval = O, reply_status = R
		5 => พิจารณาเครดิต => status = 0, credit_issue = 1, is_over_due = 1, credit_approval = P
		6 => อนุมัติ => status IN(1,2,3), credit_issue = 1, is_over_due = 1, credit_approval = A
		*/

		$current_state = 2;

		if ($doc->Status == 0)
		{
			if (empty($doc->credit_issue) && $doc->Approved == 'P')
			{
				return 2; //--- waiting for approve
			}

			if(empty($doc->credit_issue) && ($doc->Approved == 'A' OR $doc->Approved == 'S'))
			{
				return 3; //--- Approved
			}
			
			if ($doc->credit_issue == 1 && $doc->is_over_due == 0)
			{
				if($doc->Approved == 'A' OR $doc->Approved == 'S')
				{
					return 4; //--- Approved
				}
				
				return $doc->credit_approval == 'P' ? 3 : ($doc->credit_approval == 'A' ? 4 : 2); //--- waiting for approve or approved
			}

			if ($doc->credit_issue == 1 && $doc->is_over_due == 1 && $doc->credit_approval == 'O')
			{
				$this->load->model('payment_request_model');
				$reply_status = $this->payment_request_model->get_reply_status_by_code($doc->code);
				return $reply_status == 'N' ? 3 : ($reply_status == 'R' ? 4 : 2); //--- waiting for approve or approved
			}

			if ($doc->credit_issue == 1 && $doc->is_over_due == 1 && $doc->credit_approval == 'P')
			{
				return 5; //--- waiting for approve
			}

			if ($doc->credit_issue == 1 && $doc->is_over_due == 1 && $doc->credit_approval == 'A')
			{
				return 6; //--- Approved
			}
		}

		if ($doc->Status == 1 OR $doc->Status == 2 OR $doc->Status == 3)
		{
			if (empty($doc->credit_issue) && ($doc->Approved == 'A' OR $doc->Approved == 'S'))
			{
				return 3; //--- Approved
			}

			if ($doc->credit_issue == 1 && $doc->is_over_due == 0)
			{
				return $doc->credit_approval == 'A' ? 4 : 3; //--- waiting for approve or approved
			}

			if($doc->credit_issue == 1 && $doc->is_over_due == 1)
			{
				return $doc->credit_approval == 'A' ? 6 : 5; //--- Approved
			}
		}

		return $current_state;
	}

	public function get_detail()
	{
		$sc = TRUE;

		$code = $this->input->get('code');

		if (! empty($code))
		{
			$doc = $this->orders_model->get($code);

			if (! empty($doc))
			{
				$this->load->model('payment_term_discount_model');

				$can_approve = ($doc->credit_issue == 1 && $doc->credit_approval != 'A') ? FALSE : $this->can_approve($doc);
				$current_state = $this->get_current_state($doc);
				$flow = ($doc->Status < 0 OR $doc->Approved == 'R') ? '' :  order_step_flow($current_state, is_true($doc->credit_issue), is_true($doc->is_over_due));

				$ds = array(
					'orderCode' => $doc->code,
					'user' => $doc->uname,
					'SONO' => $doc->DocNum,
					'DONO' => $doc->DeliveryNo,
					'INVNO' => $doc->InvoiceNo,
					'customerName' => $doc->CardCode . ' | ' . $doc->CardName,
					'billToCode' => $doc->PayToCode,
					'billToAddress' => $doc->Address,
					'shipToCode' => $doc->ShipToCode,
					'shipToAddress' => $doc->Address2,
					'exShipTo' => $doc->Address3,
					'currency' => $doc->DocCur,
					'currencyRate' => $doc->DocRate,
					'docDate' => thai_date($doc->DocDate, FALSE),
					'dueDate' => thai_date($doc->DocDueDate, FALSE),
					'PoNo' => $doc->NumAtCard,
					'fileName' => $doc->has_file ? '&nbsp;&nbsp; <span class="label label-info label-white pointer" onclick="openFile(\'' . $doc->file_name . '\')"><i class="fa fa-paperclip"></i>' . $doc->file_name . '</span>' : NULL,
					'PriceList' => order_price_list_name($doc->PriceList, $doc->SpecialPriceList),
					'termName' => term_name($doc->term_id),
					'billOption' => $doc->BillDate == 1 ? 'Y' : 'N',
					'requiredSQ' => $doc->requireSQ == 1 ? 'Y' : 'N',
					'isExport' => $doc->isExport == 1 ? 'Export' : 'Local',
					'remark' => $doc->Comments,
					'Approved' => $doc->Approved,
					'promotionCode' => $doc->promotion_code,
					'CanApprove' => $can_approve,
					'creditIssue' => ($doc->credit_issue == 1 && $doc->credit_approval != 'A'),
					'status' => $doc->Status,
					'isCancel' => $doc->isCancel == 1 ? TRUE : FALSE,
					'isReject' => $doc->isCancel == 0 && $doc->Approved == 'R' ? TRUE : FALSE,
					'cancel_by' => $doc->cancel_by,
					'cancel_at' => empty($doc->cancel_date) ? NULL : thai_date($doc->cancel_date, TRUE),
					'approval_status' => NULL,
					'items' => array(),
					'subTotal' => NULL,
					'flow' => $flow
				);

				if($doc->isCancel == 1)
				{
					$ds['approval_status'] = "ยกเลิกแล้ว";
					$ds['approval_color'] = "red";
					$ds['ApproveBy'] = "Cancelled by  {$doc->cancel_by}  @ " . thai_date($doc->cancel_date, TRUE);
				}				
				
				if ($doc->isCancel == 0 && $doc->Approved == 'R')
				{
					$ds['approval_status'] = "ไม่อนุมัติ";
					$ds['approval_color'] = "red";
					$ds['ApproveBy'] = "Rejected By  {$doc->Approver}  @ " . thai_date($doc->ApproveDate, TRUE);
				}				

				if ($doc->promotion_id)
				{
					$this->load->model('promotion_model');
					$pro = $this->promotion_model->get($doc->promotion_id);

					$ds['promotionName'] = $pro->name;
				}

				$details = $this->orders_model->get_details($code);
				$totalBefDi = 0;

				if (! empty($details))
				{
					$no = 1;
					foreach ($details as $rs)
					{
						$totalBefDi += $rs->LineTotal;

						if ($rs->free_item == 0)
						{
							$open_qty = (!empty($doc->DocNum) ? $this->orders_model->get_open_qty($doc->code, $rs->ItemCode) : ($rs->freeQty + $rs->Qty));
							$DoNo = (!empty($doc->DocNum) ? $this->orders_model->get_do_no($doc->code, $rs->ItemCode) : NULL);
							$Inv = (!empty($doc->DocNum) ? $this->orders_model->get_inv_no_and_date($doc->code, $rs->ItemCode) : NULL);
							$InvNo = NULL;
							$InvDate = NULL;
							if (! empty($Inv))
							{
								$ra = array();
								$rb = array();
								$i = 1;
								$e = 1;
								foreach ($Inv as $re)
								{
									if (! isset($ra[$re->DocNum]))
									{
										$ra[$re->DocNum] = $re->DocNum;
										$InvNo .= $i == 1 ? $re->DocNum : "<br/>" . $re->DocNum;
										$i++;

										$Y = date('Y', strtotime($re->DocDate));
										$m = date('m', strtotime($re->DocDate));
										$d = date('d', strtotime($re->DocDate));

										$date = $Y . $m . $d;

										if (!isset($rb[$date]))
										{
											$rb[$date] = $re->DocDate;
											$InvDate .= $e == 1 ? thai_date($re->DocDate, FALSE) : "<br/>" . thai_date($re->DocDate, FALSE);
											$e++;
										}
									}
								}
							}

							$arr = array(
								'id' => $rs->id,
								'itemName' => $rs->ItemName,
								'qty' => number($rs->Qty, 2),
								'free' => number($rs->freeQty, 2),
								'uom' => $rs->UomCode,
								'stdPrice' => number($rs->stdPrice, 2),
								'sellPrice' => number($rs->SellPrice, 2),
								'amount' => number($rs->LineTotal, 2),
								'dis' => $rs->discount_sales == 1 ? '<i class="fa fa-check blue"></i>' : '',
								'lineText' => $rs->LineText,
								'openQty' => round($open_qty, 2),
								'DoNo' => $DoNo,
								'InvNo' => $InvNo,
								'InvDate' => empty($InvNo) ? NULL : $InvDate,
								'checkbox' => get_checkbox($rs->id, $rs->status, $can_approve, $no), //--- orders_helper
								'rejectbox' => ($rs->status == 'P' ? get_rejectbox($rs->id, $rs->status, $can_approve, $no) : $rs->reject_text)
							);

							array_push($ds['items'], $arr);

							$no++;
						}
					}

					$ds['subTotal'] = array(
						'totalBefDi' => number($totalBefDi, 2),
						'DiscPrcnt' => $doc->DiscPrcnt,
						'DiscSum' => number($doc->DiscSum, 2),
						'totalBefVat' => number($doc->DocTotal - $doc->VatSum, 2),
						'totalVat' => number($doc->VatSum, 2),
						'docTotal' => number($doc->DocTotal, 2)
					);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Order Code : {$code}";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : Code";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}

	public function can_approve($order)
	{
		if ($this->isGM)
		{
			return TRUE;
		}

		$this->load->model('approve_rule_model');
		$this->load->model('approver_model');

		//---- อยู่ในรายชื่อที่มีสิทธิ์ อนุมัติมั้ย
		$approver = $this->sales_team_condition_model->get_approver($order->Condition_id, $this->_user->id);

		if (! empty($approver))
		{
			if ($approver->amount >= $order->DocTotal)
			{
				return  TRUE;
			}
		}

		return FALSE;
	}

	public function do_approve()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		if (! empty($code))
		{
			$order = $this->orders_model->get($code);
			$approval_status = 'F'; //--- F = full, P = partial

			if (! empty($order))
			{
				if ($order->must_approve == 1 && $order->Approved == 'P')
				{

					$items = json_decode($this->input->post('items'));

					if (! empty($items))
					{
						if ($this->can_approve($order))
						{
							foreach ($items as $item)
							{
								if ($item->status == "A")
								{
									$this->orders_model->approve_detail($item->id);
								}

								if ($item->status == "R")
								{
									$this->orders_model->reject_detail($item->id, get_null($item->reject_text));
									$approval_status = 'P';
								}
							}

							//--- approve order
							$arr = array(
								'Approved' => 'A',
								'Approver' => $this->_user->uname,
								'ApproveDate' => now(),
								'Approval_status' => $approval_status
							);

							if ($this->orders_model->update($code, $arr))
							{
								$this->doExport($code);
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Missing Approve Permission";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "No items found";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Order Status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid order code : {$code}";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		$this->_response($sc);
	}

	public function do_reject()
	{
		$sc = TRUE;
		$code = $this->input->post('code');

		if (! empty($code))
		{
			$order = $this->orders_model->get($code);

			if (! empty($order))
			{
				if ($order->must_approve == 1 && $order->Approved == 'P')
				{

					if ($this->can_approve($order))
					{
						$this->orders_model->reject_details($code);

						//--- approve order
						$arr = array(
							'Approved' => 'R',
							'Approver' => $this->_user->uname,
							'ApproveDate' => now(),
							'Approval_status' => 'R'
						);

						$this->orders_model->update($code, $arr);

						$items = json_decode($this->input->post('items'));
						if (! empty($items))
						{
							foreach ($items as $item)
							{
								$reject_text = get_null($item->reject_text);
								if (! empty($reject_text))
								{
									$arr = array('reject_text' => $item->reject_text);
									$this->orders_model->update_detail($item->id, $arr);
								}
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Missing Reject Permission";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Order Status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid order code : {$code}";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		$this->_response($sc);
	}

	public function get_authorizer()
	{
		$sc = TRUE;
		$ds = array();

		$code = $this->input->get('code');

		if (! empty($code))
		{
			$doc = $this->orders_model->get($code);
			$con_id = $doc->Condition_id;

			if (! empty($doc))
			{
				$approver = $this->sales_team_condition_model->get_condition_approver($con_id);

				if (! empty($approver))
				{
					foreach ($approver as $rs)
					{
						if ($rs->amount >= $doc->DocTotal)
						{
							$arr = array(
								'uname' => $rs->uname,
								'emp_name' => $rs->emp_name
							);

							array_push($ds, $arr);
						}
					}

					if (empty($ds))
					{
						$gm = $this->user_model->get_gm();

						if (! empty($gm))
						{
							foreach ($gm as $g)
							{
								$arr = array(
									'uname' => $g->uname,
									'emp_name' => $g->emp_name
								);

								array_push($ds, $arr);
							}
						}
					}
				}
				else
				{
					$arr = array('nodata' => 'nodata');
					array_push($ds, $arr);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Order Code : {$code}";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : Code";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}

	public function get_sale_name_by_customer()
	{
		$cardCode = trim($this->input->get('CardCode'));

		if (! empty($cardCode))
		{
			$saleName = $this->customer_model->get_sale_name_by_customer($cardCode);

			echo $saleName;
		}
	}

	public function get_new_code($date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : $date;
		$Y = date('y', strtotime($date));
		$M = date('m', strtotime($date));
		$prefix = getConfig('PREFIX_ORDER');
		$run_digit = getConfig('RUN_DIGIT_ORDER');
		$pre = $prefix . '-' . $Y . $M;
		$code = $this->orders_model->get_max_code($pre);
		if (! empty($code))
		{
			$run_no = mb_substr($code, ($run_digit * -1), NULL, 'UTF-8') + 1;
			$new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', $run_no);
		}
		else
		{
			$new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', '001');
		}

		return $new_code;
	}

	public function doExport($code)
	{
		$sc = TRUE;

		$this->load->library('export');

		if (!$this->export->export_order($code))
		{
			$sc = FALSE;
			$this->error = $this->export->error;
		}

		return $sc;
	}

	public function get_temp_data()
	{
		$code = $this->input->get('code'); //--- U_WEBORDER

		$data = $this->orders_model->get_temp_data($code);

		if (! empty($data))
		{
			//$btn = "<button type='button' class='btn btn-sm btn-danger' onClick='removeTemp()'' ><i class='fa fa-trash'></i> Delete Temp</button>";

			$status = 'Pending';

			if ($data->F_Sap === NULL)
			{
				$status = "Pending";
			}
			else if ($data->F_Sap === 'N')
			{
				$status = "Failed";
			}
			else if ($data->F_Sap === 'Y')
			{
				$so = $this->orders_model->get_sap_order($code);

				if (! empty($so))
				{
					$status = "Success";
				}
				else
				{
					$status = "Not Found";
				}
			}


			$arr = array(
				'U_WEB_ORNO' => $data->U_WEB_ORNO,
				'CardCode' => $data->CardCode,
				'CardName' => $data->CardName,
				'F_WebDate' => thai_date($data->F_WebDate, TRUE),
				'F_SapDate' => empty($data->F_SapDate) ? '-' : thai_date($data->F_SapDate, TRUE),
				'F_Sap' => $status, //$data->F_Sap === 'Y' ? 'Success' : ($data->F_Sap === 'N' ? 'Failed' : 'Pending'),
				'Message' => empty($data->Message) ? '' : $data->Message,
				'del_btn' => ($status === "Pending" or $status === "Failed") ? 'ok' : ''
			);

			echo json_encode($arr);
		}
		else
		{
			echo 'No data found';
		}
	}

	public function remove_temp()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$temp = $this->orders_model->get_temp_status($code);

		if (empty($temp))
		{
			$sc = FALSE;
			$this->error = "Temp data not exists";
		}
		else if ($temp->F_Sap === 'Y')
		{
			$sc = FALSE;
			$this->error = "Delete Failed : Temp Data already in SAP";
		}

		if ($sc === TRUE)
		{
			if (! $this->orders_model->drop_temp_exists_data($temp->DocEntry))
			{
				$sc = FALSE;
				$this->error = "Delete Failed : Delete Temp Failed";
			}
			else
			{
				$arr = array(
					'Status' => 0,
					'DocNum' => NULL,
					'Message' => NULL,
					'sap_date' => NULL,
					'temp_date' => NULL
				);

				$this->orders_model->update($code, $arr);
			}
		}


		$this->_response($sc);
	}

	public function cancel_order()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$temp = $this->orders_model->get_temp_status($code);

		if (empty($temp))
		{
			$sc = FALSE;
			$this->error = "Temp data not exists";
		}
		else if ($temp->F_Sap === 'Y')
		{
			$sc = FALSE;
			$this->error = "Delete Failed : Temp Data already in SAP";
		}

		if ($sc === TRUE)
		{
			if (! $this->orders_model->drop_temp_exists_data($temp->DocEntry))
			{
				$sc = FALSE;
				$this->error = "Delete Failed : Delete Temp Failed";
			}
			else
			{
				$arr = array(
					'Status' => -1,
					'DocNum' => NULL,
					'Message' => NULL,
					'sap_date' => NULL,
					'temp_date' => NULL,
					'isCancel' => 1,
					'cancel_by' => $this->_user->uname,
					'cancel_date' => now()
				);

				$this->orders_model->update($code, $arr);
			}
		}


		$this->_response($sc);
	}

	public function sendToSAP()
	{
		$sc = TRUE;

		$code = $this->input->post('code');

		if (! empty($code))
		{
			$rs = $this->doExport($code);

			if (!$rs)
			{
				$sc = FALSE;
			}
		}

		$this->_response($sc);
	}

	public function open_file($filename)
	{		
		$path = $this->config->item('user_files_path') . 'order_po/' . $filename;

		if (!file_exists($path))
		{
			show_404();
			return;
		}

		// Detect MIME type automatically
		$mime = mime_content_type($path);

		// Force browser to display file inline
		header('Content-Type: ' . $mime);
		header('Content-Disposition: inline; filename="' . $filename . '"');
		header('Content-Length: ' . filesize($path));

		readfile($path);
	}

	public function clear_filter()
	{
		$filter = array(
			'so_WebCode',
			'so_DocNum',
			'so_DeliveryNo',
			'so_InvoiceNo',
			'so_PoNo',
			'so_CardCode',
			'so_user_id',
			'so_Approver',
			'so_Approved',
			'doc_status',
			'SO_Status',
			'DO_Status',
			'INV_Status',
			'so_fromDate',
			'so_toDate',
			'so_is_promotion',
			'is_discount_sales',
			'so_credit_issue',
			'so_is_over_due',
			'so_is_export'
		);

		clear_filter($filter);

		echo 'done';
	}
} //--- end class
