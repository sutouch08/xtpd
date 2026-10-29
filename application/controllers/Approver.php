<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approver extends PS_Controller{
	public $menu_code = 'APPROVER';
	public $menu_group_code = 'ADMIN';
	public $title = 'Authorizer';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'approver';
		$this->load->model('approver_model');
  }


	public function index()
	{
		$this->title = "Authorizer - List";

		$filter = array(
			'uname' => get_filter('uname', 'ap_uname', ''),
			'emp_name' => get_filter('emp_name', 'ap_emp_name', ''),			
			'status' => get_filter('status', 'ap_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->approver_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->approver_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);

		$this->load->view('approver/approver_list', $filter);
	}


	public function add_new()
	{
		$this->title = "Authorizer - Add";

		if($this->pm->can_add)
		{
			$this->load->view('approver/approver_add');
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

			if( ! empty($ds) && ! empty($ds->uname) && ! empty($ds->max_amount))
			{
				$user = $this->user_model->get_user_by_uname($ds->uname);

				if( ! empty($user))
				{
					//--- check exists approver
					if( ! $this->approver_model->is_exists($user->id))
					{
						$arr = array(
							'user_id' => $user->id,
							'uname' => $user->uname,
							'emp_name' => $user->emp_name,
							'min_amount' => (empty($ds->min_amount) OR $ds->min_amount < 0) ? 0 : floatval($ds->min_amount),
							'amount' => floatval($ds->max_amount),
							'status' => $ds->status == 1 ? 1 : 0,
							'date_add' => now(),
							'add_by' => $this->_user->id
						);

						if( ! $this->approver_model->add($arr))
						{
							$sc = FALSE;
							$this->error = "Failed to create approver";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Approver already exists";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid username";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Required Parameter";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing permission";
		}

		$this->_response($sc);
	}


	public function edit($id)
	{
		$this->title = "Authorizer - Edit";

		if($this->pm->can_edit)
		{
			$ap = $this->approver_model->get($id);

			if( ! empty($ap))
			{
				$ds['data'] = $ap;

				$this->load->view('approver/approver_edit', $ds);
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

			if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->max_amount))
			{
				$arr = array(
					'min_amount' => (empty($ds->min_amount) OR $ds->min_amount < 0) ? 0 : floatval($ds->min_amount),
					'amount' => floatval($ds->max_amount),
					'status' => $ds->status == 1 ? 1 : 0,
					'date_upd' => now(),
					'update_by' => $this->_user->id
				);

				if(! $this->approver_model->update($ds->id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}


	public function delete()
	{
		$sc = TRUE;

		$id = $this->input->post('id');

		if($this->pm->can_delete)
		{
			if( ! empty($id))
			{
				$ap = $this->approver_model->get($id);

				if(empty($ap))
				{
					$sc = FALSE;
					$this->error = get_error_message('notfound');
				}

				if($sc === TRUE)
				{
					$this->db->trans_begin();

					//--- delete approver
					if( ! $this->approver_model->delete($id))
					{
						$sc = FALSE;
						$this->error = "Failed to delete authorizer";
					}

					if($sc === TRUE && ! $this->approver_model->drop_approver_condition($ap->user_id))
					{
						$sc = FALSE;
						$this->error = "Failed to remove sales team condition link";
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
				$this->error = "Missing required parameter";
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
		$id = $this->input->post('id');
		$active = $this->input->post('active');

		if($this->pm->can_edit)
		{
			if( ! empty($id))
			{
				if( ! $this->approver_model->update($id, ['status' => $active]))
				{
					$sc = FALSE;
					$this->error = "Failed to update status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}

	public function clear_filter()
	{
		$filter = array(
			'ap_uname',
			'ap_emp_name',
			'ap_status',
			'ap_amount'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
