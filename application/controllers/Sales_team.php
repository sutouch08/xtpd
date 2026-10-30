<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_team extends PS_Controller
{
	public $menu_code = 'SALES_TEAM';
	public $menu_group_code = 'ADMIN';
	public $title = 'Sales Team';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'sales_team';
		$this->load->model('sales_team_model');
  }


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'sales_team_name', '')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
			exit;
		}

		$perpage = get_rows();
		$segment = 3; //-- url segment
		$rows = $this->sales_team_model->count_rows($filter);
		$filter['data'] = $this->sales_team_model->get_list($filter, $perpage, $this->uri->segment($segment));		
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$this->pagination->initialize($init);
		$this->load->view('sales_team/sales_team_list', $filter);
	}


	public function syncData()
	{
		$sc = TRUE;
		$list = $this->sales_team_model->get_sap_data();

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

				if( ! $this->sales_team_model->is_exists_id($rs->id))
				{
					$this->sales_team_model->add($arr);
				}
				else
				{
					$this->sales_team_model->update($rs->id, $arr);
				}
			}
		}

		$this->_response($sc);
	}


  public function clear_filter()
	{
		return clear_filter('sales_team_name');			
	}
}
