<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_data extends CI_Controller
{
  public $title = 'Sync Logs';
	public $menu_code = 'SYNC_LOGS';
	public $menu_group_code = 'ADMIN';
  public $pm;
  public $home;
  public $ms;
  public $mc;
  public $_user;
  public $_SuperAdmin = FALSE;
  public $isGM = FALSE;
  public $isAdmin = FALSE;
  public $uid;
  public $limit = 100;
  public $date;

  public function __construct()
  {
    parent::__construct();
    //--- get permission for user

    $this->uid = get_cookie('uid');
    $this->_user = $this->user_model->get_user_by_uid($this->uid);
    $this->_SuperAdmin = $this->_user->ugroup_id == -987654321 ? TRUE : FALSE;
    $this->isGM = $this->_user->role == 'GM' ? TRUE : FALSE;
    $this->isAdmin = $this->_user->role == 'salesAdmin' ? TRUE : FALSE;

    $this->pm = get_permission($this->menu_code);

    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->load->model('sync_data_model');
    $this->load->model('orders_model');
    $this->home = base_url().'sync_data';
    $this->date = date('Y-d-m H:i:s');
  }


  public function index()
  {

    $filter = array(
      'code' => get_filter('code', 'sync_code', ''),
      'docType' => get_filter('docType', 'docType', 'all'),
      'docNum' => get_filter('docNum', 'docNum', ''),
      'status' => get_filter('status', 'sync_status', 'all'),
      'from_date' => get_filter('from_date', 'sync_from_date', ''),
      'to_date' => get_filter('to_date', 'sync_to_date', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 3; //-- url segment
		$rows = $this->sync_data_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->sync_data_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('sync_data_view', $filter);
  }


  public function clear_logs($days = NULL)
  {
    $days = empty($days) ? getConfig('KEEP_SYNC_LOGS') : $days;

    if($this->sync_data_model->clear_logs($days))
    {
      echo 'success';
    }
    else
    {
      echo "Clear Sync Logs Failed";
    }

    $logs = array(
      'code' => 'Clear logs',
      'sync_code' => 'Logs',
      'get_code' => NULL,
      'status' => 1
    );

    $this->sync_data_model->add_logs($logs);
  }


  public function clear_all_logs()
  {
    if($this->sync_data_model->clear_all_logs())
    {
      echo "success";
    }
    else
    {
      echo "Clear Sync Logs Failed";
    }
  }



  public function clear_sync_logs()
  {
    $days = getConfig('KEEP_SYNC_LOGS');

    $days = empty($days) ? 7 : $days;

    $this->sync_data_model->clear_logs($days);

    $logs = array(
      'code' => 'Clear logs',
      'sync_code' => 'Logs',
      'get_code' => NULL,
      'status' => 1
    );

    $this->sync_data_model->add_logs($logs);
  }



  public function syncData()
  {
    $docType = trim($this->input->post('docType'));

    switch ($docType) {
      case 'DO':
        $this->syncDoCode();
        break;
      case 'SO' :
        $this->syncSOCode();
        break;
      case 'INV' :
        $this->syncInvCode();
        break;
      default:
        $this->syncSOCode();
        break;
    }

    echo "success";
  }



  public function syncSOCode()
  {
    $ds = $this->orders_model->get_non_so_code($this->limit);

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $temp = $this->orders_model->get_temp_status($rs->code);

        if(!empty($temp))
        {
          if($temp->F_Sap === 'Y')
          {
            $sap = $this->orders_model->get_sap_order($rs->code);

            if(!empty($sap))
            {
              $arr = array(
                'DocNum' => $sap->DocNum,
                'sap_date' => $temp->F_SapDate,
                'Status' => 2, //--- เข้า SAP แล้ว
                'SO_Status' => 'O',
                'Message' => NULL
              );

              $this->orders_model->update($rs->code, $arr);
            }

            $logs = array(
              'code' => $rs->code,
              'sync_code' => 'SO',
              'get_code' => empty($sap) ? NULL : $sap->DocNum,
              'status' => 1
            );

            $this->sync_data_model->add_logs($logs);

          }
          else
          {
            if($temp->F_Sap === 'N')
            {
              $arr = array(
                'Status' => 3,
                'Message' => $temp->Message
              );

              $this->orders_model->update($rs->code, $arr);

              $logs = array(
                'code' => $rs->code,
                'sync_code' => 'SO',
                'get_code' => NULL,
                'status' => 3,
                'message' => $temp->Message
              );

              $this->sync_data_model->add_logs($logs);

            }
            else
            {
              $logs = array(
                'code' => $rs->code,
                'sync_code' => 'SO',
                'get_code' => NULL,
                'status' => 1,
                'message' => 'Document not found'
              );

              $this->sync_data_model->add_logs($logs);
            }
          }
        }
        else
        {
          $logs = array(
            'code' => $rs->code,
            'sync_code' => 'SO',
            'get_code' => NULL,
            'status' => 3,
            'message' => 'Order not in temp'
          );

          $this->sync_data_model->add_logs($logs);
        }
      }
    }
    else
    {
      $arr = array(
        'code' => 'Sync',
        'sync_code' => 'SO',
        'get_code' => NULL,
        'status' => 0,
        'message' => 'No Document to Sync'
      );

      $this->sync_data_model->add_logs($arr);
    }

  }



  public function syncDoCode()
  {
    $ds = $this->orders_model->get_non_do_code($this->limit);

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {

        $do = $this->orders_model->get_delivery_order($rs->code);

        if(!empty($do))
        {
          $doNo = "";
          $i = 1;
          $ra = array();

          foreach($do as $ds)
          {
            if(!isset($ra[$ds->DocNum]))
            {
              $ra[$ds->DocNum] = $ds->DocNum;
              $doNo .= $i === 1 ? $ds->DocNum : "<br/> ".$ds->DocNum;
              $i++;
            }
          }

          $arr = array(
            'DeliveryNo' => limitText($doNo, 97),
            'last_do_sync' => now()
          );

          $so = $this->orders_model->get_sap_order($rs->code);

          if(!empty($so))
          {
            if($so->DocStatus === 'C')
            {
              $arr['DO_Status'] = 'F';
              $arr['SO_Status'] = 'C';
            }
            else
            {
              $arr['DO_Status'] = $this->orders_model->get_do_status_by_so($so->DocEntry);
            }
          }

          $this->orders_model->update($rs->code, $arr);

          $logs = array(
            'code' => $rs->code,
            'sync_code' => 'DO',
            'get_code' => limitText($doNo, 97),
            'status' => 1
          );

          $this->sync_data_model->add_logs($logs);
        }
        else
        {
          $arr = array(
            'last_do_sync' => now()
          );

          $this->orders_model->update($rs->code, $arr);

          $logs = array(
            'code' => $rs->code,
            'sync_code' => 'DO',
            'get_code' => NULL,
            'status' => 1,
            'message' => 'Document not found'
          );

          $this->sync_data_model->add_logs($logs);
        }
      } //--- end foreach

    }
    else
    {
      $arr = array(
        'code' => 'Sync',
        'sync_code' => 'DO',
        'get_code' => NULL,
        'status' => 0,
        'message' => 'No Document to Sync'
      );

      $this->sync_data_model->add_logs($arr);
    }
  }


  public function syncInvCode()
  {
    $ds = $this->orders_model->get_non_inv_code($this->limit);

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $iv = $this->orders_model->get_invoice_order($rs->code);

        if(!empty($iv))
        {
          $ivNo = "";
          $ra = array();
          $i = 1;
          $inv_date = NULL;

          foreach($iv as $ds)
          {
            if(!isset($ra[$ds->DocNum]))
            {
              $ra[$ds->DocNum] = $ds->DocNum;
              $ivNo .= $i === 1 ? $ds->DocNum : "<br/>".$ds->DocNum;
              $inv_date = $ds->DocDate;
              $i++;
            }
          }


          $arr = array(
            'InvoiceNo' => limitText($ivNo, 97),
            'InvoiceDate' => $inv_date,
            'last_inv_sync' => now()
          );

          $so = $this->orders_model->get_sap_order($rs->code);

          if(!empty($so))
          {
            if($so->DocStatus === 'C')
            {
              $openDo = $this->orders_model->count_do_open_staus($rs->code);

              if($openDo > 0)
              {
                $arr['INV_Status'] = 'P';
              }
              else
              {
                $arr['INV_Status'] = 'F';
              }
            }
            else
            {
                $arr['INV_Status'] = 'P';
            }
          }

          $this->orders_model->update($rs->code, $arr);

          $logs = array(
            'code' => $rs->code,
            'sync_code' => 'INV',
            'get_code' => limitText($ivNo, 97),
            'status' => 1
          );

          $this->sync_data_model->add_logs($logs);
        }
        else
        {
          $arr = array(
            'last_inv_sync' => now()
          );

          $this->orders_model->update($rs->code, $arr);

          $logs = array(
            'code' => $rs->code,
            'sync_code' => 'INV',
            'get_code' => NULL,
            'status' => 1,
            'message' => 'Document not found'
          );

          $this->sync_data_model->add_logs($logs);
        }
      }
    }
    else
    {
      $arr = array(
        'code' => 'Sync',
        'sync_code' => 'INV',
        'get_code' => NULL,
        'status' => 0,
        'message' => 'No Document to Sync'
      );

      $this->sync_data_model->add_logs($arr);
    }

  }



  public function syncSaleName()
  {
    $list = $this->db->select('id, sale_id')->where('id >', 0)->where('sale_id IS NOT NULL', NULL, FALSE)->get('user');

    if($list->num_rows() > 0)
    {
      foreach($list->result() as $rs)
      {
        $name = $this->user_model->get_saleman_name($rs->sale_id);

        if(! empty($name))
        {
          $arr = array(
            'sale_name' => $name
          );

          $this->user_model->update($rs->id, $arr);
        }
      }
    }
  }


  public function clear_filter()
  {
    $filter = array(
      'sync_code',
      'docType', 'docNum',
      'sync_status',
      'sync_from_date',
      'sync_to_date'
    );

    clear_filter($filter);

    echo 'done';
  }



} //--- end class

 ?>
