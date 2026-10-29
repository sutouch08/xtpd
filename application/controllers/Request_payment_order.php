<?php
class Request_payment_order extends PS_Controller
{
  public $menu_code = 'ORDERCASE';
  public $menu_group_code = 'ORDER';
  public $title = 'Request Payment Order';
  public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'request_payment_order';
    $this->load->model('payment_request_model');
    $this->load->model('orders_model');
    $this->load->helper('credit_approval');
  }


  public function index()
  {
    $this->title = "Request Payment Order - List";

    $filter = array(
      'code' => get_filter('code', 'rpo_code', ''),
      'customer' => get_filter('customer', 'rpo_customer', ''),
      'request_by' => get_filter('request_by', 'rpo_request_by', 'all'),
      'from_date' => get_filter('from_date', 'rpo_from_date', ''),
      'to_date' => get_filter('to_date', 'rpo_to_date', ''),      
      'status' => get_filter('status', 'rpo_status', 'O'),
      'reply_status' => get_filter('reply_status', 'rpo_reply_status', 'N')
    );

    $perpage = get_rows();
    $rows = $this->payment_request_model->count_rows($filter);
    $filter['data'] = $this->payment_request_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
    $init  = pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
    $this->pagination->initialize($init);
    $this->load->view('request_payment_order/request_payment_order_list', $filter);
  }


  public function view_detail($code)
  {
    $req = $this->payment_request_model->get($code);

    if (! empty($req))
    {
      $req->overdue_total = $this->orders_model->get_overdue_amount($req->CardCode, $req->CustCode);
      $req->request_by = emp_name_by_id($req->add_by);

      $files = $req->has_document == 1 ? $this->get_file_list($code) : NULL;
      $req->files = $files;

      $ds = array(
        'data' => $req
      );

      $this->load->view('request_payment_order/request_payment_order_detail', $ds);
    }
    else
    {
      set_error("Request not found");
      $this->page_error();
    }
  }

  public function get_detail()
  {
    $code = $this->input->get('code');
    $rs = $this->payment_request_model->get($code);
    $ds = [];

    if (!empty($rs))
    {
      $ds['code'] = $rs->code;
      $ds['customer'] = $rs->CardCode . ' | ' . $rs->CardName;
      $ds['doc_total'] = number($rs->DocTotal, 2);
      $ds['diff'] = number($rs->credit_diff, 2);
      $ds['overdue'] = number($this->orders_model->get_overdue_amount($rs->CardCode, $rs->CustCode), 2);
      $ds['request_by'] = emp_name_by_id($rs->add_by);
      $ds['date'] = thai_date($rs->date_add, TRUE);
      $ds['message'] = $rs->message;
      $ds['reply_message'] = $rs->reply_message;
      
      $files = $rs->has_document == 1 ? $this->get_file_list($code) : NULL;

      if(!empty($files))
      {
        $no = 1;
        foreach($files as $f)
        {
          $f->code = $code;
          $f->no = $no;
          $no++;
        }
      }
      else 
      {
        $files = array(['nodata' => 'nodata']);
      }

      $ds['files'] = $files;      
    }
    
    $arr = array(
      'status' => empty($rs) ? 'error' : 'success',
      'message' => empty($rs) ? 'Request not found' : NULL,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  private function get_file_list($code)
  {
    $list = array();    
    $file_path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/';

    if (is_dir($file_path))
    {
      if ($handle = opendir($file_path))
      {
        while (FALSE !== ($entry = readdir($handle)))
        {
          if ($entry !== '.' && $entry !== '..')
          {
            $f = $file_path . $entry;

            if (is_file($f))
            {
              $file = (object) array(
                'name' => $entry,
                'size' => number(ceil((filesize($f) / 1024))) . " KB",
                'date_modify' => date('Y-m-d H:i:s', filemtime($f))
              );

              $list[] = $file;
            }
          }
        }

        closedir($handle);
      }      
    }

    return $list;
  }


  public function get_file_list_json($code)
  {
    $files = $this->get_file_list($code);

    if (!empty($files))
    {
      $no = 1;
      foreach ($files as $f)
      {
        $f->code = $code;
        $f->no = $no;
        $no++;
      }
    }
    else
    {
      $files = array(['nodata' => 'nodata']);
    }

    echo json_encode($files);
  }

  public function upload_file($code)
  {
    $sc = TRUE;    

    $files = $_FILES['uploadFile'];

    if (!empty($files))
    {
      $this->load->library('upload');
      $path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/';

      if (!is_dir($path))
      {
        mkdir($path, 0777, true);
      }

      $config = array(
        'upload_path' => $path,
        'allowed_types' => 'jpg|jpeg|png|pdf',
        'max_size' => 5120,
        'overwrite' => TRUE
      );

      $this->upload->initialize($config);

      for($i = 0; $i < count($files['name']); $i++)
      {
        $_FILES['uploadFile']['name']     = $files['name'][$i];
        $_FILES['uploadFile']['type']     = $files['type'][$i];
        $_FILES['uploadFile']['tmp_name'] = $files['tmp_name'][$i];
        $_FILES['uploadFile']['error']    = $files['error'][$i];
        $_FILES['uploadFile']['size']     = $files['size'][$i];

        if (!$this->upload->do_upload('uploadFile'))
        {
          $sc = FALSE;
          $this->error = $this->upload->display_errors();
          break;
        }        
      }

      if ($sc === TRUE)
      {
        $this->payment_request_model->update($code, array('has_document' => 1));
      }      
    }
    else
    {
      $sc = FALSE;
      $this->error = "No file selected";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'Upload file successfully' : $this->error
    );

    echo json_encode($arr);
  }


  public function open_file($code, $filename)
  {
    $path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/' . $filename;

    if (!file_exists($path))
    {
      show_404();
      return;
    }

    // Detect MIME type automatically
    $mime = mime_content_type($path);

    // Force browser to display file inline
    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($path));

    readfile($path);
  }


  public function download_file($code, $filename)
  {
    // ป้องกันการโจมตีด้วย ../
    $filename = basename($filename);

    // โฟลเดอร์เก็บไฟล์
    $path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/' . $filename;

    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'xlsx', 'xls', 'zip', 'txt'];

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed))
    {
      show_error("File type not allowed");
      return;
    }

    // เช็คว่ามีไฟล์จริงไหม
    if (!file_exists($path))
    {
      show_404();
      return;
    }  

    // โหลด helper ของ CI
    $this->load->helper('download');

    // อ่านไฟล์
    $data = file_get_contents($path);

    // สั่งดาวน์โหลด
    force_download($filename, $data);
  }


  public function delete_file()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $filename = $this->input->post('fileName');
    
    if( ! empty($code) && ! empty($filename))
    {
      // ป้องกันการโจมตีด้วย ../
      $filename = basename($filename);

      // โฟลเดอร์เก็บไฟล์
      $path = $this->config->item('user_files_path') . 'request_payment/' . $code . '/' . $filename;

      $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'xlsx', 'xls', 'zip'];

      $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

      if (!in_array($ext, $allowed))
      {
        $sc = FALSE;
        $this->error = "File type not allowed";
      }       

      // ถ้าไฟล์ไม่มีอยู่จริง
      if ($sc === TRUE && !file_exists($path))
      {
        $sc = FALSE;
        $this->error = "File not found";       
      }

      if($sc === TRUE && ! unlink($path))
      {
        $sc = FALSE;
        $this->error = "Cannot delete file";
      }

      if($sc === TRUE)
      {
        $files = $this->get_file_list($code);

        if(empty($files))
        {
          $this->payment_request_model->update($code, array('has_document' => 0));
        }
      }  
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid request";     
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'error',
      'message' => $sc === TRUE ? 'Delete file successfully' : $this->error
    );

    echo json_encode($arr);
  }

  public function submit_reply()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $message = get_null($this->input->post('message'));

    if( ! empty($code))
    {
      $files = $this->get_file_list($code);

      $arr = array(
        'has_document' => empty($files) ? 0 : 1,
        'reply_status' => 'R',
        'reply_message' => $message,
        'reply_date' => now(),
        'update_by' => $this->_user->id
      );

      if( ! $this->payment_request_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "update reply failed";
      }
      else 
      {
        $arr = array(
          'reply_date' => now()
        );

        $this->orders_model->update($code, $arr);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid request";
    }

    $this->_response($sc);
  }

  public function get_approver()
  {
    $this->load->model('credit_approver_model');
    $amount = $this->input->post('diff');
    $apv = $this->credit_approver_model->get_active_by_amount($amount);
    $list = [];

    if (!empty($apv))
    {
      foreach ($apv as $rs)
      {
        $list[] = array(
          'id' => $rs->user_id,
          'uname' => $rs->uname,
          'emp_name' => $rs->emp_name,
          'amount' => number($rs->amount, 2)
        );
      }
    }
    else
    {
      $list[] = ['nodata' => 'No authorizer'];
    }


    echo json_encode($list);
  }

  public function clear_filter()
  {
    return clear_filter(array('rpo_code', 'rpo_customer', 'rpo_request_by', 'rpo_from_date', 'rpo_to_date', 'rpo_status', 'rpo_reply_status'));
  }
} // endclass 
