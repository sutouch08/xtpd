<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promotion extends PS_Controller
{
	public $menu_code = 'PROMOTION';
	public $menu_group_code = 'ADMIN';
	public $title = 'Promotion';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'promotion';
		$this->load->model('promotion_model');
		$this->load->model('item_model');
  }



  public function index()
  {
		$this->title = "Promotion - List";

		$filter = array(
			'code' => get_filter('code', 'pro_code', ''),
			'name' => get_filter('name', 'pro_name', ''),
			'start_date' => get_filter('start_date', 'start_date', ''),
			'end_date' => get_filter('end_date', 'end_date', ''),
			'status' => get_filter('status', 'pro_status', 'all')
		);

				//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->promotion_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$result = $this->promotion_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $result;

		$this->pagination->initialize($init);
    $this->load->view('promotion/promotion_list', $filter);
  }



	public function add_new()
	{
		$this->title = "Promotion - Add";

		if($this->pm->can_add)
		{
			$ds['code'] = $this->get_new_code();
			$ds['items'] = $this->item_model->get_item_list();

			$this->load->view('promotion/promotion_add', $ds);
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
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$status = $this->input->post('status') == 1 ? 1 : 0;
		$items = json_decode($this->input->post('items'));

		if(!empty($name) && !empty($start_date) && !empty($end_date) && !empty($items))
		{
			if($this->pm->can_add)
			{
				//--- gen new code
				$code = $this->get_new_code();

				if(!empty($code))
				{
					$arr = array(
						'code' => $code,
						'name' => $name,
						'start_date' => db_date($start_date),
						'end_date' => db_date($end_date),
						'status' => $status,
						'add_by' => $this->_user->id
					);


					$id = $this->promotion_model->add($arr);

					if(!$id)
					{
						$sc = FALSE;
						$this->error = "Insert data failed";
					}
					else
					{
						if(!empty($items))
						{
							foreach($items as $rs)
							{
								$arr = array(
									'promotion_id' => $id,
									'ItemCode' => $rs->itemCode,
									'ItemName' => $rs->itemName,
									'Qty' => $rs->qty,
									'SellPrice' => $rs->price,
									'UomCode' => get_null($rs->uom)
								);

								$this->promotion_model->add_detail($arr);
							}
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Error Cannot generate new Promotion code";
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
			$this->error = "Missing required parameter";
		}


		$this->_response($sc);
	}


	public function edit($id)
	{
		$this->title = "Promotion - Edit";

		if($this->pm->can_edit)
		{
			$rs = $this->promotion_model->get($id);

			if(!empty($rs))
			{
				$data['doc'] = $rs;
				$data['details'] = $this->promotion_model->get_details($id);
				$data['items'] = $this->item_model->get_item_list();
				$this->load->view('promotion/promotion_edit', $data);
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
		$name = trim($this->input->post('name'));
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$status = $this->input->post('status') == 1 ? 1 : 0;
		$items = json_decode($this->input->post('items'));

		if(!empty($name) && !empty($start_date) && !empty($end_date) && !empty($items))
		{
			if($this->pm->can_edit)
			{
				if(!empty($id))
				{
					$this->db->trans_begin();

					$arr = array(
						'name' => $name,
						'start_date' => db_date($start_date),
						'end_date' => db_date($end_date),
						'status' => $status,
						'update_by' => $this->_user->id
					);




					if(! $this->promotion_model->update($id, $arr))
					{
						$sc = FALSE;
						$this->error = "Update data failed";
					}
					else
					{
						if(!empty($items))
						{
							//--- drop current details
							if($this->promotion_model->delete_details($id))
							{
								foreach($items as $rs)
								{
									$arr = array(
										'promotion_id' => $id,
										'ItemCode' => $rs->itemCode,
										'ItemName' => $rs->itemName,
										'Qty' => $rs->qty,
										'SellPrice' => $rs->price,
										'UomCode' => get_null($rs->uom)
									);

									$this->promotion_model->add_detail($arr);
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "Update failed : Delete current promotion details failed";
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
					$this->error = "Invalid Promotion id";
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
			$this->error = "Missing required parameter";
		}


		$this->_response($sc);
	}




	public function preview($id)
	{
		$sc = TRUE;

		$ds = array();

		$doc = $this->promotion_model->get($id);

		if(!empty($doc))
		{
			$ds['code'] = $doc->code;
			$ds['name'] = $doc->name;
			$ds['start_date'] = thai_date($doc->start_date, FALSE, '.');
			$ds['end_date'] = thai_date($doc->end_date, FALSE, '.');
			$ds['status'] = $doc->status == 1 ? 'Active' : 'Disactive';
			$ds['items'] = array();

			$details = $this->promotion_model->get_details($id);
			if(!empty($details))
			{
				$no = 1;
				foreach($details as $rs)
				{
					$arr = array(
						'no' => $no,
						'ItemName' => $rs->ItemName,
						'Qty' => $rs->Qty,
						'Price' => $rs->SellPrice
					);

					array_push($ds['items'], $arr);
					$no++;
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Promotion Id";
		}


		echo $sc === TRUE ? json_encode($ds) : $this->error;
	}



	public function delete()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if(!empty($id))
		{
			$this->db->trans_begin();
			//--- delete details
			if(! $this->promotion_model->delete_details($id))
			{
				$sc = FALSE;
				$this->error = "Delete details failed";
			}
			else
			{
				if(! $this->promotion_model->delete($id))
				{
					$sc = FALSE;
					$this->erro = "Delete Promotion failed";
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
			$this->error = "Delete failed";
		}

		$this->_response($sc);
	}




	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $prefix = "PM";
    $run_digit = 4;
    $pre = $prefix .'-'.$Y;
    $code = $this->promotion_model->get_max_code($pre);
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
			'pro_code',
			'pro_name',
			'start_date',
			'end_date',
			'pro_status'
		);

		clear_filter($filter);
		echo 'done';
	}

}//--- end class


 ?>
