<?php
class Sync_order_status extends CI_Controller
{
  public $mc;
  public $limit = 50;

  public function __construct()
  {
    parent::__construct();
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
  }


  public function index()
  {
    $list = $this->getUpdateList();

    if(!empty($list))
    {
      foreach($list as $ds)
      {
        if($ds->Type == 'SO' && $ds->Cancelled == 'Y')
        {
          $arr = array(
            'SO_Status' => 'D',
            'DO_Status' => NULL,
            'INV_Status' => NULL
          );
        }
        else
        {
          $arr = array();

          if($ds->Type == 'DO' && $ds->Cancelled == 'Y')
          {
            $arr['DO_Status'] = NULL;
            $arr['DeliveryNo'] = NULL;
          }

          if($ds->Type == 'INV' && $ds->Cancelled == 'Y')
          {
            $arr['INV_Status'] = NULL;
            $arr['InvoiceNo'] = NULL;
          }
        }

        if( ! $this->update($ds->U_WEB_ORNO, $arr))
        {
          $upd = array(
            'F_Web' => 'N',
            'Message' => "Update failed",
            'F_WebDate' => now()
          );
        }
        else
        {
          $upd = array(
            'F_Web' => 'Y',
            'Message' => NULL,
            'F_WebDate' => now()
          );
        }

        $this->update_temp($ds->DocEntry, $upd);

        $logs = array(
          'code' => $ds->U_WEB_ORNO,
          'sync_code' => 'STATUS',
          'get_code' => $ds->Cancelled == 'Y' ? 'Cancelled' : NULL,
          'status' => 1
        );

        $this->add_logs($logs);

      } //--- endforeach
    }
  }



  private function getUpdateList()
  {
    $rs = $this->mc
    ->group_start()
    ->where('F_Web IS NULL', NULL, FALSE)
    ->or_where('F_Web', 'N')
    ->group_end()
    ->where('DocNum IS NOT NULL', NULL, FALSE)
    ->limit($this->limit)
    ->get('Status');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('orders', $ds);
    }

    return TRUE;
  }


  private function update_temp($docEntry, array $ds = array())
  {

    if(!empty($ds))
    {
      return $this->mc->where('DocEntry', $docEntry)->update('Status', $ds);
    }

    return TRUE;
  }

  public function add_logs($arr)
  {
    return $this->db->insert('sync_logs', $arr);
  }

} //--- end class

 ?>
