<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group extends PS_Controller
{
	public $menu_code = 'USERGROUP';
	public $menu_group_code = 'ADMIN';
	public $title = 'Users Group';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'user_group';
		$this->load->model('user_group_model');
  }



  public function index()
  {
		$this->title = "Users Group - List";

		$filter = array(
			'name' => get_filter('name', 'ugroup_name', '')
		);

				//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->user_group_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->user_group_model->get_list($filter, $perpage, $this->uri->segment($segment));

		if(!empty($result))
		{
			foreach($result as $rs)
			{
				$rs->member = $this->user_group_model->count_members($rs->id);
			}
		}


    $filter['data'] = $result;

		$this->pagination->initialize($init);
    $this->load->view('user_group/user_group_list', $filter);
  }



	public function add_new()
	{
		$this->title = "Users Group - Add";

		if($this->pm->can_add)
		{
			$this->load->view('user_group/user_group_add');
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
				if(! $this->user_group_model->is_exists_name($name))
				{
					$arr = array(
						'name' => $name,
						'add_by' => $this->_user->uname
					);

					if(!$this->user_group_model->add($arr))
					{
						$sc = FALSE;
						$this->error = "Insert data failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Duplicated Group name. Please user another name";
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
			$this->error = "Missing required parameter : Group Name";
		}


		$this->_response($sc);
	}


	public function edit($id)
	{
		$this->title = "Users Group - Edit";

		if($this->pm->can_edit)
		{
			$rs = $this->user_group_model->get($id);

			if(!empty($rs))
			{
				$data['data'] = $rs;
				$this->load->view('user_group/user_group_edit', $data);
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
				if(! $this->user_group_model->is_exists_name($name, $id))
				{
					$arr = array(
						'name' => $name,
						'update_by' => $this->_user->uname
					);

					if(!$this->user_group_model->update($id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update data failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Duplicated User group name. Please use another name";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : Group Name";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}


		$this->_response($sc);
	}


	public function get_detail($id)
	{
		$rs = $this->user_group_model->get($id);

		if(!empty($rs))
		{
			$arr = array(
				'id' => $rs->id,
				'name' => $rs->name,
				'member' => number($this->user_group_model->count_members($rs->id)),
				'add_by' => $this->user_model->get_name($rs->add_by),
				'date_add' => thai_date($rs->date_add, TRUE),
				'update_by' => empty($rs->update_by) ? "-" : $this->user_model->get_name($rs->update_by),
				'date_upd' => empty($rs->date_upd) ? "-" : thai_date($rs->date_upd, TRUE),
				'permission' => $this->get_permission_view($rs->id)
			);

			echo json_encode($arr);
		}
		else
		{
			echo "Not found";
		}
	}



	public function edit_permission($id)
	{
		$ugroup = $this->user_group_model->get($id);

		if(!empty($ugroup))
		{
			$this->title = "Edit Permission - ".$ugroup->name;
			$pm = get_permission('PERMISSION');

			if($pm->can_add OR $pm->can_edit OR $pm->can_delete)
			{
				$this->load->model('menu');
				$this->load->model('permission_model');

				$data['data'] = $ugroup;
				$data['menus'] = array();
				$groups = $this->menu->get_menu_groups();

				if(!empty($groups))
				{
					foreach($groups as $group)
					{
						$ds = array(
							'group_code' => $group->code,
							'group_name' => $group->name,
							'menu' => ''
						);

						$menus = $this->menu->get_menus_by_group($group->code);

						if(!empty($menus))
		        {
		          $item = array();

		          foreach($menus as $menu)
		          {
								if($menu->valid)
								{
									$arr = array(
			              'menu_code' => $menu->code,
			              'menu_name' => $menu->name,
			              'permission' => $this->permission_model->get_permission($menu->code, $id)
			            );

			            array_push($item, $arr);
								}
		          }

		          $ds['menu'] = $item;
		        }

		        array_push($data['menus'], $ds);
					}
				}

				$this->load->view('user_group/permission_edit', $data);

			}
			else
			{
				$this->deny_page();
			}
		}
		else
		{
			$this->error_page();
		}

	}



	public function update_permission()
	{
		$sc = TRUE;

		if($this->input->post('ugroup_id'))
		{
			$pmd = get_permission('PERMISSION');

			if($pmd->can_add OR $pmd->can_edit OR $pmd->can_delete)
			{
				$this->load->model('permission_model');

				$ugroup_id = $this->input->post('ugroup_id');
				$menu = $this->input->post('menu');
				$view = $this->input->post('view');
				$add = $this->input->post('add');
				$edit = $this->input->post('edit');
				$delete = $this->input->post('delete');

				$this->permission_model->drop_permission($ugroup_id);

				if(!empty($menu))
				{
					foreach($menu as $code)
					{
						$pm = array(
							'menu' => $code,
							'ugroup_id' => $ugroup_id,
							'can_view' => isset($view[$code]) ? 1 : 0,
							'can_add' => isset($add[$code]) ? 1 : 0,
							'can_edit' => isset($edit[$code]) ? 1 : 0,
							'can_delete' => isset($delete[$code]) ? 1 : 0,
						);

						$this->permission_model->add($pm);
					}
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
			$this->error = "Missing required parameter : ugroup_id";
		}

		$this->_response($sc);
	}


	public function get_permission_view($id)
	{
		$this->load->model('menu');
		$this->load->model('permission_model');

		$sc = array();

		$groups = $this->menu->get_menu_groups();

		if(!empty($groups))
		{

			foreach($groups as $group)
			{
				$mgroup = array(
					'group_name' => $group->name,
					'menu' => array()
				);

				$menus = $this->menu->get_menus_by_group($group->code);

				if(!empty($menus))
				{
					foreach($menus as $menu)
					{
						if($menu->valid)
						{
							$pm = $this->permission_model->get_permission($menu->code, $id);
							$arr = array(
								'menu_name' => $menu->name,
								'can_view' => is_true($pm->can_view),
								'can_add' => is_true($pm->can_add),
								'can_edit' => is_true($pm->can_edit),
								'can_delete' => is_true($pm->can_delete)
							);

							array_push($mgroup['menu'], $arr);


						}
					}
				}

				array_push($sc, $mgroup);
			}
		}

		return $sc;
	}



  public function clear_filter()
	{
		$filter = array(
			'ugroup_name'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
