<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale_team extends PS_Controller
{
	public $menu_code = 'SALE_TEAM';
	public $menu_group_code = 'ADMIN';
	public $title = 'Sales Team';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'sale_team';
		$this->load->model('sale_team_model');
		$this->load->helper('sale_team');
		$this->load->helper('approve');
  }



  public function index()
  {
		$this->title = "Sales team - List";

		$filter = array(
			'name' => get_filter('name', 'team_name', '')
		);

				//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->sale_team_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->sale_team_model->get_list($filter, $perpage, $this->uri->segment($segment));

		if(!empty($result))
		{
			foreach($result as $rs)
			{
				$rs->member = $this->sale_team_model->count_members($rs->id);
				$customerGroup = $this->sale_team_model->get_team_customer_group($rs->id);
				$cg = "";
				$apv = "";

				if(!empty($customerGroup))
				{
					$i = 1;
					foreach($customerGroup as $group)
					{
						$name = $this->user_model->get_customer_group_name($group->group_id);
						$cg .= $i === 1 ? $name : ", ".$name;
						$i++;
					}
				}

				$rs->customerGroup = $cg;

				$approver = $this->sale_team_model->get_team_approver($rs->id);

				if(!empty($approver))
				{
					$i = 1;
					foreach($approver as $ap)
					{
						$apv .= $i === 1 ? $ap->emp_name : ", ".$ap->emp_name;
						$i++;
					}
				}

				$rs->approver_list = $apv;
			}
		}


    $filter['data'] = $result;

		$this->pagination->initialize($init);
    $this->load->view('sale_team/sale_team_list', $filter);
  }



	public function add_new()
	{
		$this->title = "Sales team - Add";

		if($this->pm->can_add)
		{
			$ds['customer_group'] = $this->user_model->get_customer_group_list();
			$this->load->view('sale_team/sale_team_add', $ds);
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
		$sale_person_id = $this->input->post('sale_person');
		$customer_team_id = $this->input->post('customer_team');

		if(!empty($name))
		{
			if($this->pm->can_add)
			{
				//--- check duplicate name
				if(! $this->sale_team_model->is_exists_name($name))
				{
					$arr = array(
						'name' => $name,
						'sale_person_id' => $sale_person_id,
						'customer_team_id' => $customer_team_id,
						'add_by' => $this->_user->id
					);

					$team_id = $this->sale_team_model->add($arr);

					if($team_id !== FALSE)
					{
						//--- insert user_customer_group
						if(!empty($this->input->post('customer_group')))
						{
							$customer_group = json_decode($this->input->post('customer_group'));

							if(!empty($customer_group))
							{
								foreach($customer_group as $rs)
								{
									$arr = array(
										'team_id' => $team_id,
										'group_id' => $rs->group_id,
										'sale_person_id' => $sale_person_id,
										'customer_team_id' => $customer_team_id
									);

									$this->sale_team_model->add_team_customer_group($arr);
								}
							}
						}


						//--- insert user_customer_group
						if(!empty($this->input->post('approver')))
						{
							$approver = json_decode($this->input->post('approver'));

							if(!empty($approver))
							{
								foreach($approver as $user_id)
								{
									$arr = array(
										'team_id' => $team_id,
										'user_id' => $user_id
									);

									$this->sale_team_model->add_team_approver($arr);
								}
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Insert data failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Duplicated Team Name. Please user another name";
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
			$this->error = "Missing required parameter : Team Name";
		}


		$this->_response($sc);
	}


	public function edit($id)
	{
		$this->title = "Sales team - Edit";

		if($this->pm->can_edit)
		{
			$rs = $this->sale_team_model->get($id);

			if(!empty($rs))
			{
				$team_customer_group = $this->sale_team_model->get_team_customer_group($id);
				$tgroup = array();

				if(!empty($team_customer_group))
				{
					foreach($team_customer_group as $cg)
					{
						$tgroup[$cg->group_id] =  $cg->group_id;
					}
				}

				$rs->tgroup = $tgroup;
				$rs->approver = $this->sale_team_model->get_team_approver($id);

				$data['data'] = $rs;
				$data['customer_group'] = $this->user_model->get_customer_group_list();

				$this->load->view('sale_team/sale_team_edit', $data);
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
			$sale_person_id = $this->input->post('sale_person');
			$customer_team_id = $this->input->post('customer_team');

			if(!empty($name))
			{
				//--- check duplication
				if(! $this->sale_team_model->is_exists_name($name, $id))
				{
					$arr = array(
						'name' => $name,
						'sale_person_id' => $sale_person_id,
						'customer_team_id' => $customer_team_id,
						'update_by' => $this->_user->id
					);

					if($this->sale_team_model->update($id, $arr))
					{
						//--- droup current team customer group
						$this->sale_team_model->drop_team_customer_group($id);

						//--- insert team customer group
						if(!empty($this->input->post('customer_group')))
						{
							$customer_group = json_decode($this->input->post('customer_group'));

							if(!empty($customer_group))
							{
								foreach($customer_group as $rs)
								{
									$arr = array(
										'team_id' => $id,
										'group_id' => $rs->group_id,
										'sale_person_id' => $sale_person_id,
										'customer_team_id' => $customer_team_id
									);

									$this->sale_team_model->add_team_customer_group($arr);
								}
							}
						}


						$this->sale_team_model->drop_team_approver($id);

						//--- insert user_customer_group
						if(!empty($this->input->post('approver')))
						{
							$approver = json_decode($this->input->post('approver'));

							if(!empty($approver))
							{
								foreach($approver as $user_id)
								{
									$arr = array(
										'team_id' => $id,
										'user_id' => $user_id
									);

									$this->sale_team_model->add_team_approver($arr);
								}
							}
						}
					}
					else
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


	public function get_member($id)
	{
		$members = $this->sale_team_model->get_members($id);

		if(!empty($members))
		{
			$ds = array();

			foreach($members as $rs)
			{
				$arr = array(
					'uname' => $rs->uname,
					'emp_name' => $rs->emp_name,
					'role' => $rs->user_role
				);

				array_push($ds, $arr);
			}

			echo json_encode($ds);
		}
		else
		{
			echo "No member";
		}
	}



	public function delete($id)
	{
		$sc = TRUE;

		if($this->pm->can_delete)
		{
			$this->db->trans_begin();

			//--- delete team_customer_group
			if(! $this->sale_team_model->drop_team_customer_group($id))
			{
				$sc = FALSE;
				$this->error = "Delete team customer group failed";
			}


			//--- delete team approver
			if(! $this->sale_team_model->drop_team_approver($id))
			{
				$sc = FALSE;
				$this->error = "Delete team approver failed";
			}

			//--- delete team_user
			if(! $this->sale_team_model->drop_user_team($id))
			{
				$sc = FALSE;
				$this->error = "Remove user in team failed";
			}


			if($sc === TRUE)
			{
				if(! $this->sale_team_model->delete($id))
				{
					$sc = FALSE;
					$this->error = "Delete team failed";
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
			'team_name'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
