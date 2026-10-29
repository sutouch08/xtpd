<?php

function select_promotion($id = NULL)
{
  $sc = "<option value=''>No active promotion found</option>";

  $ci =& get_instance();
  $ci->load->model('promotion_model');

  $list = $ci->promotion_model->get_all_active_promotion();

  if(!empty($list))
  {
    $sc = "";
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" data-code="'.$rs->code.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}

 ?>
