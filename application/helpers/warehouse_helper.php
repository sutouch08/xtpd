<?php

function select_warehouse($code = NULL)
{
  $sc = "";
  $ci =& get_instance();

  $ci->load->model('warehouse_model');

  $options = $ci->warehouse_model->get_warehouse_list();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= "<option value='{$rs->code}' ".is_selected($rs->code, $code).">{$rs->code} : {$rs->name}</option>";
    }
  }

  return $sc;
}


 ?>
