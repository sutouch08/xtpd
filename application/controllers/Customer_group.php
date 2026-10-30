<?php
defined('BASEPATH') or exit('No direct script access allowed');
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class Customer_group extends PS_Controller
{
  public $menu_code = 'CUSTOMERGROUP';
  public $menu_group_code = 'ADMIN';
  public $title = 'Customer Groups';
  public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'customer_group';
    $this->load->model('customer_group_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'customer_group_code', ''),
      'name' => get_filter('name', 'customer_group_name', ''),
      'active' => get_filter('active', 'customer_group_active', 'all')
    );

    if ($this->input->post('search'))
    {
      redirect($this->home);
      exit;
    }

    $perpage = get_rows();
    $rows = $this->customer_group_model->count_rows($filter);
    $filter['data'] = $this->customer_group_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
    $init  = pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
    $this->pagination->initialize($init);
    $this->load->view('customer_group/customer_group_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('customer_group/customer_group_add');
  }


  public function add()
  {
    $sc = TRUE;

    if ($this->input->post('code') && $this->input->post('name'))
    {
      $code = trim($this->input->post('code') ?? '');
      $name = trim($this->input->post('name') ?? '');
      $status = $this->input->post('status') ? 1 : 0;

      if ($this->customer_group_model->is_exists($code))
      {
        $sc = FALSE;
        set_error('Code already exists');
      }

      if ($sc === TRUE)
      {
        $arr = array(
          'code' => $code,
          'name' => $name,
          'active' => $status,
          'date_upd' => now(),
          'update_by' => $this->_user->id
        );

        if (! $this->customer_group_model->add($arr))
        {
          $sc = FALSE;
          set_error('Insert data failed');
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('Missing required fields');
    }

    $this->_response($sc);
  }


  public function edit($id, $tab = 'info')
  {
    $group = $this->customer_group_model->get($id);

    if (!empty($group))
    {
      $this->load->model('customer_model');
      $details = $this->customer_group_model->get_details($id);
      $list = $this->customer_model->get_all_in_array();

      if (!empty($details))
      {
        foreach ($details as $rs)
        {
          if (isset($list[$rs->CardId]))
          {
            unset($list[$rs->CardId]);
          }
        }
      }

      $data = array(
        'tab' => $tab,
        'data' => $group,
        'details' => $details,
        'list' => $list
      );

      $this->load->view('customer_group/customer_group_edit', $data);
    }
    else
    {
      $this->page_error();
    }
  }


  public function update()
  {
    $sc = TRUE;

    if ($this->input->post('id') && $this->input->post('code') && $this->input->post('name'))
    {
      $id = $this->input->post('id');
      $code = trim($this->input->post('code') ?? '');
      $name = trim($this->input->post('name') ?? '');
      $status = $this->input->post('status') ? 1 : 0;

      if ($this->customer_group_model->is_exists($code, $id))
      {
        $sc = FALSE;
        set_error('Code already exists');
      }

      if ($sc === TRUE)
      {
        $arr = array(
          'code' => $code,
          'name' => $name,
          'active' => $status,
          'date_upd' => now(),
          'update_by' => $this->_user->id
        );

        if (! $this->customer_group_model->update($id, $arr))
        {
          $sc = FALSE;
          set_error('Update data failed');
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('Missing required fields');
    }

    $this->_response($sc);
  }


  public function update_group_details()
  {
    $sc = TRUE;

    $group_id = $this->input->post('id');
    $lists = json_decode($this->input->post('lists') ?? '');

    if (! empty($group_id))
    {
      $group = $this->customer_group_model->get($group_id);

      if (!empty($group))
      {
        $groupList = [];

        if (! empty($lists))
        {
          foreach ($lists as $rs)
          {
            $groupList[] = array(
              'group_id' => $group_id,
              'CardId' => $rs->id,
              'CardCode' => $rs->code
            );
          }
        }

        $this->db->trans_begin();

        if (! $this->customer_group_model->delete_details($group_id))
        {
          $sc = FALSE;
          $this->error = "Failed to delete existing group details";
        }

        if ($sc === TRUE && ! empty($groupList))
        {
          if (! $this->customer_group_model->add_details($groupList))
          {
            $sc = FALSE;
            $this->error = "Failed to add new group details";
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
        set_error('Group not found');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function view_detail($id, $tab = 'info')
  {
    $rs = $this->customer_group_model->get($id);

    if (!empty($rs))
    {
      $ds = array(
        'tab' => $tab,
        'data' => $rs,
        'details' => $this->customer_group_model->get_details($id)
      );

      $this->load->view('customer_group/customer_group_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }

  public function import_details($group_id)
  {
    $sc = TRUE;
    $file = isset($_FILES['uploadFile']) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path') . 'customer_group/';
    $file = 'uploadFile';
    $config = [
      'allowed_types' => 'xlsx',
      'upload_path' => $path,
      'file_name'  => 'import-customer-group-' . date('YmdHis'),
      'max_size' => 5120,
      'overwrite' => TRUE
    ];

    $this->load->library("upload", $config);

    if (! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }

    if($sc === TRUE)
    {
      $this->load->model('customer_model');
      $info = $this->upload->data();
      $reader = new Reader();
      $reader->open($info['full_path']);
      $sheet = $reader->getSheetIterator()->current();
      $isFirstRow = TRUE;
      $j = 0;
      $k = 0;
      $limit = 1000; //-- limit insert batch 1000 rows per time
      $groupList = []; //--- array for insert batch

      foreach ($sheet->getRowIterator() as $row)
      {
        if ($isFirstRow)
        {
          $isFirstRow = FALSE;
          continue;
        }

        $cells = $row->getCells();
        $CardCode = str_replace(array("\n", "\r"), '', trim($cells[0]->getValue())); //--- เอาตัวขึ้นบรรทัดใหม่ออก

        if (! empty($CardCode))
        {
          $CardId = $this->customer_model->get_id($CardCode);

          if (! empty($CardId))
          {
            $arr = array(
              'group_id' => $group_id,
              'CardId' => $CardId,
              'CardCode' => $CardCode
            );

            $groupList[$j][$k] = $arr;
            $k++;

            if ($k === $limit)
            {
              $k = 0;
              $j++;
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Customer : {$CardCode} does not exists";
            break;
          }
        }

        if($sc === FALSE) break;
      }

      if ($sc === TRUE && ! empty($groupList))
      {
        $this->db->trans_begin();

        if( ! $this->customer_group_model->delete_details($group_id))
        {
          $sc = FALSE;
          $this->error = "Failed to delete existing group details";
        }

        if($sc === TRUE)
        {
          foreach($groupList as $list)
          {
            if( ! $this->customer_group_model->add_details($list))
            {
              $sc = FALSE;
              $this->error = "Failed to add group details";
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'Import completed' : $this->error
    );
    
    echo json_encode($arr);
  }  

  public function export_details()
  {
    $id = $this->input->post('id');
    $token = $this->input->post('token');

    if(empty($id) || empty($token))
    {
      $this->page_error();
      return;
    }

    $group = $this->customer_group_model->get($id);

    if(empty($group))
    {
      $this->page_error();
      return;
    }

    $groupDetails = $this->customer_group_model->get_details($id);

    if(empty($groupDetails))
    {
      $this->page_error();
      return;
    }

    $tempFile = tempnam(sys_get_temp_dir(), 'customer_group_');
    $fileName = "memberOfGroup-{$group->code}.xlsx";
    $title = "Member of Group: {$group->code} : {$group->name}";
    $header = ['Code', 'Name'];
    
    try 
    {
      $writer = new Writer();
      $writer->openToFile($tempFile);
      $writer->addRow(Row::fromValues([$title]));
      $writer->addRow(Row::fromValues(['  ']));
      $writer->addRow(Row::fromValues($header));
      $sheet = $writer->getCurrentSheet();
      $sheet->setColumnWidth(15, 1); //-- A
      $sheet->setColumnWidth(60, 2); //-- B

      if(!empty($groupDetails))
      {
        foreach($groupDetails as $detail)
        {
          $row = [$detail->CardCode, $detail->CardName];
          $writer->addRow(Row::fromValues($row));
        }
      }

      $writer->close();
      setToken($token);
      while(ob_get_level() > 0) { ob_end_clean(); }
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment; filename="'.$fileName.'"');
      readfile($tempFile);      
    }
    finally
    {
      if(file_exists($tempFile))
      {
        unlink($tempFile);
      }
    }

    exit;
  }

  public function get_import_template()
  {
    $this->load->helper('download');
    $file = 'templates/customer_group_import_template.xlsx';

    if (file_exists($file))
    {
      force_download($file, NULL);
    }
    else
    {
      $this->page_error();
    }
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

    if (! $this->customer_group_model->update($id, $arr))
    {
      $sc = FALSE;
      set_error('Update failed');
    }

    echo $sc === TRUE ? 'success' : 'failed';
  }

  public function clear_filter()
  {
    $filter = array(
      'customer_group_code',
      'customer_group_name',
      'customer_group_active'
    );

    return clear_filter($filter);
  }
} //--- end class
