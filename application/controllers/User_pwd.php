<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_pwd extends PS_Controller
{
  public $title = 'Change Password';
	public $menu_code = 'change password';
	public $menu_group_code = 'SC';
  public $error;


	public function __construct()
	{
		parent::__construct();
    $this->home = base_url().'user_pwd';
	}


	public function index()
	{
    redirect($this->home.'/change');
	}


  public function change()
	{
    if(!empty($this->_user->uid))
    {
      $this->pm->can_view = 1;
      $this->pm->can_edit = 1;

      $ds['data'] = $this->_user;
      $this->load->view('users/change_pwd', $ds);
    }
    else
    {
      $this->error_page();
    }

	}


  public function verify_password()
  {
    $sc = TRUE;
    $pwd = trim($this->input->post('pwd'));
    if(!empty($this->_user))
    {
      if(!password_verify($pwd, $this->_user->pwd))
      {
        $sc = FALSE;
        $this->error = "Current password not accepted";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid uid";
    }

    echo $sc === TRUE ? 'accepted' : $this->error;
  }



  public function change_password()
	{
    $sc = TRUE;

		if($this->input->post('pwd'))
		{
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);

			if(!$this->user_model->change_password($this->_user->id, $pwd))
      {
        $sc = FALSE;
        $this->error = "Change password not successfull, please try again";
      }
		}
    else
    {
      $sc = FALSE;
      $this->error = "Missing Required Parameter : password";
    }

		$this->_response($sc);
	}

}
 ?>
