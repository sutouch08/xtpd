<?php
class Sync_customer extends CI_Controller
{
  public $ms;  
  
  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database    
    $this->load->model('customer_model');
  }

  public function index()
  {
    $last_sync = $this->customer_model->get_last_sync_date();
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
  }

} //--- end class
