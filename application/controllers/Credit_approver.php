<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Credit_approver extends PS_Controller
{
	public $menu_code = 'CREDIT_APPROVER';
	public $menu_group_code = 'ADMIN';
	public $title = 'Credit Authorizer';
	public $segment = 3;

	public function __construct()
	{
		parent::__construct();
		$this->home = base_url() . 'credit_approver';
		$this->load->model('credit_approver_model');
	}


	public function index()
	{
		$this->title = "Credit Authorizer - List";

		$filter = array(
			'uname' => get_filter('uname', 'ap_uname', ''),
			'emp_name' => get_filter('emp_name', 'ap_emp_name', ''),
			'status' => get_filter('status', 'ap_status', 'all'),
			'can_approve' => get_filter('can_approve', 'ap_can_approve', 'all'),
			'can_review' => get_filter('can_review', 'ap_can_review', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		$rows = $this->credit_approver_model->count_rows($filter);
		$filter['data'] = $this->credit_approver_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
		$init	= pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
		$this->pagination->initialize($init);
		$this->load->view('credit_approver/approver_list', $filter);
	}


	public function add_new()
	{
		$this->title = "Authorizer - Add";

		if ($this->pm->can_add)
		{
			$this->load->view('credit_approver/approver_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;

		if ($this->pm->can_add)
		{
			$ds = json_decode($this->input->post('data'));

			if (! empty($ds) && ! empty($ds->uname))
			{
				$user = $this->user_model->get_user_by_uname($ds->uname);

				if (! empty($user))
				{
					//--- check exists approver
					if (! $this->credit_approver_model->is_exists($user->id))
					{
						if ($ds->amount <= 0 && $ds->can_approve == 1)
						{
							$sc = FALSE;
							$this->error = "Approve amount must be greater than 0";
						}

						if ($sc === TRUE)
						{
							$arr = array(
								'user_id' => $user->id,
								'uname' => $user->uname,
								'emp_name' => $user->emp_name,
								'amount' => $ds->amount,
								'status' => $ds->status == 1 ? 1 : 0,
								'can_approve' => $ds->can_approve == 1 ? 1 : 0,
								'can_review' => $ds->can_review == 1 ? 1 : 0,
								'date_add' => now(),
								'add_by' => $this->_user->id
							);

							if (! $this->credit_approver_model->add($arr))
							{
								$sc = FALSE;
								$this->error = "Failed to create approver";
							}
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

		if ($this->pm->can_edit)
		{
			$ap = $this->credit_approver_model->get($id);

			if (! empty($ap))
			{
				$ds['data'] = $ap;

				$this->load->view('credit_approver/approver_edit', $ds);
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

		if ($this->pm->can_edit)
		{
			$ds = json_decode($this->input->post('data'));

			if (! empty($ds) && ! empty($ds->id))
			{
				if ($ds->amount <= 0 && $ds->can_approve == 1)
				{
					$sc = FALSE;
					$this->error = "Approve amount must be greater than 0";
				}

				if ($sc === TRUE)
				{
					$arr = array(
						'amount' => $ds->amount,
						'status' => $ds->status == 1 ? 1 : 0,
						'can_approve' => $ds->can_approve == 1 ? 1 : 0,
						'can_review' => $ds->can_review == 1 ? 1 : 0,
						'date_upd' => now(),
						'update_by' => $this->_user->id
					);

					if (! $this->credit_approver_model->update($ds->id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update failed";
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
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}


	public function set_active()
	{
		$sc = TRUE;

		if ($this->pm->can_edit)
		{
			$id = $this->input->post('id');
			$active = $this->input->post('active');

			if (! empty($id))
			{
				$arr = array(
					'status' => $active == 1 ? 1 : 0,
					'date_upd' => now(),
					'update_by' => $this->_user->id
				);

				if (! $this->credit_approver_model->update($id, $arr))
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
			$this->error = "Missing permission";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function set_can_approve()
	{
		$sc = TRUE;

		if ($this->pm->can_edit)
		{
			$id = $this->input->post('id');
			$active = $this->input->post('can_approve');

			if (! empty($id))
			{
				$ap = $this->credit_approver_model->get($id);

				if (! empty($ap))
				{
					if ($ap->amount <= 0 && $active == 1)
					{
						$sc = FALSE;
						$this->error = "Approve amount must be greater than 0";
					}

					if ($sc === TRUE)
					{
						$arr = array(
							'can_approve' => $active == 1 ? 1 : 0,
							'date_upd' => now(),
							'update_by' => $this->_user->id
						);

						if (! $this->credit_approver_model->update($id, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to update status";
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Approver not found";
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
			$this->error = "Missing permission";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function set_can_review()
	{
		$sc = TRUE;

		if ($this->pm->can_edit)
		{
			$id = $this->input->post('id');
			$active = $this->input->post('can_review');

			if (! empty($id))
			{
				$arr = array(
					'can_review' => $active == 1 ? 1 : 0,
					'date_upd' => now(),
					'update_by' => $this->_user->id
				);

				if (! $this->credit_approver_model->update($id, $arr))
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
			$this->error = "Missing permission";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function delete()
	{
		$sc = TRUE;

		$id = $this->input->post('id');

		if ($this->pm->can_delete)
		{
			if (! empty($id))
			{
				$ap = $this->credit_approver_model->get($id);

				if (empty($ap))
				{
					$sc = FALSE;
					$this->error = get_error_message('notfound');
				}

				if ($sc === TRUE)
				{
					if (! $this->credit_approver_model->delete($id))
					{
						$sc = FALSE;
						$this->error = "Failed to delete approver";
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


	public function clear_filter()
	{
		$filter = array(
			'ap_uname',
			'ap_emp_name',
			'ap_status',
			'ap_amount',
			'ap_can_approve',
			'ap_can_review'
		);

		return clear_filter($filter);
	}
} //--- end class
