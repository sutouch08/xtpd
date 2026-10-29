<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends PS_Controller
{
	public $title = 'Welcome';
	public $menu_code = '';
	public $menu_group_code = '';
	public $error;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('report/order_report_model');
	}


	public function index()
	{
		$this->pm->can_view = 1;
		$ds = array();

		if(!empty($this->_user->id))
		{

			$fromDate = date('01-m-Y');
			$toDate = date('t-m-Y');
			$user_id = $this->_user->id;
			$ds['sumOrdered'] = round($this->order_report_model->get_sum_order($user_id, $fromDate, $toDate), 2);
			$ds['sumPending'] = round($this->order_report_model->get_sum_pending($user_id, $fromDate, $toDate), 2);
			$ds['sumApproved'] = round($this->order_report_model->get_sum_approved($user_id, $fromDate, $toDate), 2);
			$ds['sumRejected'] = round($this->order_report_model->get_sum_rejected($user_id, $fromDate, $toDate), 2);
			$ds['bi_link'] = $this->_user->bi_link == 1 ? $this->user_model->get_bi_link($this->_user->sale_id) : NULL;
		}


		$this->load->view('main_view', $ds);
	}



	public function get_summary()
	{

		$sc = TRUE;
		$ds = array();
		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$user_id = $this->_user->id;

		if(!empty($user_id))
		{
			$ds['sumOrdered'] = number($this->order_report_model->get_sum_order($user_id, $fromDate, $toDate), 2);
			$ds['sumPending'] = number($this->order_report_model->get_sum_pending($user_id, $fromDate, $toDate), 2);
			$ds['sumApproved'] = number($this->order_report_model->get_sum_approved($user_id, $fromDate, $toDate), 2);
			$ds['sumRejected'] = number($this->order_report_model->get_sum_rejected($user_id, $fromDate, $toDate), 2);
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบ Sales Employee";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function get_details()
	{
		$sc = TRUE;
		$ds = array();

		$fromDate = $this->input->get('fromDate');
		$toDate = $this->input->get('toDate');
		$status = $this->input->get('status');
		$user_id = $this->_user->id;

		if(!empty($user_id))
		{
			$report = $this->order_report_model->get_report_by_user_id($user_id, $fromDate, $toDate, $status);

			if(!empty($report))
			{
				$no = 1;
				// $priceListName = $this->user_model->get_all_price_list_array();				
				$terms = $this->user_model->get_all_payment_term_array();
				$totalQty = 0;
				$totalAmount = 0;

				foreach($report as $rs)
				{

					$arr = array(
						'no' => $no,
						'DocDate' => thai_date($rs->DocDate, FALSE),
						'code' => $rs->code,
						'SONo' => $rs->DocNum,
						'paymentTerm' => empty($terms[$rs->GroupNum]) ? "-" : $terms[$rs->GroupNum],
						'CardCode' => $rs->CardCode,
						'CardName' => $rs->CardName,
						'ItemCode' => $rs->ItemCode,
						'ItemName' => $rs->ItemName,
						'Qty' => number($rs->Qty, 2),
						'stdPrice' => number($rs->stdPrice, 2),
						'SellPrice' => number($rs->SellPrice, 2),
						'DiscPrcnt' => $rs->DiscPrcnt.' %',
						'Amount' => number($rs->LineTotal, 2),
						'PoNo' => $rs->NumAtCard,
						'Approver' => $rs->Approver,
						'Approval_status' => $this->approval_status_label($rs->Approved, $rs->Approval_status),
						'DoNo' => $rs->DeliveryNo,
						'InvNo' => $rs->InvoiceNo,
						'InvoiceDate' => !empty($rs->InvoiceDate) ? thai_date($rs->InvoiceDate) : NULL
					);

					array_push($ds, $arr);
					$no++;
					$totalQty += $rs->Qty;
					$totalAmount += $rs->LineTotal;
				}

				$arr = array(
					'totalQty' => number($totalQty, 2),
					'totalAmount' => number($totalAmount, 2)
				);

				array_push($ds, $arr);
			}
			else
			{
				$arr = array("nodata" => "nodata");
				array_push($ds, $arr);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบ Sales Employee";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function approval_status_label($approval, $approval_status)
	{
		if($approval == 'A' && $approval_status == 'P')
		{
			return "อนุมัติบางส่วน";
		}
		else
		{
			if($approval == 'A')
			{
				return "อนุมัติ";
			}

			if($approval == 'P')
			{
				return "รออนุมัติ";
			}

			if($approval == 'R')
			{
				return "ไม่อนุมัติ";
			}
		}

		return NULL;
	}


} //--- end class
