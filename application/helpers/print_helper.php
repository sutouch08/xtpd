<?php


function barcodeImage($barcode)
{
	return '<img src="'.base_url().'assets/barcode/barcode.php?text='.$barcode.'" style="height:8mm;" />';
}


function inputRow($text, $style='')
{
  return '<input type="text" class="print-row" value="'.$text.'" style="'.$style.'" />';
}


function phone_display($phone1 = NULL, $phone2 = NULL, $phone3 = NULL)
{
	$display = "";
	$display .= empty($phone1) ? "" : $phone1;
	$display .= empty($phone2) ? "" : (! empty($display) ? ", {$phone2}" : $phone2);
	$display .= empty($phone2) ? "" : (! empty($display) ? ", {$phone3}" : $phone3);

	return $display;
}


function payment_display($term = 0)
{
	if($term > 0)
	{
		return "เครดิต {$term} วัน";
	}
	else
	{
		return "เงินสด";
	}
}



function getTotalPage($details, $row_per_page, $row_text)
{
	$total_use_row = 0;
	$total_page = 1;
	$row = $row_per_page;
	$i = 0;
	$index = 0;
	$last_row = FALSE;
	$limit_text = $row_per_page * $row_text;
  foreach($details as $rs)
  {
		if($i >= $row)
		{
			$total_page++;
			$i=0;
		}

    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
			$model = mb_strlen($rs->Dscription);
			$spec  = mb_strlen(substr($rs->ItemDetail, 0, $limit_text));
			$newline = ceil(substr_count($rs->Dscription, "\n") * 0.5);
			$text_length = $spec > $model ? $spec : $model;
			$use_row = ceil($text_length/$row_text);
			//$use_row = $use_row > $newline ? $use_row : $newline;
			$use_row += $newline;
			$total_use_row += $use_row;
			$use_row = $use_row > $row_per_page ? $row_per_page : $use_row;

			if($use_row > 1)
			{
				//--- คำนวนบรรทัดที่ต้องใช้ต่อ 1 รายการ
				$use_row -= 1;
				$i += $use_row;
			}
    }

		$index++;
		//--- check next row
		$nextrow = isset($details[$index]) ? $details[$index] : FALSE;
		if(!empty($nextrow))
		{
			$model = mb_strlen($nextrow->Dscription);
			$spec  = mb_strlen($nextrow->ItemDetail);
			$newline = ceil(substr_count($nextrow->Dscription, "\n") * 0.5);
			$text_length = $spec > $model ? $spec : $model;
			$use_row = ceil($text_length/$row_text);
			$use_row = $use_row > $newline ? $use_row : $newline;
			$use_row += $i;

			if($row < $use_row)
			{
				$i = $use_row;
				$last_row = TRUE;
			}
			else
			{
				$i++;
			}
		}
		else
		{
			$i++;
		}

  }

	return $total_page;
}

 ?>
