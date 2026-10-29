<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends PS_Controller
{
	public $title = 'Welcome';
	public $menu_code = '';
	public $menu_group_code = '';
	public $error;

	public function __construct()
	{
		parent::__construct();
	}


	public function index()
	{
		$this->pm->can_view = 1;
		$this->load->view('test');
	}



	


} //--- end class
