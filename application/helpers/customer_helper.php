<?php
function select_department($id = '')
{
  $ci =& get_instance();
  $ci->load->model('department_model');
  $list = $ci->department_model->get_all();

  if(!empty($list))
  {
    $sc = '';

    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }

    return $sc;
  }

  return NULL;
}


function select_sales_team($id = '')
{
  $ci =& get_instance();
  $ci->load->model('sales_team_model');
  $list = $ci->sales_team_model->get_all();

  if(!empty($list))
  {
    $sc = '';

    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }

    return $sc;
  }

  return NULL;
}


function select_sales_person($id = '')
{
  $ci =& get_instance();
  $ci->load->model('sales_person_model');
  $list = $ci->sales_person_model->get_all();

  if(!empty($list))
  {
    $sc = '';

    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->SlpCode.'" '.is_selected($rs->SlpCode, $id).'>'.$rs->SlpName.'</option>';
    }

    return $sc;
  }

  return NULL;
}

function department_name($id)
{
  $ci =& get_instance();
  $ci->load->model('department_model');
  return $ci->department_model->get_name($id);
}

function department_array()
{
  $ci =& get_instance();
  $ci->load->model('department_model');
  $list = $ci->department_model->get_all();

  if(!empty($list))
  {
    $ds = array();

    foreach($list as $rs)
    {
      $ds[$rs->id] = $rs->name;
    }

    return $ds;
  }

  return NULL;
}

function area_name($id)
{
  $ci =& get_instance();
  $ci->load->model('area_name_model');
  return $ci->area_name_model->get_name($id);
}

function area_array()
{
  $ci =& get_instance();
  $ci->load->model('area_name_model');
  $list = $ci->area_name_model->get_all();

  if(!empty($list))
  {
    $ds = array();

    foreach($list as $rs)
    {
      $ds[$rs->id] = $rs->name;
    }

    return $ds;
  }

  return NULL;
}


function sales_team_name($id)
{
  $ci =& get_instance();
  $ci->load->model('sales_team_model');
  return $ci->sales_team_model->get_name($id);
}

function sales_team_array()
{
  $ci =& get_instance();
  $ci->load->model('sales_team_model');
  $list = $ci->sales_team_model->get_all();

  if(!empty($list))
  {
    $ds = array();

    foreach($list as $rs)
    {
      $ds[$rs->id] = $rs->name;
    }

    return $ds;
  }

  return NULL;
}


function sales_person_array()
{
  $ci =& get_instance();
  $ci->load->model('sales_person_model');
  $list = $ci->sales_person_model->get_all();

  if(!empty($list))
  {
    $ds = array();

    foreach($list as $rs)
    {
      $ds[$rs->SlpCode] = $rs->SlpName;
    }

    return $ds;
  }

  return NULL;
}


function sales_person_name($id)
{
  $ci =& get_instance();
  $ci->load->model('sales_person_model');
  return $ci->sales_person_model->get_name($id);
}


function customer_group_array()
{
  $ci =& get_instance();
  $ci->load->model('customer_group_model');
  $list = $ci->customer_group_model->get_all();

  if(!empty($list))
  {
    $ds = array();

    foreach($list as $rs)
    {
      $ds[$rs->id] = [
        'code' => $rs->code,
        'name' => $rs->name
      ];
    }

    return $ds;
  }

  return NULL;
}