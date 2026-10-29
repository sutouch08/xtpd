<?php
function select_all_price_list_type($id = '')
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('price_list_type_model');
  $list = $ci->price_list_type_model->get_all(FALSE); //--- get all price list type

  if(!empty($list))
  {    
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_price_list_type($id = '')
{
  $sc = "";
  $ci = &get_instance();
  $ci->load->model('price_list_type_model');
  $list = $ci->price_list_type_model->get_all(); //--- get all price list type

  if (!empty($list))
  {
    foreach ($list as $rs)
    {
      $sc .= '<option value="' . $rs->id . '" ' . is_selected($id, $rs->id) . '>' . $rs->name . '</option>';
    }
  }

  return $sc;
}


function price_list_type_array()
{
  $ci = &get_instance();
  $ci->load->model('price_list_type_model');
  $list = $ci->price_list_type_model->get_all(); //--- get all price list type

  $arr = array();

  if (!empty($list))
  {
    foreach ($list as $rs)
    {
      $arr[$rs->id] = $rs->name;
    }
  }

  return $arr;
}


function special_price_list_name($id)
{
  $ci = &get_instance();
  $ci->load->model('special_price_list_model');
  return $ci->special_price_list_model->get_name($id);
}