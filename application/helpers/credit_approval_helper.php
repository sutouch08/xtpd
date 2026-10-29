<?php
function get_reply_status($id)
{
  $ci =& get_instance();
  $ci->load->model('payment_request_model');

  return $ci->payment_request_model->get_reply_status($id);
}

function select_credit_approver($id = '')
{
  $ds = "";
  $ci =& get_instance();
  $ci->load->model('credit_approver_model');
  $list = $ci->credit_approver_model->get_all_approver();

  if(!empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->user_id.'" '.is_selected($rs->user_id, $id).'>'.$rs->uname.' | '.$rs->emp_name.'</option>';
    }
  }

  return $ds;
}

