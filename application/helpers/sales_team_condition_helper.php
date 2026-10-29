<?php
function select_sales_team_condition($id = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('sales_team_condition_model');

  $team = $ci->sales_team_condition_model->get_all();

  if( ! empty($team))
  {
    foreach($team as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_sale_team($id = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('sales_team_model');

  $teams = $ci->sales_team_model->get_all();

  if(!empty($teams))
  {
    foreach($teams as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_department($id = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('department_model');

  $rows = $ci->department_model->get_all();

  if(!empty($rows))
  {
    foreach($rows as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_sale_person($id = NULL)
{
  $sc = "";
  $ci =& get_instance();
  $ci->load->model('sale_person_model');

  $list = $ci->sale_person_model->get_all();

  if(!empty($list))
  {
    foreach($list as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function sale_person_name($id)
{
  $ci =& get_instance();
  $ci->load->model('sale_person_model');

  $rs = $ci->sale_person_model->get($id);

  if(!empty($rs))
  {
    return $rs->name;
  }

  return NULL;
}


function department_name($id)
{
  $ci =& get_instance();
  $ci->load->model('department_model');
  return $ci->department_model->get_name($id);
}


function sales_team_name($id)
{
  $ci =& get_instance();
  $ci->load->model('sales_team_model');
  return $ci->sales_team_model->get_name($id);
}

function sales_team_condition_name($id)
{
  $ci =& get_instance();
  $ci->load->model('sales_team_condition_model');
  return $ci->sales_team_condition_model->get_name($id);
}

?>
