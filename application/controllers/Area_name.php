<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_name extends PS_Controller
{
	public $menu_code = 'AREA_NAME';
	public $menu_group_code = 'ADMIN';
	public $title = 'Area Name';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'area_name';
		$this->load->model('area_name_model');
  }


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'area_name', '')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
			exit;
		}

		$perpage = get_rows();
		$segment = 3; //-- url segment
		$rows = $this->area_name_model->count_rows($filter);
		$filter['data'] = $this->area_name_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$this->pagination->initialize($init);
		$this->load->view('area_name/area_name_list', $filter);
	}


	public function syncData()
	{
		$sc = TRUE;

		$list = $this->area_name_model->get_sap_data();

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

				if( ! $this->area_name_model->is_exists_id($rs->id))
				{
					$this->area_name_model->add($arr);
				}
				else
				{
					$this->area_name_model->update($rs->id, $arr);
				}
			}
		}

		$this->_response($sc);
	}


  public function clear_filter()
	{
		$filter = array(
			'area_name'
		);

		return clear_filter($filter);
	}

}
