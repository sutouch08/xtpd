<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Price_list_group extends PS_Controller
{
  public $menu_code = 'PRICELISTGROUP';
  public $menu_group_code = 'ADMIN';
  public $title = 'Price List Group';
  public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'price_list_group';
    $this->load->model('price_list_group_model');
    $this->load->model('price_list_model');
  }


  public function index()
  {
    $filter = array(
      'name' => get_filter('name', 'price_list_group_name', ''),
      'active' => get_filter('active', 'price_list_group_active', 'all')
    );

    if ($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();

      $segment = $this->segment; //-- url segment
      $rows = $this->price_list_group_model->count_rows($filter);
      $filter['data'] = $this->price_list_group_model->get_list($filter, $perpage, $this->uri->segment($segment));
      $init  = pagination_config($this->home . '/index/', $rows, $perpage, $segment);
      $this->pagination->initialize($init);
      $this->load->view('price_list_group/price_list_group_list', $filter);
    }
  }


  public function add_new()
  {
    $ds = array(
      'lists' => $this->price_list_model->get_all(TRUE)
    );

    $this->load->view('price_list_group/price_list_group_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if (!empty($ds) && ! empty($ds->name))
    {
      if ($this->price_list_group_model->is_exists_name($ds->name))
      {
        $sc = FALSE;
        set_error('Group name already exists');
      }

      if ($sc === TRUE)
      {
        $arr = array(
          'name' => $ds->name,
          'active' => $ds->active,
          'update_by' => $this->_user->id
        );

        $group_id = $this->price_list_group_model->add($arr);

        if ($group_id)
        {
          if (!empty($ds->lists))
          {
            $batch = array();

            foreach ($ds->lists as $id)
            {
              $batch[] = array(
                'group_id' => $group_id,
                'price_list_id' => $id
              );
            }

            if (! empty($batch))
            {
              if (! $this->price_list_group_model->add_details($batch))
              {
                $sc = FALSE;
                set_error('Insert price list failed');
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('Insert group failed');
        }
      }
    }
    else 
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function edit($id)
  {
    $group = $this->price_list_group_model->get($id);

    if (!empty($group))
    {
      $details = $this->price_list_group_model->get_details($id);
      $lists = [];

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $lists[] = $rs->price_list_id;
        }
      }
        
      $ds = array(
        'group' => $group,
        'lists' => $this->price_list_model->get_all(TRUE),
        'selected_lists' => $lists
      );

      $this->load->view('price_list_group/price_list_group_edit', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if (! empty($ds) && ! empty($ds->id) && ! empty($ds->name))
    {
      if ($this->price_list_group_model->is_exists_name($ds->name, $ds->id))
      {
        $sc = FALSE;
        set_error('Group name already exists');
      }

      if ($sc === TRUE)
      {
        $arr = array(
          'name' => $ds->name,
          'active' => $ds->active,
          'update_by' => $this->_user->id
        );

        if (! $this->price_list_group_model->update($ds->id, $arr))
        {
          $sc = FALSE;
          set_error('update');
        }

        if($sc === TRUE)
        {
          $this->db->trans_begin();

          //--- delete all details
          $this->price_list_group_model->delete_details($ds->id);

          //--- insert new details
          if ( ! empty($ds->lists))
          {
            $batch = array();

            foreach ($ds->lists as $id)
            {
              $batch[] = array(
                'group_id' => $ds->id,
                'price_list_id' => $id
              );
            }

            if (! empty($batch))
            {
              if (! $this->price_list_group_model->add_details($batch))
              {
                $sc = FALSE;
                set_error('Insert price list failed');
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
    }
    else 
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function view_detail($id)
  {
    $group = $this->price_list_group_model->get($id);

    if (!empty($group))
    {
      $details = $this->price_list_group_model->get_details($id);
      $lists = [];

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $lists[] = $rs->price_list_id;
        }
      }
        
      $ds = array(
        'group' => $group,
        'lists' => $this->price_list_model->get_all(TRUE),
        'selected_lists' => $lists
      );

      $this->load->view('price_list_group/price_list_group_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');

      if($id)
      {
        if ($this->price_list_group_model->is_exists($id))
        {
          $this->db->trans_begin();

          if (! $this->price_list_group_model->delete_details($id))
          {
            $sc = FALSE;
            set_error('Delete details failed');
          }
        
          if($sc === TRUE)
          {
            if (! $this->price_list_group_model->delete($id))
            {
              $sc = FALSE;
              set_error('Delete failed');
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
          set_error('Group not found');
        }
      }
      else 
      {
        $sc = FALSE;
        set_error('No group id found');
      }      
    }
    else 
    {
      $sc = FALSE;
      set_error('You do not have permission to delete');
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
      'update_by' => $this->_user->id
    );

    if (! $this->price_list_group_model->update($id, $arr))
    {
      $sc = FALSE;
      set_error('Update failed');
    }

    echo $sc === TRUE ? 'success' : 'failed';
  }
  

  public function clear_filter()
  {
    $filter = array(
      'price_list_group_name',
      'price_list_group_active'
    );

    return clear_filter($filter);
  }
} //--- end class
