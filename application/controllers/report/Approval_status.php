<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
				'customerFrom' => trim($this->input->get('customerFrom')),
				'customerTo' => trim($this->input->get('customerTo')),
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
					$arr = array(
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
			$this->error = "Missing Form Data";
		}

		echo $sc === TRUE ? json_encode($ds) : $this->error;

	} //--- end function




	public function do_export()
	{
		if($this->input->post())
		{
			$ds = array(
				'fromDate' => $this->input->post('fromDate'),
				'toDate' => $this->input->post('toDate'),
				'allCustomer' => $this->input->post('allCustomer') == 0 ? FALSE : TRUE,
				'customerFrom' => trim($this->input->post('customerFrom')),
				'customerTo' => trim($this->input->post('customerTo')),
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

			//---  Report title
			$report_title = 'รายงานออเดอร์ แยกตามลูกค้า แสดงรายการสินค้า วันที่ ' . thai_date($ds['fromDate'],'/') .' ถึง '.thai_date($ds['toDate'], '/');
			$customer_title = 'ลูกค้า : '.($ds['allCustomer'] === TRUE ? 'ทั้งหมด' : $ds['customerFrom']."  -  ".$ds['customerTo']);
			$grouplist = 'กลุ่มลูกค้า : '.($ds['allCustomerGroup'] === TRUE ? 'ทั้งหมด' : $group_title);
			$approver  = 'ผู้อนุมัติ :  '. ($ds['allApprover']? 'ทั้งหมด' : $approver_title);

			//--- load excel library
			$this->load->library('excel');

			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Order List');

			//--- set report title header
			$this->excel->getActiveSheet()->setCellValue('A1', $report_title);
			$this->excel->getActiveSheet()->mergeCells('A1:Q1');
			$this->excel->getActiveSheet()->setCellValue('A2', $customer_title);
			$this->excel->getActiveSheet()->mergeCells('A2:Q2');
			$this->excel->getActiveSheet()->setCellValue('A3', $grouplist);
			$this->excel->getActiveSheet()->mergeCells('A3:Q3');
			$this->excel->getActiveSheet()->setCellValue('A4', $approver);
			$this->excel->getActiveSheet()->mergeCells('A4:Q4');
			$this->excel->getActiveSheet()->setCellValue('A5', 'วันที่ : ('.thai_date($ds['fromDate'],'/') .') - ('.thai_date($ds['toDate'],'/').')');
			$this->excel->getActiveSheet()->mergeCells('A5:Q5');

			//--- set Table header


			$this->excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B6', 'วันที่ออเดอร์');
			$this->excel->getActiveSheet()->setCellValue('C6', 'เลขที่ออเดอร์');
			$this->excel->getActiveSheet()->setCellValue('D6', 'Price List');
			$this->excel->getActiveSheet()->setCellValue('E6', 'Payment Term');
			$this->excel->getActiveSheet()->setCellValue('F6', 'รหัสลูกค้า');
			$this->excel->getActiveSheet()->setCellValue('G6', 'ชื่อลูกค้า');
			$this->excel->getActiveSheet()->setCellValue('H6', 'รายการสินค้า');
			$this->excel->getActiveSheet()->setCellValue('I6', 'ราคา/หน่วย');
			$this->excel->getActiveSheet()->setCellValue('J6', 'ราคาพิเศษ/หน่วย');
			$this->excel->getActiveSheet()->setCellValue('K6', 'ส่วนลด');
			$this->excel->getActiveSheet()->setCellValue('L6', 'จำนวน');
			$this->excel->getActiveSheet()->setCellValue('M6', 'มูลค่า');
			$this->excel->getActiveSheet()->setCellValue('N6', 'ผู้อนุมัติ');
			$this->excel->getActiveSheet()->setCellValue('O6', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('P6', 'เลขที่ PO');
			$this->excel->getActiveSheet()->setCellValue('Q6', 'เลขที่ SO');
			$this->excel->getActiveSheet()->setCellValue('R6', 'เลขที่ DO');
			$this->excel->getActiveSheet()->setCellValue('S6', 'เลขที่ Invoice');
			$this->excel->getActiveSheet()->setCellValue('T6', 'วันที่ Invoice');


			//---- กำหนดความกว้างของคอลัมภ์
	    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
	    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	    $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
	    $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
	    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
	    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
	    $this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(20);

			$row = 7;

			if(!empty($report))
			{
				$no = 1;

				$priceListName = $this->user_model->get_all_price_list_array();
				$paymentTermName = $this->user_model->get_all_payment_term_array();

				foreach($report as $rs)
				{
					$y		= date('Y', strtotime($rs->DocDate));
	        $m		= date('m', strtotime($rs->DocDate));
	        $d		= date('d', strtotime($rs->DocDate));
	        $date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);

					$pName = empty($rs->PriceList) ? NULL : ($rs->PriceList == -10 ? $this->special_price_list_model->get_name($rs->SpecialPriceList) : $priceListName[$rs->PriceList]);
					$tName = empty($paymentTermName[$rs->GroupNum]) ? NULL : $paymentTermName[$rs->GroupNum];

	        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, $date);
	        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $pName);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $tName);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->CardCode);
	        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->CardName);
	        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->ItemName);
	        $this->excel->getActiveSheet()->setCellValue('I'.$row, $rs->stdPrice);
	        $this->excel->getActiveSheet()->setCellValue('J'.$row, $rs->SellPrice);
	        $this->excel->getActiveSheet()->setCellValue('K'.$row, $rs->DiscPrcnt);
	        $this->excel->getActiveSheet()->setCellValue('L'.$row, round($rs->Qty, 2));
	        $this->excel->getActiveSheet()->setCellValue('M'.$row, round($rs->LineTotal, 2));
	        $this->excel->getActiveSheet()->setCellValue('N'.$row, $rs->Approver);
	        $this->excel->getActiveSheet()->setCellValue('O'.$row, $this->approval_status_label($rs->Approved, $rs->Approval_status));
	        $this->excel->getActiveSheet()->setCellValue('P'.$row, $rs->NumAtCard);
	        $this->excel->getActiveSheet()->setCellValue('Q'.$row, $rs->DocNum);
	        $this->excel->getActiveSheet()->setCellValue('R'.$row, $rs->DeliveryNo);
	        $this->excel->getActiveSheet()->setCellValue('S'.$row, $rs->InvoiceNo);
	        $this->excel->getActiveSheet()->setCellValue('T'.$row, !empty($rs->InvoiceDate) ? thai_date($rs->InvoiceDate) : "");

					$row++;
					$no++;
				}

				$this->excel->getActiveSheet()->getStyle('I7:K'.$row)->getNumberFormat()->setFormatCode("#,##0.00");
				$this->excel->getActiveSheet()->getStyle('L7:M'.$row)->getNumberFormat()->setFormatCode("#,##0.00");
				$this->excel->getActiveSheet()->getStyle('I6:M6')->getAlignment()->setHorizontal('right');
			}

			setToken($token);

	    $file_name = "Report Sales Order.xlsx";
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	    header('Content-Disposition: attachment;filename="'.$file_name.'"');
	    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
	    $writer->save('php://output');
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
