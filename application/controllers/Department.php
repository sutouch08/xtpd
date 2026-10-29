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

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->department_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->department_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $result;

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
		$filter = array(
			'department_name'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>
