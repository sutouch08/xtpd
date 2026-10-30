<?php
function _check_login()
{
  $ci =& get_instance();
  $uid = get_cookie('uid');

  if(empty($uid) || ! $ci->user_model->verify_uid($uid))
  {
    redirect(base_url().'authentication');
    exit;
  }
}

function get_permission($menu)
{
  $ci =& get_instance();

  if(empty($ci->_user))
  {
    return $ci->user_model->reject_permission();
  }

  //--- If super admin
  if($ci->_user->ugroup_id == -987654321)
  {
    return $ci->user_model->grant_all_permission();
  }

  $pm = $ci->user_model->get_permission($menu, $ci->_user->uid, $ci->_user->ugroup_id);

  if(getConfig('CLOSE_SYSTEM') == 2 && $menu != 'CLOSE_SYSTEM')
  {
    $pm->can_add = 0;
    $pm->can_edit = 0;
    $pm->can_delete = 0;
  }  

  return $pm;
}

function reject_permission()
{
  return (object) [
    'can_view' => 0,
    'can_add' => 0,
    'can_edit' => 0,
    'can_delete' => 0
  ];
}


function select_user_group($id = '')
{
  $ds = '';
  $ci =& get_instance();
  $qs = $ci->user_model->get_all_user_group();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function user_condition_label($user_id)
{
  $ds = "";
  $ci =& get_instance();
  $team = $ci->user_model->get_user_condition($user_id);

  if(!empty($team))
  {
    $i = 1;

    foreach($team as $rs)
    {
      $ds .= $i == 1 ? $rs->team_name : ", {$rs->team_name}";
      $i++;
    }
  }

  return $ds;
}


function select_user_id($id = '')
{
  $ds = '';
  $ci =& get_instance();
  $users = $ci->user_model->get_all();

  if( ! empty($users))
  {
    foreach($users as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" data-uname="'.$rs->uname.'" data-name="'.$rs->emp_name.'" '.is_selected($id, $rs->id).'>'.$rs->uname.' | '.$rs->emp_name.'</option>';
    }
  }

  return $ds;
}


function select_uname($uname = '')
{
  $ds = '';
  $ci =& get_instance();
  $users = $ci->user_model->get_all();

  if( ! empty($users))
  {
    foreach($users as $rs)
    {
      $ds .= '<option value="'.$rs->uname.'" data-id="'.$rs->id.'" data-name="'.$rs->emp_name.'" '.is_selected($uname, $rs->uname).'>'.$rs->uname.' | '.$rs->emp_name.'</option>';
    }
  }

  return $ds;
}


function select_saleman($sale_id = '')
{
  $ds = '';
  $ci =& get_instance();
  $qs = $ci->user_model->get_all_slp();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $sale_id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_employee($id = '')
{
  $ds = '';
  $ci =& get_instance();
  $qs = $ci->user_model->get_all_employee();

  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->empID.'" data-name="'.$rs->firstName.' '.$rs->lastName.'" '.is_selected($rs->empID, $id).'>'.$rs->firstName.' '.$rs->lastName.'</option>';
    }
  }

  return $ds;
}

function select_area($id = '')
{
  $ds = '';
  $ci =& get_instance();
  $area = $ci->user_model->get_all_area();

  if( ! empty($area))
  {
    foreach($area as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function get_sale_name($sale_id = '')
{
  $ci =& get_instance();
  return $ci->user_model->get_saleman_name($sale_id);
}


function uname($id = '')
{
  $ci =& get_instance();
  return $ci->user_model->get_uname($id);  
}


function emp_name($uname = '')
{
  $ci =& get_instance();
  return $ci->user_model->get_name($uname);
}


function emp_name_by_id($id = '')
{
  $ci =& get_instance();
  return $ci->user_model->get_name_by_id($id);  
}

function users_array()
{
  $ci =& get_instance();
  $users = $ci->user_model->get_all();
  $arr = [];

  if( ! empty($users))
  {
    foreach($users as $rs)
    {
      $arr[$rs->id] = array(
        'id' => $rs->id,
        'uname' => $rs->uname,
        'name' => $rs->emp_name,
      );
    }
  }

  return $arr;
}
