<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_Controller extends CI_Controller
{
  public $pm;
  public $home;
  public $ms;
  public $mc;
	public $error;
  public $_user;
  public $_SuperAdmin = FALSE;
  public $isGM = FALSE;
  public $isAdmin = FALSE;
  public $uid;

  public function __construct()
  {
    parent::__construct();
    //--- check is user has logged in ?
    _check_login();

    $this->uid = get_cookie('uid');
    $this->_user = $this->user_model->get_user_by_uid($this->uid);
    $this->_SuperAdmin = $this->_user->ugroup_id == -987654321 ? TRUE : FALSE;
    $this->isGM = $this->_user->role == 'GM' ? TRUE : FALSE;
    $this->isAdmin = $this->_user->role == 'salesAdmin' ? TRUE : FALSE;

    $this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

    if($this->close_system == 1 && $this->_SuperAdmin === FALSE && $this->isGM === FALSE)
    {
      redirect(base_url().'maintenance');
    }

    //--- get permission for user
    $this->pm = get_permission($this->menu_code);

    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database

  }



  public function _response($sc = TRUE)
	{
		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function deny_page()
  {
    return $this->load->view('deny_page');
  }


  public function error_page()
  {
    return $this->load->view('page_error');
  }

  public function page_error()
  {
    $this->load->view('page_error');
  } 


  public function _get_user_team($user_id)
  {
    return $this->user_model->get_user_team($user_id);
  }


  public function _get_team_customer_group($team_id)
  {
    return $this->user_model->get_team_customer_group($team_id);
  }
}

?>
