<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Step_rule extends PS_Controller
{
	public $menu_code = 'STEPRULE';
	public $menu_group_code = 'ADMIN';
	public $title = 'Price List Step Rule';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'step_rule';
		$this->load->model('step_rule_model');
		$this->load->helper('price_list');
		//$this->load->helper('orders');
  }


	public function index()
	{
		$filter = array(
			'price_list' => get_filter('price_list', 'step_price_list', 'all'),
			'name' => get_filter('name', 'step_name', ''),
			'status' => get_filter('status', 'step_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->step_rule_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->step_rule_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $result;
		$filter['priceList'] = $this->user_model->get_all_price_list_array();

		$this->pagination->initialize($init);
		$this->load->view('step_rule/step_rule_list', $filter);
	}


	public function add_new()
	{
		if($this->pm->can_add)
		{
			$this->load->view('step_rule/step_rule_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if($this->pm->can_add)
		{
			if( ! empty($ds) && ! empty($ds->price_list) && ! empty($ds->name))
			{
				if( ! $this->step_rule_model->is_exists_price_list($ds->price_list))
				{
					$arr = array(
						'PriceList' => $ds->price_list,
						'name' => trim($ds->name),
						'active' => $ds->active == 1 ? 1 : 0,
						'add_by' => $this->_user->id,
						'date_add' => now()
					);

					if( ! $this->step_rule_model->add($arr))
					{
						$sc = FALSE;
						$this->error = get_error_message('insert');
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Price List {$ds->price_list_name} already exists";
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


	public function edit($price_list, $tab = 'info')
	{
		if($this->pm->can_add OR $this->pm->can_edit)
		{
			$doc = $this->step_rule_model->get($price_list);

			if( ! empty($doc))
			{
				$ds = array(
					'tab' => $tab,
					'doc' => $doc,
					'details' => $this->step_rule_model->get_details($price_list)
				);
				
				$this->load->view('step_rule/step_rule_edit', $ds);
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


	public function update()
	{
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if($this->pm->can_add OR $this->pm->can_edit)
		{
			if( ! empty($ds) && ! empty($ds->price_list) && ! empty($ds->name))
			{
				$doc = $this->step_rule_model->get($ds->price_list);

				if( ! empty($doc))
				{
					$arr = array(
						'name' => trim($ds->name),
						'active' => $ds->active == 1 ? 1 : 0,
						'update_by' => $this->_user->id,
						'date_upd' => now()
					);

					if( ! $this->step_rule_model->update($doc->PriceList, $arr))
					{
						$sc = FALSE;
						$this->error = get_error_message('update');
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = get_error_message('notfound');
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

	public function update_details()
	{
		$sc = TRUE;

		if($this->pm->can_add OR $this->pm->can_edit)
		{
			$ds = json_decode($this->input->post('data'));

			if( ! empty($ds))
			{
				$doc = $this->step_rule_model->get($ds->price_list);

				if( ! empty($doc))
				{
					$this->db->trans_begin();
					
					//--- delete select rows
					if($sc === TRUE && ! empty($ds->delete_rows))
					{
						if( ! $this->step_rule_model->delete_detail_by_ids($ds->delete_rows))
						{
							$sc = FALSE;
							$this->error = "Failed to delete rows";
						}
					}

					//---- add or update row
					if($sc === TRUE && ! empty($ds->rows))
					{
						foreach($ds->rows as $rs)
						{
							//--- update if id exists
							if( ! empty($rs->id))
							{
								$arr = array(
									'labelText' => $rs->label,
									'stepQty' => $rs->stepQty,
									'limitQty' => $rs->limitQty,
									'freeQty' => $rs->freeQty,
									'active' => $rs->active == 1 ? 1 : 0,
									'highlight' => $rs->highlight == 1 ? 1 : 0,
									'position' => empty($rs->position) ? 10 : $rs->position,
									'is_force' => $rs->is_force == 1 ? 1 : 0,
									'date_upd' => now(),
									'update_by' => $this->_user->id
								);

								if( ! $this->step_rule_model->update_detail($rs->id, $arr))
								{
									$sc = FALSE;
									$this->error = "Failed to update step {$rs->label}";
								}
							}
							else
							{
								$arr = array(
									'PriceList' => $ds->price_list,
									'labelText' => $rs->label,
									'stepQty' => $rs->stepQty,
									'limitQty' => $rs->limitQty,
									'freeQty' => $rs->freeQty,
									'active' => $rs->active == 1 ? 1 : 0,
									'highlight' => $rs->highlight == 1 ? 1 : 0,
									'position' => empty($rs->position) ? 10 : $rs->position,
									'is_force' => $rs->is_force == 1 ? 1 : 0,
									'date_add' => now(),
									'add_by' => $this->_user->id
								);

								if( ! $this->step_rule_model->add_detail($arr))
								{
									$sc = FALSE;
									$this->error = "Failed to insert step {$rs->label}";
								}
							}

							if($sc === FALSE)
							{
								break;
							}
						} //-- end foreach
					} //-- end add or update row

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
					$this->error = get_error_message('notfound');
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


	public function view_detail($price_list)
	{
		$doc = $this->step_rule_model->get($price_list);

		if( ! empty($doc))
		{
			$data['doc'] = $doc;
			$data['details'] = $this->step_rule_model->get_details($price_list);
			$this->load->view('step_rule/step_rule_view_detail', $data);
		}
		else
		{
			$this->error_page();
		}
	}


	public function delete()
	{
		$sc = TRUE;
		$priceList = $this->input->post('price_list');

		if( ! empty($priceList))
		{
			if($this->pm->can_delete)
			{
				$this->db->trans_begin();

				if( ! $this->step_rule_model->delete_details($priceList))
				{
					$sc = FALSE;
					$this->error = "Failed to delete step rules";
				}

				if($sc === TRUE)
				{
					if( ! $this->step_rule_model->delete($priceList))
					{
						$sc = FALSE;
						$this->error = "Failed to delete Price List";
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
				$this->error = get_error_message('permission');
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = get_error_message('required');
		}

		$this->_response($sc);
	}


	public function set_active()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$active = $this->input->post('active');

		if( ! empty($id))
		{
			if($this->pm->can_edit)
			{
				$arr = array(
					'active' => $active,
					'update_by' => $this->_user->id,
					'date_upd' => now()
				);

				if( ! $this->step_rule_model->update($id, $arr))
				{
					$sc = FALSE;
					$this->error = get_error_message('update');
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = get_error_message('permission');
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = get_error_message('required');
		}

		$this->_response($sc);
	}


	public function is_exists($price_list)
	{
		if($this->step_rule_model->is_exists_price_list($price_list))
		{
			echo 'exists';
		}
		else
		{
			echo 'not exists';
		}
	}


  public function clear_filter()
	{
		$filter = array(
			'step_price_list',
			'step_name',
			'step_status'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>
