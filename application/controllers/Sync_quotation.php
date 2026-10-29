<?php
class Sync_quotation extends CI_Controller
{
  public $ms;
  public $mc;
  public $limit = 1000;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->load->model('sync_data_model');
  }


  public function index()
  {
    $list = $this->getSyncList();
    $count = 0;
    $update = 0;

    if(!empty($list))
    {
      foreach($list as $ds)
      {
        $count++;
        if($ds->Status == 1 OR $ds->Status == 3)
        {
          $temp = $this->get_temp_status($ds->code);
          if(!empty($temp))
          {

            if($temp->F_Sap === 'Y')
            {
              $DocNum = $this->get_sap_doc_num($ds->code);

              if(!empty($DocNum))
              {
                $arr = array(
                  'DocNum' => $DocNum,
                  'sap_date' => $temp->F_SapDate,
                  'Status' => 2,  //-- เข้า SAP แล้ว
                  'Message' => NULL
                );

                $this->update($ds->code, $arr);
                $update++;
              }
              else
              {
                $draft_code = $this->get_sap_draft_code($ds->code);
                if(!empty($draft_code))
                {
                  $arr = array(
                    'sap_date' => $temp->F_SapDate,
                    'Status' => 4,  //-- เข้า Darft ใน SAP แล้ว
                    'Message' => NULL
                  );

                  $this->update($ds->code, $arr);
                  $update++;
                }
              }
            }
            else
            {
              if($temp->F_Sap === 'N')
              {
                $arr = array(
                  'Status' => 3, //--- error
                  'Message' => $temp->Message
                );

                $this->update($ds->code, $arr);
              }
            }
          } //--- end temp
        }

        if($ds->Status == 4)
        {
          $DocNum = $this->get_sap_doc_num($ds->code);

          if(!empty($DocNum))
          {
            $arr = array(
              'DocNum' => $DocNum,
              'sap_date' => $temp->F_SapDate,
              'Status' => 2,  //-- เข้า SAP แล้ว
              'Message' => NULL
            );

            $this->update($ds->code, $arr);
            $update++;
          }
        }

      } //--- endforeach
    }


    //---- add logs
    $logs = array(
      'sync_item' => 'SQ',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);
  }


  private function getSyncList()
  {
    $rs = $this->db
    ->select('code, Status')
    ->where_in('Status', array(1, 3, 4))
    ->order_by('code', 'DESC')
    ->limit($this->limit)
    ->get('quotation');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function get_temp_status($code)
  {
    $rs = $this->mc->select('F_Sap, F_SapDate, Message')->where('U_WEBORDER', $code)->get('OQUT');
    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  private function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_WEBORDER', $code)
    ->get('OQUT');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }

  private function get_sap_draft_code($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('ObjType', 23)
    ->where('U_WEBORDER', $code)
    ->get('ODRF');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  private function update($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update('quotation', $ds);
  }




} //--- end class

 ?>
