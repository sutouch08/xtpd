<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_term_discount extends PS_Controller
{
	public $menu_code = 'PMTDISC';
	public $menu_group_code = 'ADMIN';
	public $title = 'Payment Term Discount';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'payment_term_discount';
		$this->load->model('payment_term_discount_model');
		$this->load->model('price_list_model');
		$this->load->model('special_price_list_model');
		$this->load->helper('price_list');
		$this->load->helper('special_price_list');
		$this->load->helper('orders');
  }


	public function index()
	{
		$filter = array(
			'payment_term' => get_filter('payment_term', 'payment_term', 'all'),
			'name' => get_filter('name', 'payment_name', ''),
			'status' => get_filter('status', 'payment_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->payment_term_discount_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->payment_term_discount_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $result;

		$this->pagination->initialize($init);
		$this->load->view('payment_term_discount/payment_term_discount_list', $filter);
	}


	public function add_new()
	{
		if($this->pm->can_add)
		{
			$ds['priceList'] = $this->price_list_model->get_all();
			$ds['specialPriceList'] = $this->special_price_list_model->get_all_active();
			$this->load->view('payment_term_discount/payment_term_discount_add', $ds);
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

			if( ! empty($ds))
			{
				if( ! $this->payment_term_discount_model->is_exists_name($ds->name))
				{
					$arr = array(
						'name' => $ds->name,
						'GroupNum' => $ds->GroupNum,
						'PymntGroup' => $ds->PymntGroup,
						'DiscPrcnt' => $ds->DiscPrcnt,
						'canChange' => $ds->canChange,
						'active' => $ds->active,
						'position' => $ds->position,
						'add_by' => $this->_user->id,
						'date_add' => now()
					);

					$this->db->trans_begin();

					$id = $this->payment_term_discount_model->add($arr);

					if( ! $id )
					{
						$sc = FALSE;
						$this->error = get_error_message('insert');
					}

					if($sc === TRUE && ! empty($ds->priceList))
					{
						foreach($ds->priceList as $list)
						{
							$arr = array(
								'term_id' => $id,
								'list_id' => $list->id,
								'special_price_id' => $list->special_price_id
							);

							if( ! $this->payment_term_discount_model->add_price_list($arr))
							{
								$sc = FALSE;
								$this->error = "Failed to add price list";
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
				else
				{
					$sc = FALSE;
					$this->error = get_error_message('exists', $ds->name);
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
			$doc = $this->payment_term_discount_model->get($id);

			if( ! empty($doc))
			{
				$data['doc'] = $doc;
				$data['priceList'] = $this->user_model->get_all_price_list();
				$data['specialPriceList'] = $this->special_price_list_model->get_all_active();
				$term_price_list = $this->payment_term_discount_model->get_term_price_list($doc->id);
				$TPL = array();

				if( ! empty($term_price_list))
				{
					foreach($term_price_list as $p)
					{
						$TPL[$p->list_id][$p->special_price_id] = $p->list_id;
					}
				}

				$data['term_price_list'] = $TPL;

				$this->load->view('payment_term_discount/payment_term_discount_edit', $data);
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

		if($this->pm->can_edit)
		{
			$ds = json_decode($this->input->post('data'));

			if( ! empty($ds))
			{
				if( ! $this->payment_term_discount_model->is_exists_name($ds->name, $ds->id))
				{
					$arr = array(
						'name' => $ds->name,
						'GroupNum' => $ds->GroupNum,
						'PymntGroup' => $ds->PymntGroup,
						'DiscPrcnt' => $ds->DiscPrcnt,
						'canChange' => $ds->canChange,
						'active' => $ds->active,
						'position' => $ds->position,
						'update_by' => $this->_user->id,
						'date_upd' => now()
					);

					$this->db->trans_begin();

					if( ! $this->payment_term_discount_model->update($ds->id, $arr))
					{
						$sc = FALSE;
						$this->error = get_error_message('update');
					}

					if($sc === TRUE)
					{
						//---- delete current price list
						if( ! $this->payment_term_discount_model->drop_term_price_list($ds->id))
						{
							$sc = FALSE;
							$this->error = "Failed to drop current price list";
						}
					}

					if($sc === TRUE && ! empty($ds->priceList))
					{
						foreach($ds->priceList as $list)
						{
							$arr = array(
								'term_id' => $ds->id,
								'list_id' => $list->id,
								'special_price_id' => $list->special_price_id
							);

							if( ! $this->payment_term_discount_model->add_price_list($arr))
							{
								$sc = FALSE;
								$this->error = "Failed add price list";
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
				else
				{
					$sc = FALSE;
					$this->error = get_error_message('exists', $ds->name);
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


	public function view_detail($id)
	{
		$doc = $this->payment_term_discount_model->get($id);

		if( ! empty($doc))
		{
			$ds = array(
				'doc' => $doc,
				'term_list' => $this->payment_term_discount_model->get_term_price_details($doc->id),
				'special_term_list' => $this->payment_term_discount_model->get_term_special_price_details($doc->id)
			);

			$this->load->view('payment_term_discount/payment_term_discount_view_detail', $ds);
		}
		else
		{
			$this->error_page();
		}
	}


	public function delete()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if( ! empty($id))
		{
			if($this->pm->can_delete)
			{
				if( ! $this->payment_term_discount_model->delete($id))
				{
					$sc = FALSE;
					$this->error = "Failed to delete payment term List";
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

				if( ! $this->payment_term_discount_model->update($id, $arr))
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


  public function clear_filter()
	{
		$filter = array(
			'payment_term',
			'payment_name',
			'payment_status'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>
