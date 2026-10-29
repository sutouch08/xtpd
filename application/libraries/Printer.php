<?php
class Printer
{
	public $page;
	public $total_page		= 1;
	public $current_page	= 1;
	public $page_width 	= 200;
	public $page_height	= 287;
	public $content_width	= 190;
	public $row	= 10; //--- items rows perpage

	//---- top header logo and doc name
	public $title = "";
	public $title_position = "right"; //--- left or middle or right
	public $has_logo = TRUE;
	public $logo_position = "left"; //-- left or middle or right
	public $cancle_watermark = "";


	public $header_rows = 4;
	public $sub_total_row	= 2;
	public $footer_row		= 4;
	public $ex_row			= 0;
	public $total_row		= 16;
	public $row_height 	= 10;
	public $font_size 		= 12;
	public $text_color = "";

	public $title_size 		= "h4";
	public $content_border = 2;
	public $pattern			= array();
	public $footer			= true;
	public $custom_header = '';

	public $header_row	= array();

	public $sub_header	= "";


	public function __construct()
	{
		$this->cancle_watermark();
	}



	public function config(array $data)
	{
		foreach($data as $key=>$val)
		{
			$this->$key = $val;
		}

		if(empty($data['total_page']))
		{
			$this->total_page = ceil($this->total_row/$this->row);
		}

		return true;
	}




	public function doc_header($pageTitle = 'print pages')
	{
		$header = "";
		$header .= "<!DOCTYPE html>";
		$header .= "	<html>";
		$header .= "<head>";
		$header .= "	<meta charset='utf-8'>";
		$header .= "	<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
		$header .= "	<link rel='icon' href='".base_url()."assets/images/favicon.ico' type='image/x-icon' />";
		$header .= "	<title>". $pageTitle ."</title>";
		$header .= "	<link href='".base_url()."assets/fonts/fontawesome-5/css/all.css' rel='stylesheet' />";
		$header .= "	<link href='".base_url()."assets/css/bootstrap.css' rel='stylesheet' />";
		$header .= "	<link href='".base_url()."assets/css/template.css' rel='stylesheet' />";
		$header .= "	<link href='".base_url()."assets/css/print.css' rel='stylesheet' />";
		$header .= "	<script src='".base_url()."assets/js/jquery.min.js'></script>";
		$header .= "	<script src='".base_url()."assets/js/bootstrap.min.js'></script> ";
		$header .= "	<style>
										.page_layout{
											border: solid 1px #aaa;
											border-radius:0px;
										}

										.content-table > tbody > tr {
											height:10mm;
										}
										.content-table > tbody > tr:last-child {
											height: auto;
										}

										@media print{
											.page_layout{ border: none; }
										}
									</style>";
		$header .= "	</head>";
		$header .= "	<body>";
		$header .= "	<div class='modal fade' id='xloader' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>";
		$header .= "	<div class='modal-dialog' style='width:150px; background-color:transparent;' >";
		$header .= "	<div class='modal-content'>";
		$header .= "	<div class='modal-body'>";
		$header .= "	<div style='width:100%; height:150px; padding-top:25px;'>";
		$header .= "	<div style='width:100%;  text-align:center; margin-bottom:10px;'><i class='fa fa-spinner fa-4x fa-pulse' style='color:#069; display:block;'></i>	</div>";
		$header .= "	<div style='width:100%; height:10px; background-color:#333;'></div>";
		$header .= "	<div id='preloader' style='margin-top:-10px; height:10px; width:1%; background-color:#09F;'></div>";
		$header .= "	<div style='width:100%;  text-align:center; margin-top:15px; font-size:12px;'><span><strong>Loading....</strong></span></div>";
		$header .= "	</div></div></div></div></div> "; // modal fade;
		$header .= "	<div class='hidden-print' style='margin-top:10px; padding-bottom:10px; padding-right:5mm; width:200mm; margin-left:auto; margin-right:auto; text-align:right'>";
		$header .= "	<button class='btn btn-primary' onclick='print()'><i class='fa fa-print'></i>&nbspพิมพ์</button>";
		$header .= "	</div><div style='width:100%'>";

		return $header;
	}



	public function cancle_watermark()
	{
		$this->cancle_watermark = '<div id="watermark" style="width:0px; height:0px; position:absolute; left:30%; line-height:0px; top:400px;color:red; text-align:center; z-index:100000; opacity:0.1; transform:rotate(-30deg)">
		    <span style="font-size:150px; padding:0px 20px 0px 20px; border:solid 10px; border-color:red; border-radius:20px;">ยกเลิก</span>
		</div>';
	}



	public function add_title($title)
	{
		$this->title = $title;
	}






	public function set_pattern($pattern) //// กำหนดรูปแบบ CSS ให้กับ td
	{
		$this->pattern = $pattern;
	}






	public function print_sub_total(array $data)
	{
		$page = "<div style='width:190mm; margin:auto;'>";
		$page .= '<table class="table" style="margin-bottom:0px; border:solid 1px #333; height:50mm;">';
		foreach($data as $value)
		{
			foreach($value as $val)
			{
				$page .= "<tr style='font-size:".($this->font_size +2)."px; height:31px;'>";
				$page .= $val;
				$page .= "</tr>";
			}
		}
		$page .= "</table>";
		$page .= "</div>";

		return $page;
	}





	public function add_subheader($sub_header)
	{
		$this->sub_header = $this->thead($sub_header);
	}





	public function thead(array $dataset)
	{
		$thead	= "<table class='table content-table' style='table-layout:fixed; margin-bottom:2px; border:solid 1px #333; height:100mm;'>";
		$thead 	.= "<thead>";
		$thead	.= "<tr style='line-height:".$this->row_height."mm; font-size:".$this->font_size."px;'>";
		foreach($dataset as $data)
		{
			$value 	= $data[0];
			$css		= $data[1];
			$colspan   = empty($data[2]) ? "" : $data[2];
			$thead 	.= "<th ".$colspan." style='border:solid 1px #333; ".$css."'>".$value."</th>";
		}
		$thead	.= "</tr>";
		$thead 	.= "</thead>";
		return $thead;
	}





	public function doc_footer()
	{
		return "</div></body></html>";
	}





	public function add_header(array $header)
	{
		$this->header_row = $header;
	}



	public function print_header()
	{

		$header  = "<div style='width:{$this->content_width}mm; margin:auto; padding-bottom:10px; border-bottom:solid 2px #333;'>";

		$header .= "<table style='border:none; width:100%;'>";
		$header .= "<tr>";
		//--- block A width 60%
		$header .= "<td style='width:60%; padding-top:10px;'>";
		if(!empty($this->header_row['A']))
		{
			foreach($this->header_row['A'] as $value)
			{
				$header .= "<p style='width:100%; margin-bottom:1px; font-size:{$this->font_size}px;'>{$value}</p>";
			}
		}
		$header .= "</td>";

		//--- block B width 40%
		$header .= "<td style='width:40%; border-bottom:solid 2px #333;'>";
		$header .= "<table style='width:100%; border:none;'>";

		if(!empty($this->header_row['B']))
		{
			foreach($this->header_row['B'] as $row)
			{
				$header .= "<tr>";
				$header .= "<td class='{$this->text_color}' style='width:30%;'>{$row['label']}</td>";
				$header .= "<td style='width:70%;'>{$row['value']}</td>";
				$header .= "</tr>";
			}
		}

		$header .= "</table>";
		$header .= "</td>";
		$header .= "</tr>";


		if(!empty($this->header_row['C']))
		{
			$header .= "<tr>";
			//--- block C width 60%
			$header .= "<td style='width:60%; padding-top:10px;'>";

				foreach($this->header_row['C'] as $value)
				{
					$header .= "<p style='width:100%; margin-bottom:1px; font-size:12px;'>{$value}</p>";
				}

			$header .= "</td>";

			if(!empty($this->header_row['D']))
			{
				//--- block D width 40%
				$header .= "<td style='width:40%; border-bottom:solid 2px #333;'>";
				$header .= "<table style='width:100%; border:none;'>";


					foreach($this->header_row['D'] as $row)
					{
						$header .= "<tr>";
						$header .= "<td class='{$this->text_color}' style='width:30%;'>{$row['label']}</td>";
						$header .= "<td style='width:70%;'>{$row['value']}</td>";
						$header .= "</tr>";
					}

				$header .= "</table>";
				$header .= "</td>";
			}

			$header .= "</tr>";
		}

		$header .= "</table>";
		$header .= "</div>";

		return $header;
	}


	public function add_custom_header($html)
	{
		$this->custom_header = $html;
	}




	public function print_custom_header()
	{
		$height = ($this->header_rows * $this->row_height) +1;
		$sc = '<div style="width:'.$this->content_width.'mm; min-height:'.$height.'mm; margin:auto; margin-bottom:2mm; border:solid 2px #333; border-radius: 10px;">';
		$sc .= $this->custom_header;
		$sc .= '</div>';
		return $sc;
	}







	public function add_content($data)
	{
		$content = "<div style='width:".$this->content_width."mm; margin:auto; margin-bottom:2px; border:solid 2px #333; border-radius: 10px;' >";
		$content .= $data;
		$content .="</div>";
		return $content;
	}







	public function page_start()
	{
		$page_break = "page-break-after:always;";
		if($this->current_page == $this->total_page)
		{
			$page_break = "";
		}
		return "<div class='page_layout' style='position:relative; width:".$this->page_width."mm; padding-top:5mm; height:".$this->page_height."mm; margin:auto; ".$page_break."'>"; //// page start
	}






	public function page_end()
	{

		$phone = getConfig('COMPANY_PHONE');
		$fax = getConfig('COMPANY_FAX');
		$email = getConfig('COMPANY_EMAIL');
		$line = getConfig('COMPANY_LINE');
		$facebook = getConfig('COMPANY_FACEBOOK');
		$website = getConfig('COMPANY_WEBSITE');

		$page  = "<div style='width:100%; margin:auto; position:absolute; bottom:10px; z-index:-1;'>";
		$page .= "<div style='width:180mm; margin:auto; text-align:center; font-size:12px; padding-top:5px;'>";
		$page .= getConfig('COMPANY_FULL_NAME')." ".getConfig('COMPANY_ADDRESS1')." ";
		$page .= getConfig('COMPANY_ADDRESS2')." ".getConfig('COMPANY_POST_CODE')." เลขประจำตัวผู้เสียภาษี ".getConfig('COMPANY_TAX_ID');
		$page .= "</div>";
		$page .= "<div style='width:180mm; margin:auto; text-align:center; font-size:12px;'>";
		$page .= (empty($phone) ? "" : "<span style='margin-left:10px; margin-right:10px;'><i class='fas fa-phone-alt'></i>&nbsp; ".$phone."</span>");
		$page .= (empty($fax) ? "" : "<span style='margin-left:10px; margin-right:10px;'><i class='fa fa-fax'></i>&nbsp; ".$fax."</span>");
		$page .= (empty($line) ? "" : "<span style='margin-left:10px; margin-right:10px;'><i class='fab fa-line'></i>&nbsp;".$line."</span>");
		$page .= (empty($facebook) ? "" : "<span style='margin-left:10px; margin-right:10px;'><i class='fab fa-facebook'></i>&nbsp; ".$facebook."</span>");
		$page .= (empty($website) ? "" : "<span style='margin-left:10px; margin-right:10px;'><i class='fab fa-chrome'></i>&nbsp; ".$website."</span>");
		$page .= "</div>";
		$page .= "</div>";
		$page .= "</div><div class='hidden-print' style='height: 5mm; width:".$this->page_width."'></div>";
		return $page;
	}



	public function top_page()
	{
		$logo_path = base_url()."images/company/company_logo.png";

		$top = "";
		$top .= "<div style='width:".$this->content_width."mm; margin:auto; border:solid 1px #CCC;'>"; //// top start
		$top .= "<div class='text-center'>";
		$top .= $this->has_logo === TRUE ? "<img src='{$logo_path}' class='company-logo' width='70%' />" : '';
		$top .= "</div>";
		$top .= "<div class='text-center font-size-20'>{$this->title}</div>";
		$top .= "</div>"; /// top end;


		$top .= "<div style='width:".$this->content_width."mm; margin:auto; border:solid 1px #CCC;'>"; //// top start
		$top .= "<div calss='row'>";
		$top .= "<div class='col-sm-5'>" .$this->top_page_left()."</div>";
		$top .= "<div class='col-sm-2'>&nbsp;</div>";
		$top .= "<div class='col-sm-5'>" . $this->top_page_right() . "</div>";
		$top .= "</div>";
		$top .= "</div>"; /// top end;

		return $top;
	}


	public function top_page_left()
	{
		$top  = "";
		$top .= "<table style='width:100%; border:none;'>";

		if(!empty($this->header_row['left']))
		{
			foreach($this->header_row['left'] as $left)
			{
				$top .= "<tr>";
				$top .= "<td style='padding-top:10px; white-space:normal;'>";
				foreach($left as $value)
				{
					$top .= "<p style='width:100%; margin-bottom:1px; font-size:{$this->font_size}px;'>{$value}</p>";
				}
				$top .= "</td>";
				$top .= "</tr>";
			}
		}

		$top .= "</table>";

		return $top;

	}

	public function top_page_right()
	{
		$top  = "";
		$top .= "<table class='' style='width:100%; border:none; '>";
		$top .= "<tr>";
		$top .= "<td colspan='2' class='{$this->text_color}' style='width:30%; height:10mm; font-size:28px; white-space:normal; text-align:center; border-bottom:solid 2px #333;'>";
		$top .= $this->title;
		$top .= "<span style='font-size:10px; float:right; text-align:right; color:black'>หน้า {$this->current_page}/{$this->total_page}</span>";
		$top .= "</td>";
		//$top .= "<td style='font-size:10px; vertical-align:top; text-align:right; border-bottom:solid 2px #333;'>หน้า {$this->current_page}/{$this->total_page}</td>";
		$top .= "</tr>";

		if(!empty($this->header_row['right']))
		{
			$rob = 1;
			foreach($this->header_row['right'] as $right)
			{

				$count = 0;
				$item = count($right);

				foreach($right as $row)
				{
					$count++;
					$under_line = ($rob == 1 && $count == $item) ? 'border-bottom:solid 2px #333;' : '';
					$padding_top = ($count == 1) ? 'padding-top:5px;' : '';
					$top .= "<tr class='font-size-12'>";
					$top .= "<td class='{$this->text_color}' style='padding-bottom:5px; width:30%; {$under_line} {$padding_top}'>{$row['label']}</td>";
					$top .= "<td class='' style='padding-bottom:5px; width:70%; {$under_line} {$padding_top}'>{$row['value']}</td>";
					$top .= "</tr>";
				}

				$rob++;
			}
		}

		$top .= "</table>";

		return $top;

	}







	public function content_start()
	{
		return  "<div style='width:{$this->content_width}mm; margin:auto; margin-bottom:2px; border:none;'>";
	}




	public function content_end()
	{
		return "</div>";
	}





	public function print_row($data, $last = FALSE)
	{
		$height = $last ? "" : ""; //"height:10mm;";
		$row = "<tr style='font-size:".$this->font_size."px; ".$height."'>";
		$pattern = $this->pattern;
		if(count($pattern) == 0 )
		{
			$c = count($data);
			while($c>0)
			{
				array_push($pattern, "");
				$c--;
			}
		}

		foreach($data as $n=>$value)
		{
			$row .= "<td style='border:none; border-left:solid 1px #333; {$pattern[$n]}'>".$value."</td>";
		}
		$row .= "</tr>";
		return $row;
	}




	public function table_start()
	{
		$page  = "<div style='width:190mm; margin:auto;'>";
		$page .= $this->sub_header;

		return $page;
	}





	public function table_end()
	{
		return "</table></div>";
	}




	public function print_remark($remark = NULL, $font_size = 10)
	{
		$row = "<div style='width:{$this->content_width}mm; min-height:5mm; margin:auto; border:none; padding-left:20px; margin-bottom:10mm;'>";
		$row .= empty($remark) ? "" : "หมายเหตุ : {$remark}";
		$row .= "</div>";

		return $row;
	}



	public function set_footer(array $data)
	{
		if(!$this->footer)
		{
			return false;
		}
		else
		{
			$c = count($data);
			$box_width = 100/$c;
			$box_width = $box_width > 35 ? 35 : $box_width;
			$height = $this->footer_row * $this->row_height;
			$row1 = $this->row_height;
			$row2 = 8;
			$row4 = 10;
			$row3 = $height - ($row1+$row2+$row4) - 2;
			$footer = "<div style='width:190mm; height:30mm; margin:auto; border:solid 1px #333; padding:5px; background-color:white;'>";
			foreach($data as $n=>$value)
			{
				$footer .="<div style='width:".$box_width."%; height30mm; text-align:center; float:right; padding-left:10px; padding-right:10px;'>";
				$footer .="<span style='font-size:{$this->font_size}px; width:100%; height:5mm; text-align:center;'>".$value[0]."</span>";
				$footer .="<div style='font-size:{$this->font_size}px; width:100%; height:5mm; text-align:center; padding-left:10px; padding-right:10px; '>";
				$footer .="<span style='font-size:{$this->font_size}px; width:100%; height:5mm; text-align:center;font-size:8px; float:left;'>".$value[1]."</span>";
				$footer .="<span style='font-size:{$this->font_size}px; width:100%; height:5mm; text-align:center; padding-left:5px; padding-right:5px; border-bottom:dotted 1px #333; float:left; padding:10px;'></span>";
				$footer .="<span style='font-size:{$this->font_size}px; width:30%; height:10mm; text-align:left; vertical-align:bottom; float:left; padding-top: 25px;'>".$value[2]."</span>";
				$footer .="<span style='font-size:{$this->font_size}px; width:70%; height:10mm; text-align:left; float:left; padding-top:10px; border-bottom:dotted 1px #333;'></span>";
				$footer .="</div>";
				$footer .="</div>";
			}
			$footer .="</div>";
			$this->footer = $footer;
		}
	}


	public function print_barcode($barcode, $css = "")
	{
		if($css == ""){ $css = "width: 100px;"; }
		return "<img src='".base_url()."assets/barcode/barcode.php?text=".$barcode."' style='".$css."' />";
	}
} //--- ensd class

 ?>
