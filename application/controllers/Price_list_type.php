<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Price_list_type extends PS_Controller
{
	public $menu_code = 'SPLTYPE';
	public $menu_group_code = 'ADMIN';
	public $title = 'Price List Type';
	public $segment = 3;

	public function __construct()
	{
		parent::__construct();
		$this->home = base_url() . 'price_list_type';
		$this->load->model('price_list_type_model');		
	}


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'price_list_type_name', ''),
			'active' => get_filter('active', 'price_list_type_active', 'all')
		);

		if ($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			$perpage = get_rows();

			$segment = $this->segment; //-- url segment
			$rows = $this->price_list_type_model->count_rows($filter);
			$filter['data'] = $this->price_list_type_model->get_list($filter, $perpage, $this->uri->segment($segment));
			$init  = pagination_config($this->home . '/index/', $rows, $perpage, $segment);
			$this->pagination->initialize($init);
			$this->load->view('price_list_type/price_list_type_list', $filter);
		}
	}


	public function add_new()
	{		
		$this->load->view('price_list_type/price_list_type_add');
	}


	public function add()
	{
		$sc = TRUE;
		$name = trim($this->input->post('name'));
		$active = $this->input->post('status') ? 1 : 0;

		if ( ! empty($name))
		{
			if ($this->price_list_type_model->is_exists_name($name))
			{
				$sc = FALSE;
				set_error('Type name already exists');
			}

			if ($sc === TRUE)
			{
				$arr = array(
					'name' => $name,
					'active' => $active,
					'create_by' => $this->_user->id
				);

				if (! $this->price_list_type_model->add($arr))
				{
					$sc = FALSE;
					set_error('Insert failed');
				}
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		$this->_response($sc);
	}


	public function edit($id)
	{
		$type = $this->price_list_type_model->get($id);

		if (!empty($type))
		{			
			$ds['data'] = $type;

			$this->load->view('price_list_type/price_list_type_edit', $ds);
		}
		else
		{
			$this->page_error();
		}
	}


	public function update()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$name = trim($this->input->post('name'));
		$active = $this->input->post('status') ? 1 : 0;

		if (! empty($id) && ! empty($name))
		{
			if ($this->price_list_type_model->is_exists_name($name, $id))
			{
				$sc = FALSE;
				set_error('Type name already exists');
			}

			if ($sc === TRUE)
			{
				$arr = array(
					'name' => $name,
					'active' => $active,
					'update_by' => $this->_user->id
				);

				if (! $this->price_list_type_model->update($id, $arr))
				{
					$sc = FALSE;
					set_error('Update failed');
				}				
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		$this->_response($sc);
	}


	public  function update_position()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$position = $this->input->post('position');

		if (!empty($id) && !empty($position))
		{
			if (! $this->price_list_type_model->update($id, array('position' => $position)))
			{
				$sc = FALSE;
				set_error('Update failed');
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		echo $sc === TRUE ? 'success' : 'failed';
	}


	public function delete()
	{
		$sc = TRUE;

		if ($this->pm->can_delete)
		{
			$id = $this->input->post('id');

			if ($id)
			{
				if (! $this->price_list_type_model->delete($id))
				{
					$sc = FALSE;
					set_error('Delete failed');
				}				
			}
			else
			{
				$sc = FALSE;
				set_error('No type id found');
			}
		}
		else
		{
			$sc = FALSE;
			set_error('You do not have permission to delete');
		}

		$this->_response($sc);
	}


	public function setActive()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$active = $this->input->post('active');

		$arr = array(
			'active' => $active,
			'update_by' => $this->_user->id
		);

		if (! $this->price_list_type_model->update($id, $arr))
		{
			$sc = FALSE;
			set_error('Update failed');
		}

		echo $sc === TRUE ? 'success' : 'failed';
	}


	public function clear_filter()
	{
		$filter = array(
			'price_list_type_name',
			'price_list_type_active'
		);

		return clear_filter($filter);
	}
} //--- end class
