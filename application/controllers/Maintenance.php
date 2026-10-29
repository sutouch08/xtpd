<?php
class Maintenance extends CI_Controller
{
  public $_user;
  public $_SuperAdmin = FALSE;
  public $isGM = FALSE;
  public $isAdmin = FALSE;
  public $uid;

  public function __construct()
  {
    parent::__construct();

    $this->uid = get_cookie('uid');
    $this->_user = $this->user_model->get_user_by_uid($this->uid);

    if( ! empty($this->_user))
    {
      $this->_SuperAdmin = $this->_user->ugroup_id == -987654321 ? TRUE : FALSE;
      $this->isGM = $this->_user->role == 'GM' ? TRUE : FALSE;
      $this->isAdmin = $this->_user->role == 'salesAdmin' ? TRUE : FALSE;
    }
    
    //--- get permission for user
    $this->pm = get_permission('CLOSE_SYSTEM');
    $this->load->model('config_model');
  }


  public function index()
  {
    if(getConfig('CLOSE_SYSTEM') == 0)
    {
      redirect(base_url());
    }

    $this->load->view('maintenance');
  }

  public function open_system()
  {
    if($this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete)
    {
      $rs = $this->config_model->update('CLOSE_SYSTEM', 0);
      echo $rs === TRUE ? 'success' : 'fail';
    }
  }


  public function check_open_system()
  {
    $rs = $this->config_model->get('CLOSE_SYSTEM');
    echo $rs == 1 ? 'close' : 'open';
  }


}


 ?>
