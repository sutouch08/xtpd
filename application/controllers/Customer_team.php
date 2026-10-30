<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_team extends PS_Controller
{
	public $menu_code = 'CUSTOMER_TEAM';
	public $menu_group_code = 'ADMIN';
	public $title = 'Department';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'customer_team';
		$this->load->model('customer_team_model');
  }

  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'customer_team_name', '')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
			exit;
		}

		$perpage = get_rows();
		$segment = 3; //-- url segment
		$rows = $this->customer_team_model->count_rows($filter);
		$filter['data'] = $this->customer_team_model->get_list($filter, $perpage, $this->uri->segment($segment));		
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$this->pagination->initialize($init);
    $this->load->view('customer_team/customer_team_list', $filter);
  }

	public function add_new()
	{
		if($this->pm->can_add) $this->load->view('customer_team/customer_team_add');
		else $this->deny_page(TRUE);
	}

	public function add()
	{
		$sc = TRUE;
		$name = trim($this->input->post('name') ?? '');

		if(!empty($name))
		{
			if($this->pm->can_add)
			{
				//--- check duplicate name
				if(! $this->customer_team_model->is_exists_name($name))
				{
					$arr = array(
						'name' => $name,
						'add_user' => $this->_user->id
					);

					if(!$this->customer_team_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Insert data failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Duplicated name. Please use another name";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : Name";
		}

		$this->_response($sc);
	}

	public function edit($id)
	{		
		if($this->pm->can_edit)
		{
			$rs = $this->customer_team_model->get($id);

			if(!empty($rs))
			{
				$data['data'] = $rs;
				$this->load->view('customer_team/customer_team_edit', $data);
			}
			else
			{
				$this->error_page();
			}
		}
		else
		{
			$this->deny_page(TRUE);
		}
	}

	public function update($id)
	{
		$sc = TRUE;

		if($this->pm->can_edit)
		{
			$name = trim($this->input->post('name') ?? '');

			if(!empty($name))
			{
				//--- check duplication
				if(! $this->customer_team_model->is_exists_name($name, $id))
				{
					$arr = array(
						'name' => $name,
						'update_user' => $this->_user->id
					);

					if(!$this->customer_team_model->update($id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Duplicated name. Please use another name";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : Name";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}

	public function delete($id)
	{
		$sc = TRUE;

		if($this->pm->can_delete)
		{
			if(! $this->customer_team_model->delete($id))
			{
				$sc = FALSE;
				$this->error = "Delete team failed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing permission";
		}

		$this->_response($sc);
	}

  public function clear_filter()
	{
		return clear_filter('customer_team');	
	}

}
