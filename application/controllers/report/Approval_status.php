<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class Approval_status extends PS_Controller
{
	public $menu_code = 'APPROVE_STATUS';
	public $menu_group_code = 'REPORT';
	public $title = 'Report Approval';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/approval_status';
		$this->load->model('approver_model');
		$this->load->model('report/order_report_model');
		$this->load->model('special_price_list_model');
  }

	public function index()
  {
		$ds['groupList'] = $this->user_model->get_customer_group_list();
		$ds['approverList'] = $this->approver_model->get_all_approver();
    $this->load->view('report/order_approval', $ds);
  }

	public function get_report()
	{
		$sc = TRUE;

		if($this->input->get())
		{
			$ds = array();

			$arr = array(
				'fromDate' => $this->input->get('fromDate'),
				'toDate' => $this->input->get('toDate'),
				'allCustomer' => $this->input->get('allCustomer') == 0 ? FALSE : TRUE,
				'customerFrom' => trim($this->input->get('customerFrom') ?? ''),
				'customerTo' => trim($this->input->get('customerTo') ?? ''),
				'allCustomerGroup' => $this->input->get('allCustomerGroup') == 0 ? FALSE : TRUE,
				'groupList' => $this->input->get('groupList'),
				'allApprover' => $this->input->get('allApprover') == 0 ? FALSE : TRUE,
				'approverList' => $this->input->get('approver'),
				'approval_status' => $this->input->get('approval_status')
			);

			$report = $this->order_report_model->get_report($arr);

			if(!empty($report))
			{
				$no = 1;
				$priceListName = $this->user_model->get_all_price_list_array();
				$paymentTermName = $this->user_model->get_all_payment_term_array();

				$totalQty = 0;
				$totalAmount = 0;

				foreach($report as $rs)
				{
					$ds[] = array(
						'no' => $no,
						'DocDate' => thai_date($rs->DocDate, FALSE),
						'code' => $rs->code,
						'SONo' => $rs->DocNum,
						'priceList' => empty($rs->PriceList) ? NULL : ($rs->PriceList == -10 ? $this->special_price_list_model->get_name($rs->SpecialPriceList) : $priceListName[$rs->PriceList]),
						'paymentTerm' => empty($paymentTermName[$rs->GroupNum]) ? NULL : $paymentTermName[$rs->GroupNum],
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
					
					$no++;
					$totalQty += $rs->Qty;
					$totalAmount += $rs->LineTotal;
				}

				$ds[] = array(
					'totalQty' => number($totalQty, 2),
					'totalAmount' => number($totalAmount, 2)
				);				
			}
			else
			{
				$ds[] = array("nodata" => "nodata");
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Form Data";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}


	public function do_export()
	{
		if($this->input->post())
		{
			$ds = array(
				'fromDate' => $this->input->post('fromDate'),
				'toDate' => $this->input->post('toDate'),
				'allCustomer' => $this->input->post('allCustomer') == 0 ? FALSE : TRUE,
				'customerFrom' => trim($this->input->post('customerFrom') ?? ''),
				'customerTo' => trim($this->input->post('customerTo') ?? ''),
				'allCustomerGroup' => $this->input->post('allCustomerGroup') == 0 ? FALSE : TRUE,
				'groupList' => $this->input->post('groupList'),
				'allApprover' => $this->input->post('allApprover') == 0 ? FALSE : TRUE,
				'approverList' => $this->input->post('approver'),
				'approval_status' => $this->input->post('approval_status')
			);

			$token = $this->input->post('token');
			$report = $this->order_report_model->get_report($ds);
			$groupName = array();

			if($ds['allCustomerGroup'] == FALSE)
			{
				$customerGroup = $this->user_model->get_customer_group_list();

				if(!empty($customerGroup))
				{
					foreach($customerGroup as $rs)
					{
						$groupName[$rs->GroupCode] = $rs->GroupName;
					}
				}

				$group_title = "";

				if(!empty($ds['groupList']))
				{
					$i = 1;
					foreach($ds['groupList'] as $group_id)
					{
						$group_title .= $i === 1 ? $groupName[$group_id] : ", ".$groupName[$group_id];
						$i++;
					}
				}
			}

			$approver_title = "";
			if(!empty($ds['approverList']))
			{
				$i = 1;
				foreach($ds['approverList'] as $uname)
				{
					$approver_title .= $i == 1 ? $uname :', '.$uname;
					$i++;
				}
			}

			$tempFile = tempnam(sys_get_temp_dir(), 'order_report_');
			$fileName = 'Report Sales Order - '.date('Ymdhis').'.xlsx';

			$header = [
				'ลำดับ',
				'วันที่ออเดอร์',
				'เลขที่ออเดอร์',
				'Price List',
				'Payment Term',
				'รหัสลูกค้า',
				'ชื่อลูกค้า',
				'รายการสินค้า',
				'ราคา/หน่วย',
				'ราคาพิเศษ/หน่วย',
				'ส่วนลด',
				'จำนวน',
				'มูลค่า',
				'ผู้อนุมัติ',
				'สถานะ',
				'เลขที่ PO',
				'เลขที่ SO',
				'เลขที่ DO',
				'เลขที่ Invoice',
				'วันที่ Invoice'
			];					

			//---  Report title
			$report_title = 'รายงานออเดอร์ แยกตามลูกค้า แสดงรายการสินค้า วันที่ ' . thai_date($ds['fromDate'],'/') .' ถึง '.thai_date($ds['toDate'], '/');
			$customer_title = 'ลูกค้า : '.($ds['allCustomer'] === TRUE ? 'ทั้งหมด' : $ds['customerFrom']."  -  ".$ds['customerTo']);
			$grouplist = 'กลุ่มลูกค้า : '.($ds['allCustomerGroup'] === TRUE ? 'ทั้งหมด' : $group_title);
			$approver  = 'ผู้อนุมัติ :  '. ($ds['allApprover']? 'ทั้งหมด' : $approver_title);
			$date_range = 'วันที่ : ('.thai_date($ds['fromDate'],'/') .') - ('.thai_date($ds['toDate'],'/').')';

			try 
			{				
				$writer = new Writer();
				$writer->openToFile($tempFile);				
				$writer->addRow(Row::fromValues([$report_title]));
				$writer->addRow(Row::fromValues([$customer_title]));
				$writer->addRow(Row::fromValues([$grouplist]));
				$writer->addRow(Row::fromValues([$approver]));
				$writer->addRow(Row::fromValues([$date_range]));
				$writer->addRow(Row::fromValues([' ']));
				$writer->addRow(Row::fromValues($header));
				$sheet = $writer->getCurrentSheet();				
				$sheet->setColumnWidth(5, 1);
				$sheet->setColumnWidth(15, 2);
				$sheet->setColumnWidth(20, 3);
				$sheet->setColumnWidth(20, 4);
				$sheet->setColumnWidth(20, 5);
				$sheet->setColumnWidth(40, 7);
				$sheet->setColumnWidth(10, 8);
				$sheet->setColumnWidth(15, 9);
				$sheet->setColumnWidth(20, 10);
				$sheet->setColumnWidth(15, 11);
				$sheet->setColumnWidth(15, 12);
				$sheet->setColumnWidth(15, 13);
				$sheet->setColumnWidth(20, 14);
				$sheet->setColumnWidth(15, 15);
				$sheet->setColumnWidth(20, 16);
				$sheet->setColumnWidth(20, 17);
				$sheet->setColumnWidth(20, 18);
				$sheet->setColumnWidth(20, 19);
				$sheet->setColumnWidth(20, 20);

				if(!empty($report))
				{
					$no = 1;
					$priceListName = $this->user_model->get_all_price_list_array();
					$paymentTermName = $this->user_model->get_all_payment_term_array();

					foreach($report as $rs)
					{
						$pName = empty($rs->PriceList) ? NULL : ($rs->PriceList == -10 ? $this->special_price_list_model->get_name($rs->SpecialPriceList) : $priceListName[$rs->PriceList]);
						$tName = empty($paymentTermName[$rs->GroupNum]) ? NULL : $paymentTermName[$rs->GroupNum];

						$rowData = [
							$no,
							thai_date($rs->DocDate),
							$rs->code,
							$pName,
							$tName,
							$rs->CardCode,
							$rs->CardName,
							$rs->ItemName,
							$rs->stdPrice,
							$rs->SellPrice,
							$rs->DiscPrcnt,
							round($rs->Qty, 2),
							round($rs->LineTotal, 2),
							$rs->Approver,
							$this->approval_status_label($rs->Approved, $rs->Approval_status),
							$rs->NumAtCard,
							$rs->DocNum,
							$rs->DeliveryNo,
							$rs->InvoiceNo,
							! empty($rs->InvoiceDate) ? thai_date($rs->InvoiceDate) : ""
						];

						$writer->addRow(Row::fromValues($rowData));
						$no++;
					}					
				}

				$writer->close();
				setToken($token);

				while(ob_get_level() > 0)
				{
					ob_end_flush();
				}

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment; filename="'.$fileName.'"');
				header('Cache-Control: max-age=0');
				readfile($tempFile);				
			}
			finally
			{
				if(file_exists($tempFile))
				{
					unlink($tempFile);
				}
			}

			exit;													
		}
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



	public function get_customer_code_and_name()
	{
		$txt = trim($_REQUEST['term']);
		$sc = array();

		$qr  = "SELECT CardCode, CardName FROM OCRD ";
		$qr .= "WHERE CardType = 'C' ";

		if($txt !== '*')
    {
      $qr .= "AND (CardCode LIKE N'%{$this->ms->escape_str($txt)}%' OR CardName LIKE N'%{$this->ms->escape_str($txt)}%') ";
    }

		$qr .= "ORDER BY CardCode ASC OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

		$qs = $this->ms->query($qr);

		if($qs->num_rows() > 0)
		{
			foreach($qs->result() as $rs)
			{
				$sc[] = $rs->CardCode.' | '.$rs->CardName;
			}
		}
		else
		{
			$sc[] = "not found";
		}


		echo json_encode($sc);
	}
} // end class


 ?>
