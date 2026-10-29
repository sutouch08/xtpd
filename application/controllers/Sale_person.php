<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale_person extends PS_Controller
{
	public $menu_code = 'SALE_PERSON';
	public $menu_group_code = 'ADMIN';
	public $title = 'Sales Person';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'sale_person';
		$this->load->model('sale_person_model');
  }


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'sale_person_name', '')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->sale_person_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->sale_person_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $result;

		$this->pagination->initialize($init);
		$this->load->view('sale_person/sale_person_list', $filter);
	}


	public function add_new()
	{
		if($this->pm->can_add)
		{
			$this->load->view('sale_person/sale_person_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;
		$name = trim($this->input->post('name'));

		if(!empty($name))
		{
			if($this->pm->can_add)
			{
				//--- check duplicate name
				if(! $this->sale_person_model->is_exists_name($name))
				{
					$arr = array(
						'name' => $name,
						'add_user' => $this->_user->id
					);

					if(!$this->sale_person_model->add($arr))
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
			$rs = $this->sale_person_model->get($id);

			if(!empty($rs))
			{

				$data['data'] = $rs;
				$this->load->view('sale_person/sale_person_edit', $data);
			}
			else
			{
				$this->error_page();
			}
		}
		else
		{
			$this->deny_page();
		}
	}


	public function update($id)
	{
		$sc = TRUE;

		if($this->pm->can_edit)
		{
			$name = trim($this->input->post('name'));

			if(!empty($name))
			{
				//--- check duplication
				if(! $this->sale_person_model->is_exists_name($name, $id))
				{
					$arr = array(
						'name' => $name,
						'update_user' => $this->_user->id
					);

					if(!$this->sale_person_model->update($id, $arr))
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
			if(! $this->sale_person_model->delete($id))
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
		$filter = array(
			'sale_person_name'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
