<?php
function select_step_rule($PriceList = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('step_rule_model');
  $options = $ci->step_rule_model->get_active_price_list();

  if( ! empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->PriceList.'" '.is_selected($PriceList, $rs->PriceList).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}

 ?>
