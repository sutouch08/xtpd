<?php
function select_item_group($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('item_model');

  $options = $ci->item_model->get_item_group_list();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= "<option value='{$rs->code}' ".is_selected($rs->code, $code).">{$rs->name}</option>";
    }
  }

  return $sc;
}

 ?>
