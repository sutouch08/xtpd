<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bi extends PS_Controller
{
	public $title = 'Power BI';
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
		$ds['bi_link'] = $this->_user->bi_link == 1 ? $this->user_model->get_bi_link($this->_user->sale_id) : NULL;
		$this->load->view('bi_view', $ds);
	}


	public function get_link()
	{
		$link = $this->_user->bi_link == 1 ? $this->user_model->get_bi_link($this->_user->sale_id) : NULL;

		if(! empty($link))
		{
			echo $link;
		}
		else
		{
			echo "not found";
		}
	}



} //--- end class
