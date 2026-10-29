<?php

function select_conditions($cond = NULL)
{
  $sc  = '<option value="Less Than" '.is_selected("Less Than", $cond).'>Less Than</option>';
  $sc .= '<option value="Less or Equal" '.is_selected("Less or Equal", $cond).'>Less or Equal</option>';
  $sc .= '<option value="Greater Than" '.is_selected("Greater Than", $cond).'>Greater Than</option>';
  $sc .= '<option value="Greater or Equal" '.is_selected("Greater or Equal", $cond).'>Greater or Equal</option>';

  return $sc;
}



function select_approver($user_id = NULL)
{
  $sc = '';

  $ci =& get_instance();
  $ci->load->model('approver_model');
  $approver = $ci->approver_model->get_all_active_approver();

  if(!empty($approver))
  {
    foreach($approver as $rs)
    {
      $sc .= '<option value="'.$rs->user_id.'" '.is_selected($user_id, $rs->user_id).'>'.$rs->uname.' | '.$rs->emp_name.'</option>';
    }
  }

  return $sc;
}


function select_approver_uname($uname = NULL)
{
  $sc = '';

  $ci =& get_instance();
  $ci->load->model('approver_model');
  $approver = $ci->approver_model->get_all_active_approver();

  if(!empty($approver))
  {
    foreach($approver as $rs)
    {
      $sc .= '<option value="'.$rs->uname.'" '.is_selected($uname, $rs->uname).'>'.$rs->uname.' | '.$rs->emp_name.'</option>';
    }
  }

  return $sc;
}



function approver_name_list($ds)
{
  if(!empty($ds))
  {
    $sc = "";

    foreach($ds as $rs)
    {
      $sc .= $rs->uname.' | '.$rs->emp_name.'<br/>';
    }

    return $sc;
  }

  return NULL;
}




function select_customer_team($id = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('customer_team_model');

  $list = $ci->customer_team_model->get_all();

  if(!empty($list))
  {
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


?>
