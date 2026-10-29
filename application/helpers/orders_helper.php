<?php

function select_payment_term_discount($id = NULL)
{
  $ds = "<option value=\"-10\" data-groupnum=\"x\" data-disc=\"0\" data-change=\"0\"".is_selected($id, '-10').">Customer default</option>";
  $ci =& get_instance();
  $ci->load->model('payment_term_discount_model');

  $list = $ci->payment_term_discount_model->get_active_term_list();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= "<option value=\"{$rs->id}\" data-groupnum=\"{$rs->GroupNum}\" data-disc=\"{$rs->DiscPrcnt}\" data-change=\"{$rs->canChange}\"".is_selected($id, $rs->id).">{$rs->name}</option>";
    }
  }

  return $ds;
}


function select_payment_term($id = NULL)
{
  $ds = "";
  $ci =& get_instance();
  $ci->load->model('orders_model');

  $list = $ci->orders_model->get_payment_term_list();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" data-days="'.$rs->ExtraDays.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}



function get_checkbox($id, $status = 'P', $can_approve = FALSE, $no = "")
{

  $sc = "";

  if($status == 'P')
  {
    if($can_approve)
    {
      $sc = '<label>
        <input type="checkbox" class=" ace check-item" id="check-item-'.$id.'" value="'.$id.'" onchange="toggleApprove()">
        <span class="lbl"></span>
      </label>';
    }
    else
    {
      $sc = $no;
    }
  }

  if($status == 'R')
  {
    $sc = '<i class="fa fa-times red"></i>';
  }

  if($status == 'A')
  {
    $sc = '<i class="fa fa-check green"></i>';
  }

  return $sc;
}



function get_rejectbox($id, $status = 'P', $can_approve = FALSE, $no = "")
{
  $sc = "";

  if($status == 'P')
  {
    if($can_approve)
    {
      $sc = '<input type="text" class="form-control input-sm reject-box" id="reject-item-'.$id.'" value="" />';
    }
  }

  return $sc;
}


function term_name($id = '')
{
  $ci =& get_instance();
  $ci->load->model('payment_term_discount_model');

  $name = "ไม่ระบุ";

  if( ! empty($id))
  {
    $name = $id == -10 ? "Customer default" : $ci->payment_term_discount_model->get_name($id);
  }  

  return $name;
}


function order_price_list_name($price_list_id = '', $special_price_id = '')
{
  $ci =& get_instance();
  $ci->load->model('orders_model');
  $ci->load->model('special_price_list_model');

  if(empty($price_list_id))
  {
    return "-";
  }
  else
  {
    return $price_list_id == -10 ? $ci->special_price_list_model->get_name($special_price_id) : $ci->orders_model->price_list_name($price_list_id);
  }
}

function order_step_flow($current = 1, $credit_issue = FALSE, $is_overdue = FALSE)
{
  $templates = array(
    'normal' => array(
      1 => ['title' => 'เปิดออเดอร์', 'class' => 'active'],
      2 => ['title' => 'ตรวจสอบ', 'class' => ''],
      3 => ['title' => 'อนุมัติ', 'class' => '']
    ),
    'credit_issue' => array(
      1 => ['title' => 'เปิดออเดอร์', 'class' => 'active'],
      2 => ['title' => 'FIN ตรวจสอบ Credit Limit', 'class' => ''],
      3 => ['title' => 'SMU พิจารณาอนุมัติ', 'class' => ''],
      4 => ['title' => 'อนุมัติ', 'class' => '']
    ),
    'over_due' => array(
      1 => ['title' => 'เปิดออเดอร์', 'class' => 'active'],
      2 => ['title' => 'FIN ตรวจสอบ Credit Limit', 'class' => ''],
      3 => ['title' => 'ผู้แทนแนบหลักฐานการชำระเงิน (ภายใน 7 วัน)', 'class' => ''],
      4 => ['title' => 'FIN ตรวจสอบหลักฐานการชำระเงิน', 'class' => ''],
      5 => ['title' => 'SMU พิจารณาอนุมัติ', 'class' => ''],
      6 => ['title' => 'อนุมัติ', 'class' => '']
    )
  );

  $flow = $credit_issue ? ($is_overdue ? $templates['over_due'] : $templates['credit_issue']) : $templates['normal'];

  $html = '<ul class="steps">';

  foreach($flow as $step => $data)
  {
    $class = $step <= $current ? 'active' : $data['class'];
    $html .= '<li data-step="'.$step.'" class="'.$class.'"><span class="step">'.$step.'</span><span class="title">'.$data['title'].'</span></li>';
  }

  $html .= '</ul>';
  return $html;
}

 ?>
