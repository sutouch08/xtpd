<?php
class Po_search extends PS_Controller
{
  public $menu_code = 'POSEARCH';
  public $menu_group_code = 'ORDER';
  public $menu_sub_group_code = '';
  public $title = 'ค้นหาใบสั่งซื้อ';
  public $segment = 3;
  
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'po_search';
    $this->load->model('po_search_model');    
  }

  public function index()
  {    
    $filter = array(                
      'so_code' => get_filter('so_code', 'po_so_code', ''),
      'inv_code' => get_filter('inv_code', 'po_inv_code', ''),
      'po' => get_filter('po', 'po_po', ''),
      'customer' => get_filter('customer', 'po_customer', ''),      
      'fromDate' => get_filter('fromDate', 'po_fromDate', ''),
      'toDate' => get_filter('toDate', 'po_toDate', ''),
      'has_file' => get_filter('has_file', 'po_has_file', 'all')
    );

    if ($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();      
      $rows = $this->po_search_model->count_rows($filter);
      $filter['data'] = $this->po_search_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('po_search/po_search_list', $filter);
    }
  }


  public function open_file($filename)
  {
    $path = $this->config->item('user_files_path') . 'order_po/' . $filename;

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

  public function download_file($filename)
  {
    // ป้องกันการโจมตีด้วย ../
    $filename = basename($filename);

    // โฟลเดอร์เก็บไฟล์
    $path = $this->config->item('user_files_path') . 'order_po/' . $filename;

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


  public function print_file($filename)
  {        
    $path = $this->config->item('user_files_path') . 'order_po/' . $filename;

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


  public function clear_filter()
  {
    $filter = array('po_web_code', 'po_inv_code', 'po_po', 'po_customer', 'po_fromDate', 'po_toDate', 'po_has_file');
    clear_filter($filter);
  }
} // end class 
