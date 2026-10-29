<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends PS_Controller
{
  public $menu_code = 'CUSTOMER';
  public $menu_group_code = 'ADMIN';
  public $title = 'Customers';
  public $segment = 3;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'customer';
    $this->load->model('customer_model');
    $this->load->helper('customer');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'customer_code', ''),
      'name' => get_filter('name', 'customer_name', ''),
      'cust_code' => get_filter('cust_code', 'customer_cust_code', ''),
      'department' => get_filter('department', 'customer_department', 'all'),
      'area' => get_filter('area', 'customer_area', 'all'),
      'sales_team' => get_filter('sales_team', 'customer_sales_team', 'all'),
      'sales_person' => get_filter('sales_person', 'customer_sales_person', 'all'),
      'active' => get_filter('active', 'customer_active', 'all'),
      'isRegular' => get_filter('isRegular', 'customer_isRegular', 'all'),
      'order_by' => get_filter('order_by', 'customer_order_by', 'CardCode'),
      'sort_by' => get_filter('sort_by', 'customer_sort_by', 'ASC')
    );

    if ($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();      
      $rows = $this->customer_model->count_rows($filter);
      $filter['data'] = $this->customer_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init  = pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('customer/customer_list', $filter);
    }
  }


  public function edit($id)
  {
    $rs = $this->customer_model->get_by_id($id);

    if (!empty($rs))
    {
      $data['ds'] = $rs;
      $this->load->view('customer/customer_edit', $data);
    }
    else
    {
      $this->page_error();
    }
  }


  public function view_detail($id)
  {
    $rs = $this->customer_model->get_by_id($id);

    if(!empty($rs))
    {
      $data['ds'] = $rs;
      $this->load->view('customer/customer_detail', $data);
    }
    else
    {
      $this->page_error();
    }
  }

  public function syncData()
  {
    $sc = TRUE;
    $all = $this->input->post('all') == 1 ? TRUE : FALSE;
    $last_sync = $all ? date('Y-m-d H:i:s', strtotime('2000-01-01 00:00:00')) : $this->customer_model->get_last_sync_date();
    $list = $this->customer_model->get_sap_data($last_sync);

    if (! empty($list))
    {
      foreach ($list as $rs)
      {
        $custType = substr($rs->CardCode, 3, 1);
        $isRegular = $rs->U_TPD_FirstCus === 'Y' ? 0 : 1;

        $arr = array(
          'id' => $rs->DocEntry,
          'CardCode' => $rs->CardCode,
          'CardName' => $rs->CardName,
          'CustCode' => $rs->U_TPD_CUST_HCode,
          'GroupCode' => $rs->GroupCode,
          'GroupNum' => $rs->GroupNum,
          'ListNum' => $rs->ListNum,
          'SlpCode' => $rs->SlpCode,
          'ECVatGroup' => $rs->ECVatGroup,
          'CreditLine' => $rs->CreditLine,
          'validFor' => $rs->validFor,
          'Currency' => $rs->Currency,
          'CreateDate' => $rs->CreateDate,
          'U_TPD_DrugCon' => $rs->U_TPD_DrugCon,
          'U_TPD_RA_DrugType' => $rs->U_TPD_RA_DrugType,
          'U_TPD_BI_SalesTeam' => $rs->U_TPD_BI_SalesTeam,
          'U_TPD_BI_AreaName' => $rs->U_TPD_BI_AreaName,
          'U_SALE_PERSON' => $rs->U_SALE_PERSON,
          'U_TPD_BI_Department' => $rs->U_TPD_BI_Department,
          'vatRate' => empty($rs->Rate) ? 0.00 : $rs->Rate,
          'custType' => $custType, //-- Q, V
          'isRegular' => $isRegular,
          'registerDate' => $rs->registerDate
        );

        if (! $this->customer_model->is_exists_id($rs->DocEntry))
        {
          $this->customer_model->add($arr);
        }
        else
        {
          $this->customer_model->update($rs->DocEntry, $arr);
        }        
      }
    }

    $this->customer_model->update_last_sync_date();

    $this->_response($sc);
  }
  

  public function clear_filter()
  {
    $filter = array(
      'customer_code',
      'customer_name',
      'customer_active',
      'customer_isRegular',
      'customer_cust_code',
      'customer_department',
      'customer_area',
      'customer_sales_team',
      'customer_sales_person',
      'customer_order_by',
      'customer_sort_by'
    );

    return clear_filter($filter);
  }
} //--- end class
