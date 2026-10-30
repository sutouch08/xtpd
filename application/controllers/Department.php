<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends PS_Controller
{
	public $menu_code = 'DEPARTMENT';
	public $menu_group_code = 'ADMIN';
	public $title = 'Department';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'department';
		$this->load->model('department_model');
  }


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'department_name', '')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
			exit;
		}

		$perpage = get_rows();
		$segment = 3; //-- url segment
		$rows = $this->department_model->count_rows($filter);
		$filter['data'] = $this->department_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$this->pagination->initialize($init);
		$this->load->view('department/department_list', $filter);
	}


	public function syncData()
	{
		$sc = TRUE;
		$list = $this->department_model->get_sap_data();

		if( ! empty($list))
		{
			foreach($list as $rs)
			{
				$arr = array(
					'id' => $rs->id,
					'name' => $rs->name,
					'date_upd' => now(),
					'update_by' => $this->_user->id
				);

				if( ! $this->department_model->is_exists_id($rs->id))
				{
					$this->department_model->add($arr);
				}
				else
				{
					$this->department_model->update($rs->id, $arr);
				}
			}
		}

		$this->_response($sc);
	}


  public function clear_filter()
	{
		return clear_filter('department_name');		
	}

}
