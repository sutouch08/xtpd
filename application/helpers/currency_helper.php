<?php

function select_currency($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('currency_model');
  $code = (empty($code) ? getConfig('CURRENCY') : $code);
  $code = $code === '##' ? getConfig('CURRENCY') : $code;
  $sc = "";

  $currency = $ci->currency_model->get_list();

  if(!empty($currency))
  {
    foreach($currency as $rs)
    {
      $sc .= '<option value="'.$rs->Code.'" '.is_selected($code, $rs->Code).'>'.$rs->Code.'</option>';
    }
  }

  return $sc;
}


function select_country($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('tool_model');
  $options = $CI->tool_model->get_country_list();

  if(!empty($options))
  {
    $df = getConfig('COUNTRY');
    foreach($options as $rs)
    {
      if($code === NULL)
      {
        $sc .= '<option value="'.$rs->code.'" '.is_selected($df, $rs->code).'>'.$rs->name.'</option>';
      }
      else
      {
        $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->name.'</option>';
      }
    }
  }

  return $sc;
}


 ?>
