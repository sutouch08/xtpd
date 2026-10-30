<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class Customer_status extends PS_Controller
{
	public $menu_code = 'CUST_STATUS';
	public $menu_group_code = 'REPORT';
	public $title = 'Report Customer Status';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/customer_status';		
		$this->load->model('report/customer_status_model');		
		$this->load->helper('customer');
  }

	public function index()
  {		
    $this->load->view('report/customer_status');
  }

	public function send_request()
	{		
		$CardCode = $this->input->post('customerCode');
		$CardName = $this->input->post('customerName');		
		$paymentMethod = $this->input->post('paymentMethod');		
		$BillingDate = $this->input->post('billingText');
		$PaymentDate = $this->input->post('paymentText');
		$CreateDate = $this->input->post('createDate');
		$Duration = $this->input->post('duration');
		$InvoiceCount = $this->input->post('invoiceCount');

		$this->load->library('Mail');

		$to = getConfig('RECEIVER_EMAIL');
		$subject = "Customer Status Request";
		$message = "<p>";
		$message .= "Customer Code: {$CardCode} <br>";
		$message .= "Customer Name: {$CardName} <br>";
		$message .= "วันที่สร้าง (Create Date): {$CreateDate} <br>";
		$message .= "ระยะเวลา (Duration): {$Duration} <br>";
		$message .= "เปิดบิลแล้ว (Invoice Count): {$InvoiceCount} <br>";
		$message .= "วิธีการชำระเงิน (Payment Method): {$paymentMethod} <br>";
		$message .= "เงื่อนไขการรับวางบิล: {$BillingDate} <br>";
		$message .= "รอบการชำระเงิน: {$PaymentDate} <br>";
		$message .= "<br>";
		$message .= "----------------------------- <br>";
		$message .= "<br>";
		$message .= "ผู้ส่งคำขอ (Requester): " . $this->_user->emp_name . "<br>";
		$message .= "</p>";

		$attachments = [];
		$cc = [];
		if($this->_user->email)
		{
			$cc[] = $this->_user->email;
		}

		$result = $this->mail->send($to, $subject, $message, $attachments, $cc);

		$arr = array(
			'status' => $result ? 'success' : 'error',
			'message' => $result ? 'Request sent!' : 'Failed to send request.'
		);

		echo json_encode($arr);	
	}

	public function sendmail()
	{
		$this->load->library('Mail');

		$to = getConfig('RECEIVER_EMAIL');
		$subject = "Test email with CC + attachment";
		$message = "<h3>Hello</h3><p>This email contains CC and attachments.</p>";

		$attachments = [
			FCPATH . "uploads/file.pdf",
			FCPATH . "uploads/image.jpg"
		];

		$cc = [
			"manager@example.com",
			"audit@example.com"
		];		

		$result = $this->mail->send($to, $subject, $message, $attachments, $cc);

		echo $result ? "Email sent!" : "Failed to send email.";
	}


	public function get_report()
	{		
		$ds = [];		
		$isAdmin = $this->isAdmin || $this->_SuperAdmin;
		$list = $isAdmin ? $this->customer_status_model->get_all_user_customer_list() : $this->customer_status_model->get_user_customer_list($this->_user->area_id);		

		if(!empty($list))
		{
			$no = 1;
			$today = date('Y-m-d');
			foreach($list as $rs)
			{
				$invoiceCount = $this->customer_status_model->count_customer_invoice($rs->CardCode);

				if($invoiceCount > 2)
				{
					$ds[] = (object) array(
						'no' => $no,
						'id' => $rs->id,
						'CardCode' => $rs->CardCode,
						'CardName' => $rs->CardName,
						'CreateDate' => thai_date($rs->CreateDate, FALSE),
						'Duration' => number(date_diff(date_create($rs->CreateDate), date_create($today))->days),
						'InvoiceCount' => number($invoiceCount)
					);

					$no++;					
				}
			}
		}
		else 
		{
			$ds[] = array('nodata' => 'nodata');
		}		

		echo json_encode($ds);

	} //--- end function

	public function do_export()
	{
		$token = $this->input->post('token');		
		$report_title = 'รายชื่อลูกค้าไม่ประจำที่มีอายุเกิน 3 เดือนและเคยเปิด invoice แล้วอย่างน้อย 3 ใบ ณ วันที่ ' . date('d-m-Y');
		$isAdmin = $this->isAdmin || $this->_SuperAdmin;
		$list = $isAdmin ? $this->customer_status_model->get_all_user_customer_list() : $this->customer_status_model->get_user_customer_list($this->_user->area_id);

		try
		{
			$tempFile = tempnam(sys_get_temp_dir(), 'customer_status_');
			$fileName = 'Customer Status Report - '.date('Ymdhis').'.xlsx';

			$writer = new Writer();
			$writer->openToFile($tempFile);
			$writer->addRow(Row::fromValues([$report_title]));
			$writer->addRow(Row::fromValues([' ']));
			$header = ['#', 'Code', 'Name', 'Create Date', 'Duration (days)', 'Invoice Count'];
			$writer->addRow(Row::fromValues($header));
			$sheet = $writer->getCurrentSheet();
			$sheet->setColumnWidth(5, 1); // set width for column A
			$sheet->setColumnWidth(15, 2); // set width for column B
			$sheet->setColumnWidth(60, 3); // set width for column C
			$sheet->setColumnWidthForRange(15, 4, 6); // set width for column D to F

			if(!empty($list))
			{
				$no = 1;
				$today = date('Y-m-d');

				foreach($list as $rs)
				{
					$invoiceCount = $this->customer_status_model->count_customer_invoice($rs->CardCode);

					if($invoiceCount > 2)
					{
						$rowData = [
							$no,
							$rs->CardCode,
							$rs->CardName,
							thai_date($rs->CreateDate, FALSE),
							number(date_diff(date_create($rs->CreateDate), date_create($today))->days),
							number($invoiceCount)
						];

						$writer->addRow(Row::fromValues($rowData));
						$no++;
					}
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
			readfile($tempFile);
			unlink($tempFile);
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
