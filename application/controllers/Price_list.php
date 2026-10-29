<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Price_list extends PS_Controller
{
	public $menu_code = 'PRICELIST';
	public $menu_group_code = 'ADMIN';
	public $title = 'Price List';
	public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'price_list';
		$this->load->model('price_list_model');
  }


	public function index()
	{
		$filter = array(
			'name' => get_filter('name', 'price_list_name', ''),
			'active' => get_filter('active', 'price_list_active', '1')
		);

		if($this->input->post('search'))
		{
			redirect($this->home);			
		}
		else 
		{
			$perpage = get_rows();

			$segment = $this->segment; //-- url segment
			$rows = $this->price_list_model->count_rows($filter);			
			$filter['data'] = $this->price_list_model->get_list($filter, $perpage, $this->uri->segment($segment));
			$init	= pagination_config($this->home . '/index/', $rows, $perpage, $segment);
			$this->pagination->initialize($init);
			$this->load->view('price_list/price_list_list', $filter);
		}			
	}


	public function setActive()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$active = $this->input->post('active');

		$arr = array(
			'active' => $active,
			'update_by' => $this->_user->id
		);

		if( ! $this->price_list_model->update($id, $arr))
		{			
			$sc = FALSE;
			set_error('Update failed');
		}

		echo $sc === TRUE ? 'success' : 'failed';
	}


	public function updatePosition()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$position = $this->input->post('position');

		$arr = array(
			'position' => $position,
			'update_by' => $this->_user->id
		);

		if( ! $this->price_list_model->update($id, $arr))
		{
			$sc = FALSE;
			set_error('Update failed');
		}

		echo $sc === TRUE ? 'success' : 'failed';
	}

	public function syncData()
	{
		$sc = TRUE;

		$list = $this->price_list_model->get_sap_data();

		if( ! empty($list))
		{
			foreach($list as $rs)
			{
				$arr = array(
					'id' => $rs->id,
					'name' => $rs->name,					
					'date_upd' => now(),
					'update_by' => $this->_user->id
				);

				if( ! $this->price_list_model->is_exists_id($rs->id))
				{
					$this->price_list_model->add($arr);
				}
				else
				{
					$this->price_list_model->update($rs->id, $arr);
				}
			}
		}

		$this->_response($sc);
	}


  public function clear_filter()
	{
		$filter = array(
			'price_list_name',
			'price_list_active'
		);

		return clear_filter($filter);
	}

}//--- end class


 ?>
