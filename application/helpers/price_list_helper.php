<?php

function select_price_list($id = '')
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('price_list_model');
  $options = $ci->price_list_model->get_all(); // get only active price list

  if( ! empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function price_list_name($id)
{
  $ci =& get_instance();
  $ci->load->model('price_list_model');
  return $ci->price_list_model->get_name($id);
}