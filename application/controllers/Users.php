<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends PS_Controller
{
	public $menu_code = 'USER';
	public $menu_group_code = 'ADMIN';
	public $title = 'Users';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'users';
		$this->load->model('sales_team_model');
		$this->load->model('price_list_model');
		$this->load->helper('sales_team_condition');
  }


	public function index()
	{
		$filter = array(
			'uname' => get_filter('uname', 'username', ''),
			'email' => get_filter('email', 'email', ''),
			'emp_id' => get_filter('emp_id', 'emp_id', 'all'),
			'sale_id' => get_filter('sale_id', 'sale_id', 'all'),
			'user_group' => get_filter('user_group', 'user_group', 'all'),
			'area_id' => get_filter('area_id', 'area_id', 'all'),
			'team_id' => get_filter('team_id', 'team_id', 'all'),
			'role' => get_filter('role', 'role', 'all'),
			'status' => get_filter('status', 'user_status', 'all')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_filter('set_rows', 'rows', 20);
			//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
			if($perpage > 300)
			{
				$perpage = get_filter('rows', 'rows', 300);
			}

			$segment = 3; //-- url segment
			$rows = $this->user_model->count_rows($filter);

			//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
			$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

			$data = $this->user_model->get_list($filter, $perpage, $this->uri->segment($segment));
			$teamName = [];
			$areaName = [];
			$groupName = [];

			if( ! empty($data))
			{
				$groups = $this->user_model->get_all_user_group();

				if( ! empty($groups))
				{
					foreach($groups as $ug)
					{
						$groupName[$ug->id] = $ug->name;
					}
				}


				$teams = $this->sales_team_model->get_all();

				if( ! empty($teams))
				{
					foreach($teams as $tm)
					{
						$teamName[$tm->id] = $tm->name;
					}
				}

				$areas = $this->user_model->get_all_area();

				if( ! empty($areas))
				{
					foreach($areas as $ar)
					{
						$areaName[$ar->id] = $ar->name;
					}
				}
			}

			$filter['data'] = $data;
			$filter['groupName'] = $groupName;
			$filter['teamName'] = $teamName;
			$filter['areaName'] = $areaName;

			$this->pagination->initialize($init);

			$this->load->view('users/users_list', $filter);
		}
	}


	public function add_new()
	{
		if($this->pm->can_add)
		{
			$ds['strong_pwd'] = getConfig('USE_STRONG_PWD');
			$ds['sale_list'] = $this->user_model->get_all_slp();
			$ds['price_list'] = $this->price_list_model->get_all();
			$ds['sales_team'] = $this->sales_team_model->get_all();

			$this->load->view('users/user_add', $ds);
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			$ds = json_decode($this->input->post('data'));

			if( ! empty($ds) && ! empty($ds->emp_name) && ! empty($ds->ugroup))
			{
				if( ! $this->user_model->is_exists_uname($ds->uname))
				{
					$arr = array(
						'uname' => $ds->uname,
						'pwd' => password_hash($ds->pwd, PASSWORD_DEFAULT),
						'uid' => md5(uniqid()),
						'emp_name' => $ds->emp_name,
						'emp_id' => get_null($ds->emp_id),
						'email' => get_null($ds->email),
						'sale_id' => get_null($ds->sale_id),
						'sale_name' => empty($ds->sale_id) ? NULL : $ds->sale_name,
						'ugroup_id' => $ds->ugroup,
						'area_id' => $ds->area_id,
						'team_id' => get_null($ds->team_id),
						'role' => $ds->role,
						'status' => $ds->status,
						'bi_link' => $ds->bi,
						'date_add' => now(),
						'add_by' => $this->_user->id
					);

					$user_id = $this->user_model->add($arr);

					if($user_id)
					{
						//--- insert user_customer_group
						if( ! empty($ds->team))
						{
							foreach($ds->team as $rs)
							{
								$arr = array(
									'user_id' => $user_id,
									'team_id' => $rs->id, //--- sales team id
									'user_role' => "Lead"
								);

								$this->user_model->add_user_team($arr);
								// $this->user_model->add_user_condition($arr);
							}
						}

						if( ! empty($ds->price_list))
						{
							foreach($ds->price_list as $pl)
							{
								$arr = array(
									'user_id' => $user_id,
									'list_id' => $pl->id,
									'list_name' => $pl->name
								);

								$this->user_model->add_user_price_list($arr);
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Insert user failed";
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "Username already exists";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = get_error_message('required');
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = get_error_message('permission');
		}

		$this->_response($sc);
	}


	public function is_exists_uname()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$uname = trim($this->input->post('uname'));

		if($this->user_model->is_exists_uname($uname, $id))
		{
			$sc = FALSE;
			$this->error = "Username already exists";
		}

		$this->_response($sc);
	}


	public function edit($id)
	{
		if($this->pm->can_edit)
		{
			$user = $this->user_model->get($id);

			if( ! empty($user))
			{

				$user_price_list = $this->user_model->get_user_price_list($id);

				$pl = array();

				if( ! empty($user_price_list))
				{
					foreach($user_price_list as $ps)
					{
						$pl[$ps->id] = $ps->id;
					}
				}

				$user->priceList = $pl;

				$user_team = $this->user_model->get_user_team($id);
				$uTeam = array();

				if( ! empty($user_team))
				{
					foreach($user_team as $ut)
					{
						$uTeam[$ut->team_id] = $ut->team_id;
					}
				}

				$user->user_team = $uTeam;

				$ds['user'] = $user;
				$ds['strong_pwd'] = getConfig('USE_STRONG_PWD');
				$ds['emp_list'] = $this->user_model->get_all_employee();
				$ds['sale_list'] = $this->user_model->get_all_slp();
				$ds['price_list'] = $this->price_list_model->get_all();
				$ds['sales_team'] = $this->sales_team_model->get_all();

				$this->load->view('users/user_edit', $ds);
			}
			else
			{
				$this->load->view('page_error');
			}
		}
		else
		{
			$this->deny_page();
		}
	}


	public function update()
	{
		$sc = TRUE;

		if($this->pm->can_edit)
		{
			$ds = json_decode($this->input->post('data'));

			if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->emp_id) && ! empty($ds->ugroup))
			{
				if($this->user_model->is_exists_uname($ds->uname, $ds->id))
				{
					$sc = FALSE;
					$this->error = "Username already exists";
				}

				if($sc === TRUE)
				{
					$this->db->trans_begin();

					$arr = array(
						'uname' => $ds->uname,
						'emp_name' => get_null($ds->emp_name),
						'emp_id' => get_null($ds->emp_id),
						'email' => get_null($ds->email),
						'sale_id' => get_null($ds->sale_id),
						'sale_name' => empty($ds->sale_id) ? NULL : $ds->sale_name,
						'ugroup_id' => $ds->ugroup,
						'area_id' => $ds->area_id,
						'team_id' => get_null($ds->team_id),
						'status' => $ds->status,
						'bi_link' => $ds->bi,
						'role' => $ds->role,
						'date_upd' => now(),
						'update_by' => $this->_user->id
					);

					if (! $this->user_model->update($ds->id, $arr))
					{
						$sc = FALSE;
						$this->error = "Failed to update user data";
					}

					if ($sc === TRUE)
					{
						//--- drop user price list
						if (! $this->user_model->drop_user_price_list($ds->id))
						{
							$sc = FALSE;
							$this->error = "Update user success but failed to remove previous user price list";
						}

						if ($sc === TRUE && ! empty($ds->price_list))
						{
							foreach ($ds->price_list as $rs)
							{
								$arr = array(
									'user_id' => $ds->id,
									'list_id' => $rs->id,
									'list_name' => $rs->name
								);

								$this->user_model->add_user_price_list($arr);
							}
						}
					}

					if ($sc === TRUE)
					{
						//--- drop exists user_condition
						if (! $this->user_model->drop_user_team($ds->id))
						{
							$sc = FALSE;
							$this->error = "Update user success but failed to remove previous user team lead";
						}

						//--- insert new user_team
						if ($sc === TRUE && ! empty($ds->team))
						{
							foreach ($ds->team as $rs)
							{
								$arr = array(
									'user_id' => $ds->id,
									'team_id' => $rs->id,
									'user_role' => "Lead"
								);

								$this->user_model->add_user_team($arr);								
							}
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
					}
					else 
					{
						$this->db->trans_rollback();
					}
				}				
			}
			else
			{
				$sc = FALSE;
				$this->error = get_error_message('required');
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = get_error_message('permission');
		}

		$this->_response($sc);
	}


	public function set_active()
	{
		$sc = TRUE;

		if($this->pm->can_edit)
		{
			$id = $this->input->post('id');
			$active = $this->input->post('active');

			$arr = array(
				'status' => $active,
				'date_upd' => now(),
				'update_by' => $this->_user->id
			);

			if( ! $this->user_model->update($id, $arr))
			{
				$sc = FALSE;
				$this->error = "Update failed";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = get_error_message('permission');
		}

		$this->_response($sc);
	}

	public function delete()
	{
		$sc = TRUE;

		if($this->pm->can_delete)
		{
			$id = $this->input->post('id');

			$user = $this->user_model->get($id);

			if( ! empty($user))
			{
				//--- check transection
				if($this->user_model->isApprover($user->id))
				{
					$sc = FALSE;
					$this->error = "Delete Failed : User is approver";
				}

				if($sc === TRUE && $this->user_model->has_order_transection($user->id))
				{
					$sc = FALSE;
					$this->error = get_error_message('transection', "{$user->uname}");
				}

				if($sc === TRUE)
				{
					$this->db->trans_begin();

					//--- remove condition
					if($sc === TRUE && ! $this->user_model->drop_user_team($user->id))
					{
						$sc = FALSE;
						$this->error = "Failed to remove user sales team condition";
					}

					if($sc === TRUE && ! $this->user_model->drop_user_price_list($user->id))
					{
						$sc = FALSE;
						$this->error = "Failed to remove user price list";
					}

					if($sc === TRUE && ! $this->user_model->delete($user->id))
					{
						$sc = FALSE;
						$this->error = "Failed to delete user";
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
					}
					else
					{
						$this->db->trans_rollback();
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = get_error_message('required');
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}


	//---- Reset password by Administrator
	public function reset_password($id)
	{
		$pm = get_permission('PWDRESET');
		if($pm->can_add OR $pm->can_edit OR $pm->can_delete)
		{
			$this->title = 'Reset Password';
			$rs = $this->user_model->get($id);
			if(!empty($rs))
			{
				$data['data'] = $rs;
				$data['use_strong_pwd'] = getConfig('USE_STRONG_PWD');
				$this->load->view('users/user_reset_pwd', $data);
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


	public function change_password()
	{
		$sc = TRUE;

		$pm = get_permission('PWDRESET');

		if($pm->can_add OR $pm->can_edit OR $pm->can_delete)
		{
			if(!empty($this->input->post('id')) && !empty($this->input->post('pwd')))
			{
				$pwd = trim($this->input->post('pwd'));

				if(!empty($pwd))
				{
					$id = $this->input->post('id');
					$pwd = password_hash($pwd, PASSWORD_DEFAULT);

					$arr = array(
					'pwd' => $pwd
					);

					if( ! $this->user_model->update($id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update Failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Password Can not be empty";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter !";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}


	public function get_sale_name($id)
	{
		$name = $this->user_model->get_saleman_name($id);
		return $name;
	}

	
	public function clear_filter()
	{
		$filter = array(
		'username',
		'email',
		'emp_id',
		'sale_id',
		'team_id',
		'area_id',
		'user_group',
		'user_status',
		'role'
		);

		return clear_filter($filter);
	}


}//--- end class


 ?>
