<?php
function select_unit($code = '')
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/unit_model');
  $options = $ci->unit_model->get_data(); //--- OUOM

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}

