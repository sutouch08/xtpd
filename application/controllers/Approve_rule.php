<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approve_rule extends PS_Controller{
	public $menu_code = 'APPROVE_RULE';
	public $menu_group_code = 'ADMIN';
	public $title = 'Exception rule';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'approve_rule';
		$this->load->model('approve_rule_model');
		$this->load->helper('approve');
		$this->load->helper('sales_team_condition');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'ar_code', ''),
			'conditions' => get_filter('conditions', 'ar_conditions', 'all'),
			'amount' => get_filter('amount', 'ar_amount', ''),
			'sale_team' => get_filter('sale_team', 'ar_sale_team', 'all'),
			'is_price_list' => get_filter('is_price_list', 'ar_is_price_list', 'all'),
			'status' => get_filter('status', 'ar_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->approve_rule_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->approve_rule_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $rs;

		$this->pagination->initialize($init);

    $this->load->view('approve_rule/approve_rule_list', $filter);
  }



	public function add_new()
	{
		if($this->pm->can_add)
		{
			$ds['code'] = $this->get_new_code();

			$this->load->view('approve_rule/approve_rule_add', $ds);
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

			if( ! empty($ds) && ! empty($ds->conditions) && ! empty($ds->sale_team))
			{
				$code = $this->get_new_code();

				$arr = array(
					'code' => $code,
					'conditions' => $ds->conditions,
					'amount' => $ds->amount,
					'sale_team' => $ds->sale_team,
					'is_price_list' => $ds->is_price_list,
					'status' => $ds->status,
					'add_by' => $this->_user->id,
					'date_add' => now()
				);

				if(!$this->approve_rule_model->add($arr))
				{
					$sc = FALSE;
					$this->error = "Insert approve rule failed";
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


	public function edit($id)
	{		
		if($this->pm->can_edit)
		{
			$rs = $this->approve_rule_model->get($id);

			if(!empty($rs))
			{
				$ds['data'] = $rs;
				$this->load->view('approve_rule/approve_rule_edit', $ds);
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

			if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->conditions) && ! empty($ds->sale_team))
			{
				$arr = array(
					'conditions' => $ds->conditions,
					'amount' => $ds->amount,
					'sale_team' => $ds->sale_team,
					'is_price_list' => $ds->is_price_list,
					'status' => $ds->status,
					'update_by' => $this->_user->id,
					'date_upd' => now()
				);

				if( ! $this->approve_rule_model->update($ds->id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
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


	public function delete()
	{
		$sc = TRUE;
		if($this->pm->can_delete)
		{
			if($this->input->post('id'))
			{
				$id = $this->input->post('id');

				if(! $this->approve_rule_model->delete($id))
				{
					$sc = FALSE;
					$this->error = "Delete failed";
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



	public function get_rule_approver_list($rule_id)
	{
		$list = $this->approve_rule_model->get_rule_approver($rule_id);

		$ds = array();

		if(!empty($list))
		{
			foreach($list as $rs)
			{
				$arr = array(
					'uname' => $rs->uname,
					'emp_name' => $rs->emp_name
				);

				array_push($ds, $arr);
			}
		}

		echo count($ds) > 0 ? json_encode($ds) : NULL;
	}



	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $prefix = "RL";
    $run_digit = 3;
    $pre = $prefix .'-'.$Y;
    $code = $this->approve_rule_model->get_max_code($pre);
    if(! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


	public function clear_filter()
	{
		$filter = array(
			'ar_code',
			'ar_conditions',
			'ar_amount',
			'ar_is_price_list',
			'ar_sale_team',
			'ar_status'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
