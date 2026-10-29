<?php
function _check_login()
{
  $CI =& get_instance();
  $uid = get_cookie('uid');
  if($uid === NULL OR $CI->user_model->verify_uid($uid) === FALSE)
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
    return reject_permission();
  }

  //--- If super admin
  if($ci->_user->ugroup_id == -987654321)
  {
    $pm = new stdClass();
    $pm->can_view = 1;
    $pm->can_add = 1;
    $pm->can_edit = 1;
    $pm->can_delete = 1;
  }
  else
  {
    $pm = $ci->user_model->get_permission($menu, $ci->_user->uid, $ci->_user->ugroup_id);

    if(empty($pm))
    {
      return reject_permission();
    }
    else
    {
      if(getConfig('CLOSE_SYSTEM') == 2 && $menu != 'CLOSE_SYSTEM')
      {
        $pm->can_add = 0;
        $pm->can_edit = 0;
        $pm->can_delete = 0;
      }
    }
  }

  return $pm;
}

function reject_permission()
{
  $pm = new stdClass();
  $pm->can_view = 0;
  $pm->can_add = 0;
  $pm->can_edit = 0;
  $pm->can_delete = 0;
  return $pm;
}


function select_user_group($id = NULL)
{
  $ds = '';
  $CI =& get_instance();
  $qs = $CI->user_model->get_all_user_group();
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


function select_user_id($id = NULL)
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


function select_uname($uname = NULL)
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


function select_saleman($sale_id = NULL)
{
  $ds = '';
  $CI =& get_instance();
  $qs = $CI->user_model->get_all_slp();
  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $sale_id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function select_employee($id = NULL)
{
  $ds = '';
  $CI =& get_instance();
  $qs = $CI->user_model->get_all_employee();
  if(!empty($qs))
  {
    foreach($qs as $rs)
    {
      $ds .= '<option value="'.$rs->empID.'" data-name="'.$rs->firstName.' '.$rs->lastName.'" '.is_selected($rs->empID, $id).'>'.$rs->firstName.' '.$rs->lastName.'</option>';
    }
  }

  return $ds;
}

function select_area($id = NULL)
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


function get_sale_name($sale_id)
{
  $ci =& get_instance();

  return $ci->user_model->get_saleman_name($sale_id);

}


function uname($id)
{
  $ci =& get_instance();

  if( ! empty($id))
  {
    return $ci->user_model->get_uname($id);
  }

  return NULL;
}

function emp_name($uname)
{
  $ci =& get_instance();

  if( ! empty($uname))
  {
    return $ci->user_model->get_name($uname);
  }

  return NULL;
}


function emp_name_by_id($id)
{
  $ci =& get_instance();

  if( ! empty($id))
  {
    return $ci->user_model->get_name_by_id($id);
  }

  return NULL;
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

 ?>
