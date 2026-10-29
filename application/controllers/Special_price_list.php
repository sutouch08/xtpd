<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Special_price_list extends PS_Controller
{
	public $menu_code = 'PDSPPL';
	public $menu_group_code = 'ADMIN';
	public $title = 'Special Price List';
	public $segment = 3;

	public function __construct()
	{
		parent::__construct();
		$this->home = base_url() . 'special_price_list';
		$this->load->model('special_price_list_model');
		$this->load->model('item_model');
		$this->load->helper('special_price_list');
	}


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'sp_name', ''),
			'type' => get_filter('type', 'sp_type', 'all'),
			'start_date' => get_filter('start_date', 'sp_start_date', ''),
			'end_date' => get_filter('end_date', 'sp_end_date', ''),
			'status' => get_filter('status', 'sp_status', 'all')
		);

		if ($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = set_rows();
			$rows = $this->special_price_list_model->count_rows($filter);
			$filter['data'] = $this->special_price_list_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
			$init	= pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
			$this->pagination->initialize($init);
			$this->load->view('special_price_list/special_price_list', $filter);
		}
	}


	public function add_new()
	{
		if ($this->pm->can_add)
		{
			$this->load->view('special_price_list/special_price_add');
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

			if (! empty($ds))
			{
				if (! $this->special_price_list_model->is_exists_name($ds->name))
				{
					$start_date = empty($ds->start_date) ? NULL : date('Y-m-d H:i:s', strtotime($ds->start_date));
					$end_date = empty($ds->end_date) ? NULL : date('Y-m-d H:i:s', strtotime($ds->end_date));
					$arr = array(
						'name' => $ds->name,
						'type_id' => $ds->type,
						'active' => $ds->active,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'date_add' => now(),
						'add_by' => $this->_user->id
					);

					$id = $this->special_price_list_model->add($arr);

					if (! $id)
					{
						$sc = FALSE;
						set_error('insert');
					}
				}
				else
				{
					$sc = FALSE;
					set_error('exists', $ds->name);
				}
			}
			else
			{
				$sc = FALSE;
				set_error('required');
			}
		}
		else
		{
			$sc = FALSE;
			set_error('permission');
		}

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'id' => $sc === TRUE ? $id : NULL
		);

		echo json_encode($arr);
	}


	public function edit($id, $tab = 'info')
	{
		if ($this->pm->can_add or $this->pm->can_edit)
		{
			$doc = $this->special_price_list_model->get($id);

			if (! empty($doc))
			{
				$this->load->model('customer_group_model');
				$groupsSelected = [];
				$customerGroups = $this->special_price_list_model->get_price_list_customer_groups($id);

				if( ! empty($customerGroups))
				{
					foreach($customerGroups as $rs)
					{
						$groupsSelected[$rs->customer_group_id] = $rs->customer_group_id;
					}
				}
				
				$ds = array(
					'tab' => $tab,
					'doc' => $doc,
					'details' => $this->special_price_list_model->get_details($id),
					'itemList' => $this->item_model->get_item_list(),
					'customerGroups' => $groupsSelected,
					'customerGroupList' => $this->customer_group_model->get_all_active()
				);

				$this->load->view('special_price_list/special_price_edit', $ds);
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

		if ($this->pm->can_add or $this->pm->can_edit)
		{
			$ds = json_decode($this->input->post('data'));

			if (! empty($ds))
			{
				$doc = $this->special_price_list_model->get($ds->id);

				if (! empty($doc))
				{
					if($ds->all_customer == 0 && empty($ds->customer_groups))
					{
						$sc = FALSE;
						$this->error = "Please select at least one customer group";
					}
					
					if($sc === TRUE && $this->special_price_list_model->is_exists_name($ds->name, $ds->id))
					{
						$sc = FALSE;
						$this->error = get_error_message('exists', $ds->name);
					}
					
					if($sc === TRUE)
					{
						$this->db->trans_begin();

						$arr = array(
							'name' => trim($ds->name),
							'type_id' => $ds->type,
							'start_date' => empty($ds->start_date) ? NULL : date('Y-m-d H:i:s', strtotime($ds->start_date)),
							'end_date' => empty($ds->end_date) ? NULL : date('Y-m-d H:i:s', strtotime($ds->end_date)),
							'active' => $ds->active,
							'all_customer' => $ds->all_customer,
							'date_upd' => now(),
							'update_by' => $this->_user->id
						);

						if (! $this->special_price_list_model->update($ds->id, $arr))
						{
							$sc = FALSE;
							$this->error = get_error_message('update');
						}

						if($sc === TRUE)
						{
							//-- delete previous customer groups
							if( ! $this->special_price_list_model->delete_price_list_customer_groups($ds->id))
							{
								$sc = FALSE;
								$this->error = "Failed to delete previous customer groups";
							}							
						}

						if($sc === TRUE && $ds->all_customer == 0 && ! empty($ds->customer_groups))
						{
							foreach($ds->customer_groups as $group_id)
							{
								$arr = array(
									'price_list_id' => $ds->id,
									'customer_group_id' => $group_id
								);

								if( ! $this->special_price_list_model->add_price_list_customer_group($arr))
								{
									$sc = FALSE;
									$this->error = "Failed to add customer group";
									break;
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


	public function add_item()
	{
		$sc = TRUE;

		if ($this->pm->can_add or $this->pm->can_edit)
		{
			$price_list_id = $this->input->post('id');
			$itemCode = $this->input->post('itemCode');
			$itemName = $this->input->post('itemName');

			if (! empty($itemCode))
			{
				$doc = $this->special_price_list_model->get($price_list_id);

				if (! empty($doc))
				{
					if (! $this->special_price_list_model->is_exists_item($itemCode, $price_list_id))
					{
						$item = $this->item_model->get_item($itemCode);

						$arr = array(
							'price_list_id' => $price_list_id,
							'ItemCode' => $item->code,
							'ItemName' => $item->name,
							'UomCode' => $item->uom,
							'isControl' => $item->U_BEX_Controll == 'Controlled' ? 'Y' : 'N',
							'add_by' => $this->_user->id,
							'date_add' => now()
						);

						$id = $this->special_price_list_model->add_item($arr);

						if (! $id)
						{
							$sc = FALSE;
							$this->error = get_error_message('insert');
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = get_error_message('exists', $itemName);
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

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'id' => $sc === TRUE ? $id : NULL,
			'code' => $sc === TRUE ? $itemCode : NULL,
			'name' => $sc === TRUE ? $itemName : NULL
		);

		echo json_encode($arr);
	}


	public function edit_item($id)
	{
		if ($this->pm->can_add or $this->pm->can_edit)
		{
			$item = $this->special_price_list_model->get_item_by_id($id);

			if (! empty($item))
			{
				$this->title = $this->title . " - " . $item->price_list_name;
				$data['item'] = $item;
				$data['details'] = $this->special_price_list_model->get_item_details($id);

				$this->load->view('special_price_list/special_price_item_edit', $data);
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


	public function save_item()
	{
		$sc = TRUE;

		if ($this->pm->can_add or $this->pm->can_edit)
		{
			$ds = json_decode($this->input->post('data'));

			if (! empty($ds))
			{
				$item = $this->special_price_list_model->get_item_by_id($ds->id);

				if (! empty($item))
				{
					$this->db->trans_begin();
					//--- delete previous rows					
					if (! $this->special_price_list_model->delete_details($ds->id))
					{
						$sc = FALSE;
						$this->error = "Failed to delete rows";
					}

					//---- add row
					if ($sc === TRUE && ! empty($ds->rows))
					{
						foreach ($ds->rows as $rs)
						{
							$arr = array(
								'step_id' => $item->id,
								'name' => $rs->name,
								'ItemCode' => $item->ItemCode,
								'ItemName' => $item->ItemName,
								'Qty' => $rs->Qty,
								'SellPrice' => $rs->SellPrice,
								'freeQty' => $rs->freeQty,
								'position' => $rs->position,
								'UomCode' => $item->UomCode,
								'update_by' => $this->_user->id
							);

							if (! $this->special_price_list_model->add_item_detail($arr))
							{
								$sc = FALSE;
								$this->error = "Failed to update row";
							}

							if ($sc === FALSE)
							{
								break;
							}
						} //-- end foreach
					} //-- end add row

					if($sc === TRUE)
					{
						$arr = array(
							'update_by' => $this->_user->id,
							'date_upd' => now()
						);

						$this->special_price_list_model->update_item($ds->id, $arr);
					}

					if ($sc === TRUE)
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


	public function view_item($id)
	{
		$item = $this->special_price_list_model->get_item_by_id($id);

		if (! empty($item))
		{
			$this->title = $this->title . " - " . $item->price_list_name;
			$data['item'] = $item;
			$data['details'] = $this->special_price_list_model->get_item_details($id);

			$this->load->view('special_price_list/special_price_item_detail', $data);
		}
		else
		{
			$this->error_page();
		}
	}


	public function import_details($price_list_id)
	{
		$sc = TRUE;
		$file = isset($_FILES['uploadFile']) ? $_FILES['uploadFile'] : FALSE;
		$path = $this->config->item('upload_path') . 'special_price_list/';
		$file  = 'uploadFile';
		$config = array(   // initial config for upload class
			"allowed_types" => "xlsx",
			"upload_path" => $path,
			"file_name"  => "import-special-price-list-" . date('YmdHis'),
			"max_size" => 5120,
			"overwrite" => TRUE
		);

		$this->load->library("upload", $config);

		if (! $this->upload->do_upload($file))
		{
			$sc = FALSE;
			$this->error = $this->upload->display_errors();
		}

		if ($sc === TRUE)
		{
			$this->load->library('excel');
			$info = $this->upload->data();
			/// read file
			$excel = PHPExcel_IOFactory::load($info['full_path']);
			//get only the Cell Collection
			$collection  = $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

			if (! empty($collection))
			{
				$firstRow = $collection[1]; //--- get header row for validate file format        

				if (!empty($firstRow))
				{
					$expectedHeader = array(
						'A' => 'Type',
						'B' => 'SKU',
						'C' => 'Description',
						'D' => 'Min Qty',
						'E' => 'Sell Price',
						'F' => 'Free Qty'
					);

					foreach ($expectedHeader as $col => $header)
					{
						if ($firstRow[$col] !== $header)
						{
							$sc = FALSE;
							$this->error = "Column {$col} should be {$header}";
							break;
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "The first row of the file is empty. Please make sure the file format is correct.";
				}

				if ($sc === TRUE)
				{
					array_shift($collection);

					$ds = $this->parseCollection($collection);

					if ($ds === FALSE)
					{
						$sc = FALSE;
					}
					else
					{
						$this->db->trans_begin();

						foreach ($ds as $item)
						{
							$item_id = $this->special_price_list_model->get_item_id($item['ItemCode'], $price_list_id);

							if( ! empty($item_id))
							{
								if( ! $this->special_price_list_model->delete_details($item_id))
								{
									$sc = FALSE;
									$this->error = "Failed to delete item steps";
								}

								if($sc === TRUE)
								{
									if( ! $this->special_price_list_model->delete_item($item_id))
									{
										$sc = FALSE;
										$this->error = "Failed to delete item";
									}
								}
							}

							if($sc === TRUE)
							{
								$arr = array(
									'price_list_id' => $price_list_id,
									'ItemCode' => $item['ItemCode'],
									'ItemName' => $item['ItemName'],
									'UomCode' => $item['UomCode'],
									'isControl' => $item['isControl'],
									'add_by' => $this->_user->id,
									'date_add' => now()
								);

								$id = $this->special_price_list_model->add_item($arr);

								if ($id !== FALSE && ! empty($item['details']))
								{
									$pos = 1;

									foreach ($item['details'] as $step)
									{
										$arr = array(
											'step_id' => $id,
											'name' => $step['name'],
											'ItemCode' => $item['ItemCode'],
											'ItemName' => $item['ItemName'],
											'UomCode' => $item['UomCode'],
											'Qty' => $step['Qty'],
											'SellPrice' => $step['SellPrice'],
											'freeQty' => $step['freeQty'],
											'position' => $pos,
											'update_by' => $this->_user->id
										);

										if (! $this->special_price_list_model->add_item_detail($arr))
										{
											$sc = FALSE;
											$this->error = "Failed to add row details for item : {$item['ItemCode']} - {$item['ItemName']}";
											break;
										}

										$pos++;
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "Failed to add item : {$item['ItemCode']} - {$item['ItemName']}";
								}
							}

							if ($sc === FALSE)
							{
								break;
							}
						} //--- end foreach

						if ($sc === TRUE)
						{
							$this->db->trans_commit();
						}
						else
						{
							$this->db->trans_rollback();
						}
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "The file is empty. Please make sure the file format is correct.";
			} //-- end if count limit
		} //--- end if else

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'error',
			'message' => $sc === TRUE ? 'Import completed' : $this->error
		);

		echo json_encode($arr);
	}


	public function parseCollection(array $collection = array())
	{
		$ds = []; //--- temp array for import data
		$err = 0; //-- count error rows for error message

		if (empty($collection))
		{
			$this->error = "No data to import";
			return FALSE;
		}

		if (! empty($collection))
		{
			$i = 2; //--- start from row 2 because row 1 is header

			foreach ($collection as $rs)
			{
				$sc = TRUE;
				$type = strtoupper(str_replace(array("\n", "\r"), '', trim($rs['A']))); //--- เอาตัวขึ้นบรรทัดใหม่ออก
				$sku = str_replace(array("\n", "\r"), '', trim($rs['B'])); //--- เอาตัวขึ้นบรรทัดใหม่ออก
				$code = $sku;

				if (empty($type) or empty($sku))
				{
					//--- skip empty row
					$sc = FALSE;
					$err++;
					$this->error .= "Row {$i} is empty or missing required fields (Type, SKU). <br>";
					$i++;
					continue;
				}

				$desc = str_replace(array("\n", "\r"), '', trim($rs['C'])); //--- เอาตัวขึ้นบรรทัดใหม่ออก
				$minQty = str_replace(array("\n", "\r"), '', trim($rs['D'])); //--- เอาตัวขึ้นบรรทัดใหม่ออก
				$sellPrice = str_replace(array("\n", "\r"), '', trim($rs['E'])); //--- เอาตัวขึ้นบรรทัดใหม่ออก
				$freeQty = str_replace(array("\n", "\r"), '', trim($rs['F'])); //--- เอาตัวขึ้นบรรทัดใหม่ออก

				if ($type == 'I')
				{
					if (! isset($ds[$code]))
					{
						$item = $this->item_model->get_item($sku);

						if (! empty($item))
						{
							$ds[$code] = array(
								'ItemCode' => $item->code,
								'ItemName' => $item->name,
								'UomCode' => $item->uom,
								'isControl' => $item->U_BEX_Controll == 'Controlled' ? 'Y' : 'N',
								'details' => []
							);
						}
						else
						{
							$sc = FALSE;
							$err++;
							$this->error .= "Row {$i} : Item code {$sku} does not exists. <br>";
						}
					}
				}

				if ($type == 'S')
				{
					if (empty($desc) or empty($minQty) or empty($sellPrice))
					{
						$sc = FALSE;
						$err++;
						$this->error .= "Row {$i} : Missing required fields for step (Description, Min Qty, Sell Price). <br>";
					}
					else
					{
						if (! isset($ds[$code]))
						{
							$sc = FALSE;
							$err++;
							$this->error .= "Row {$i} : Step without item. <br>";
						}
						else
						{
							$e = 0;
							$minQty = floatval($minQty);
							$sellPrice = floatval($sellPrice);
							$freeQty = floatval($freeQty);

							if ($minQty <= 0)
							{
								$sc = FALSE;
								$err++;
								$this->error .= "Row {$i} : Min Qty must be greater than 0. <br>";
								$e++;
							}

							if ($sellPrice <= 0)
							{
								$sc = FALSE;
								$err++;
								$this->error .= "Row {$i} : Sell Price must be greater than 0. <br>";
								$e++;
							}

							if ($e == 0)
							{
								$step = array(
									'name' => $desc,
									'Qty' => $minQty,
									'SellPrice' => $sellPrice,
									'freeQty' => $freeQty
								);

								$ds[$code]['details'][] = $step;
							}
						}
					}
				}

				$i++;
			} //-- end foreach
		} //-- end if empty collection

		return $err == 0 ? $ds : FALSE;
	}


	public function view_detail($id)
	{
		$doc = $this->special_price_list_model->get($id);

		if (! empty($doc))
		{
			$data['doc'] = $doc;
			$data['details'] = $this->special_price_list_model->get_details($id);
			$this->load->view('special_price_list/special_price_view_detail', $data);
		}
		else
		{
			$this->error_page();
		}
	}


	public function delete_item()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if (! empty($id))
		{
			if ($this->pm->can_delete)
			{
				$this->db->trans_begin();

				if (! $this->special_price_list_model->delete_details($id))
				{
					$sc = FALSE;
					$this->error = "Failed to delete item steps";
				}

				if ($sc === TRUE)
				{
					if (! $this->special_price_list_model->delete_item($id))
					{
						$sc = FALSE;
						$this->error = "Failed to delete item";
					}
				}

				if ($sc === TRUE)
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


	public function delete()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if (! empty($id))
		{
			if ($this->pm->can_delete)
			{
				$this->db->trans_begin();

				$items = $this->special_price_list_model->get_details($id);

				if (! empty($items))
				{
					foreach ($items as $rs)
					{
						if ($sc === FALSE)
						{
							break;
						}

						if (! $this->special_price_list_model->delete_details($rs->id))
						{
							$sc = FALSE;
							$this->error = "Failed to delete item steps";
						}

						if ($sc === TRUE)
						{
							if (! $this->special_price_list_model->delete_item($rs->id))
							{
								$sc = FALSE;
								$this->error = "Failed to delete item";
							}
						}
					}
				}

				if ($sc === TRUE)
				{
					if (! $this->special_price_list_model->delete($id))
					{
						$sc = FALSE;
						$this->error = "Failed to delete Price List";
					}
				}

				if ($sc === TRUE)
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


	public function setActive()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$active = $this->input->post('active');

		$arr = array(
			'active' => $active,
			'update_by' => $this->_user->id,
			'date_upd' => now()
		);

		if (! $this->special_price_list_model->update($id, $arr))
		{
			$sc = FALSE;
			set_error('Update failed');
		}

		echo $sc === TRUE ? 'success' : 'failed';
	}


	public function setActiveItem()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$active = $this->input->post('active');

		$arr = array(
			'active' => $active,
			'update_by' => $this->_user->id,
			'date_upd' => now()
		);

		if (! $this->special_price_list_model->update_item($id, $arr))
		{
			$sc = FALSE;
			set_error('Update failed');
		}

		echo $sc === TRUE ? 'success' : 'failed';
	}

	public function get_import_template()
	{
		$this->load->helper('download');
		$file = 'templates/special_price_list_item_import_template.xlsx';

		if (file_exists($file))
		{
			force_download($file, NULL);
		}
		else
		{
			$this->page_error();
		}
	}


	public function clear_filter()
	{
		$filter = array(
			'sp_name',
			'sp_status',
			'sp_type',
			'sp_start_date',
			'sp_end_date'
		);

		return clear_filter($filter);
	}
} //--- end class
