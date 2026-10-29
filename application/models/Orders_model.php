<?php
class Orders_model extends CI_Model
{
  private $tb = "orders";
  private $td = "order_detail";

  public function __construct()
  {
    parent::__construct();
  }

  public function get_overdue_amount($CardCode, $CustCode = NULL)
  {
    $day = 1;
    $this->ms->select_sum('OINV.DocTotal')->select_sum('OINV.PaidToDate')->from('OINV');

    if( ! empty($CustCode))
    {
      $this->ms
        ->join('OCRD', 'OINV.CardCode = OCRD.CardCode', 'left')
        ->where('OCRD.U_TPD_CUST_HCode', $CustCode)
        ->where('OINV.DocTotal >', 'OINV.PaidToDate', FALSE)
        ->where("DATEADD(day,{$day}, OINV.DocDueDate) < ", "GETDATE()", FALSE);
    }
    else 
    {
      $this->ms
        ->where('CardCode', $CardCode)
        ->where('DocTotal >', 'PaidToDate', FALSE)
        ->where("DATEADD(day,{$day}, DocDueDate) < ", "GETDATE()", FALSE);
    }

    $rs = $this->ms->get();
    
    if($rs->num_rows() === 1)
    {
      $row = $rs->row();
      return $row->DocTotal - $row->PaidToDate;
    }

    return 0;
  }

  public function is_over_due($cardCode, $custCode = NULL)
  {
    $day = 1;

    $this->ms->from('OINV');

    if( ! empty($custCode))
    {
      $this->ms
        ->join('OCRD', 'OINV.CardCode = OCRD.CardCode', 'left')
        ->where('OCRD.U_TPD_CUST_HCode', $custCode)
        ->where('OINV.DocTotal >', 'OINV.PaidToDate', FALSE)
        ->where("DATEADD(day,{$day}, OINV.DocDueDate) < ", "GETDATE()", FALSE);
    }
    else 
    {
      $this->ms
        ->where('CardCode', $cardCode)
        ->where('DocTotal >', 'PaidToDate', FALSE)
        ->where("DATEADD(day,{$day}, DocDueDate) < ", "GETDATE()", FALSE);
    }

    return $this->ms->count_all_results() > 0 ? TRUE : FALSE;    
  }


  public function get_credit_used_amount($CardCode, $CustCode = NULL)
  {
    $this->db
    ->select_sum('DocTotal', 'credit_used')
    ->where_in('status', array('0', '1', '3'))
    ->where('Approved !=', 'R')
    ->where('credit_approval !=', 'R');
    
    if( ! empty($CustCode))
    {
      $this->db->where('CustCode', $CustCode);
    }
    else
    {
      $this->db->where('CardCode', $CardCode);
    }

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->credit_used;
    }

    return 0;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('order_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds) && !empty($code))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($id) && !empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }  


  public function update_details_by_batch(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->update_batch($this->td, $ds, 'id');
    }

    return FALSE;
  }

  public function approve_detail($id)
  {
    return $this->db->set('status', 'A')->where('id', $id)->or_where('link_id', $id)->update($this->td);
  }


  public function approve_details($code)
  {
    return $this->db->set('status', 'A')->where('order_code', $code)->update($this->td);
  }


  public function reject_detail($id, $reason)
  {
    return $this->db->set('status', 'R')->set('reject_text', $reason)->where('id', $id)->or_where('link_id', $id)->update($this->td);
  }


  public function reject_details($code)
  {
    $arr = array('status' => 'R');
    return $this->db->where('order_code', $code)->update($this->td, $arr);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($code)
  {
    return $this->db->where('order_code', $code)->delete($this->td);
  }


  function count_rows(array $ds = array())
  {
    if(isset($ds['con_id']) && $ds['con_id'] !== 'all')
    {
      $this->db->where('condition_id', $ds['con_id']);
    }

    if( ! empty($ds['WebCode']))
    {
      $this->db->like('code', $ds['WebCode']);
    }

    if( ! empty($ds['CardCode']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['CardCode']);
      $this->db->or_like('CardName', $ds['CardCode']);
      $this->db->group_end();
    }

    if (isset($ds['user_id']) && $ds['user_id'] !== 'all')
    {
      $this->db->where('user_id', $ds['user_id']);
    }

    if( isset($ds['Approver']) && $ds['Approver'] !== 'all')
    {
      $this->db->where('Approver', $ds['Approver']);
    }

    if( ! empty($ds['DocNum']))
    {
      $this->db->like('DocNum', $ds['DocNum']);
    }

    if( ! empty($ds['DeliveryNo']))
    {
      $this->db->like('DeliveryNo', $ds['DeliveryNo']);
    }

    if( ! empty($ds['InvoiceNo']))
    {
      $this->db->like('InvoiceNo', $ds['InvoiceNo']);
    }

    if( ! empty($ds['PoNo']))
    {
      $this->db->like('NumAtCard', $ds['PoNo'] );
    }

    if( ! empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=',to_date($ds['toDate']));
    }

    if($ds['Approved'] !== 'all')
    {
      if($ds['Approved'] === 'AP')
      {
        $this->db->where('Approved', 'A')->where('Approval_status', 'P');
      }
      else
      {
        if($ds['Approved'] === 'A')
        {
          $this->db->where('Approved', 'A')->where('Approval_status', 'F');
        }
        else
        {
          $this->db->where('Approved', $ds['Approved']);
        }
      }
    }

    if (isset($ds['credit_issue']) && $ds['credit_issue'] !== 'all')
    {
      $this->db->where('credit_issue', $ds['credit_issue']);
    }

    if (isset($ds['is_over_due']) && $ds['is_over_due'] !== 'all')
    {
      $this->db->where('is_over_due', $ds['is_over_due']);
    }

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] !== 'all')
    {
      $this->db->where('isExport', $ds['is_export']);
    }

    if($ds['SO_Status'] != 'all')
    {
      if($ds['SO_Status'] == 'x')
      {
        $this->db->where('SO_Status IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('SO_Status', $ds['SO_Status']);
      }
    }

    if(isset($ds['is_discount_sales']) && $ds['is_discount_sales'] != 'all')
    {
      $this->db->where('is_discount_sales', $ds['is_discount_sales']);
    }

    if($ds['DO_Status'] != 'all')
    {
      if($ds['DO_Status'] == 'x')
      {
        $this->db->where('DO_Status IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('DO_Status', $ds['DO_Status']);
      }
    }

    if($ds['INV_Status'] != 'all')
    {
      if($ds['INV_Status'] == 'x')
      {
        $this->db->where('INV_Status IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('INV_Status', $ds['INV_Status']);
      }
    }

    if( ! $this->isGM && ! $this->_SuperAdmin && ! $this->isAdmin)
    {
      if(!$this->isGM && !$this->_SuperAdmin && !$this->isAdmin)
      {
        //--- จะมี user_team ได้ ต้องถูกเพิ่มเป็น lead เท่านั้น
        $teams = $this->get_user_team($this->_user->id);
        //---- case
        if( ! empty($teams))
        {
          $this->db->group_start();
          $this->db->where('user_id', $this->_user->id);

          if( ! empty($this->_user->area_id))
          {
            $this->db->or_where('area_id', $this->_user->area_id);
          }

          $this->db->or_where_in('team_id', $teams);
          $this->db->group_end();
        }
        else
        {
          $this->db
          ->group_start()
          ->where('user_id', $this->_user->id)
          ->or_where('area_id', $this->_user->area_id)
          ->group_end();
        }
      }
    }

    return $this->db->count_all_results('orders');
  }


  function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {

    if(isset($ds['con_id']) && $ds['con_id'] !== 'all')
    {
      $this->db->where('condition_id', $ds['con_id']);
    }

    if( ! empty($ds['WebCode']))
    {
      $this->db->like('code', $ds['WebCode']);
    }

    if( ! empty($ds['CardCode']))
    {
      $this->db->group_start();
      $this->db->like('CardCode', $ds['CardCode']);
      $this->db->or_like('CardName', $ds['CardCode']);
      $this->db->group_end();
    }

    if( isset($ds['user_id']) && $ds['user_id'] !== 'all')
    {
      $this->db->where('user_id', $ds['user_id']);
    }    

    if( isset($ds['Approver']) && $ds['Approver'] !== 'all')
    {
      $this->db->where('Approver', $ds['Approver']);
    }

    if( ! empty($ds['DocNum']))
    {
      $this->db->like('DocNum', $ds['DocNum']);
    }

    if( ! empty($ds['DeliveryNo']))
    {
      $this->db->like('DeliveryNo', $ds['DeliveryNo']);
    }

    if( ! empty($ds['InvoiceNo']))
    {
      $this->db->like('InvoiceNo', $ds['InvoiceNo']);
    }

    if( ! empty($ds['PoNo']))
    {
      $this->db->like('NumAtCard', $ds['PoNo'] );
    }

    if( ! empty($ds['fromDate']) && !empty($ds['toDate']))
    {
      $this->db->where('DocDate >=', from_date($ds['fromDate']));
      $this->db->where('DocDate <=',to_date($ds['toDate']));
    }

    if($ds['Approved'] !== 'all')
    {
      if($ds['Approved'] === 'AP')
      {
        $this->db->where('Approved', 'A')->where('Approval_status', 'P');
      }
      else
      {
        if($ds['Approved'] === 'A')
        {
          $this->db->where('Approved', 'A')->where('Approval_status', 'F');
        }
        else
        {
          $this->db->where('Approved', $ds['Approved']);
        }
      }
    }

    if(isset($ds['credit_issue']) && $ds['credit_issue'] !== 'all')
    {
      $this->db->where('credit_issue', $ds['credit_issue']);
    }

    if(isset($ds['is_over_due']) && $ds['is_over_due'] !== 'all')
    {
      $this->db->where('is_over_due', $ds['is_over_due']);
    }    

    if($ds['Status'] !== 'all')
    {
      $this->db->where('Status', $ds['Status']);
    }

    if (isset($ds['is_export']) && $ds['is_export'] !== 'all')
    {
      $this->db->where('isExport', $ds['is_export']);
    }

    if(isset($ds['is_discount_sales']) && $ds['is_discount_sales'] != 'all')
    {
      $this->db->where('is_discount_sales', $ds['is_discount_sales']);
    }

    if($ds['SO_Status'] != 'all')
    {
      if($ds['SO_Status'] == 'x')
      {
        $this->db->where('SO_Status IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('SO_Status', $ds['SO_Status']);
      }
    }

    if($ds['DO_Status'] != 'all')
    {
      if($ds['DO_Status'] == 'x')
      {
        $this->db->where('DO_Status IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('DO_Status', $ds['DO_Status']);
      }
    }

    if($ds['INV_Status'] != 'all')
    {
      if($ds['INV_Status'] == 'x')
      {
        $this->db->where('INV_Status IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('INV_Status', $ds['INV_Status']);
      }
    }

    if(!$this->isGM && !$this->_SuperAdmin && !$this->isAdmin)
    {
      //--- จะมี user_team ได้ ต้องถูกเพิ่มเป็น lead เท่านั้น
      $teams = $this->get_user_team($this->_user->id);
      //---- case
      if( ! empty($teams))
      {
        $this->db->group_start();
        $this->db->where('user_id', $this->_user->id);

        if( ! empty($this->_user->area_id))
        {
          $this->db->or_where('area_id', $this->_user->area_id);
        }

        $this->db->or_where_in('team_id', $teams);
        $this->db->group_end();
      }
      else
      {
        $this->db
        ->group_start()
        ->where('user_id', $this->_user->id)
        ->or_where('area_id', $this->_user->area_id)
        ->group_end();
      }
    }


    $this->db->order_by('code', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function price_list_name($price_list, $special_price_list = NULL)
  {
    if( ! empty($price_list))
    {
      if($price_list < 0 && ! empty($special_price_list))
      {
        $rs = $this->db->select('name AS ListName')->where('id', $special_price_list)->get('special_price_list');
      }
      else
      {
        $rs = $this->ms->select('ListName')->where('ListNum', $price_list)->get('OPLN');
      }

      if($rs->num_rows() === 1)
      {
        return $rs->row()->ListName;
      }
    }

    return NULL;
  }




  public function get_currency_rate($code, $date)
  {
    $rs = $this->ms->select('Rate')->where('Currency', $code)->where('RateDate', $date)->get('ORTT');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->Rate;
    }

    return NULL;
  }


  private function get_user_team($user_id)
  {
    $qs = $this->db->query("SELECT team_id FROM user_team WHERE user_id = {$user_id}");

    if($qs->num_rows() > 0)
    {
      $ds = array();

      foreach($qs->result() as $rs)
      {
        $ds[$rs->team_id] = $rs->team_id;
      }

      return $ds;
    }

    return NULL;
  }


  public function get_sale_in($txt)
  {
    $qr = "SELECT SlpCode FROM OSLP WHERE SlpName LIKE N'%{$this->ms->escape_str($txt)}%'";
    $qs = $this->ms->query($qr);

    if($qs->num_rows() > 0)
    {
      $arr = array();
      foreach($qs->result() as $rs)
      {
        $arr[] = $rs->SlpCode;
      }

      return $arr;
    }

    return array('abcdefghijklmnopqrstuvwxyz');
  }


  public function get_payment_term_list()
  {
    $rs = $this->ms
    ->select('GroupNum AS id, PymntGroup AS name, ExtraDays')
    ->get('OCTG');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_sap_order($code)
  {
    $rs = $this->ms
    ->where('U_WEB_ORNO', $code)
    ->where('CANCELED', 'N')
    ->count_all_results('ORDR');
    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_sap_order($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocNum, DocStatus')
    ->where('U_WEB_ORNO', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'DESC')
    ->get('ORDR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_do_status_by_so($docEntry)
  {
    $rs = $this->ms
    ->from('RDR1')
    ->join('ORDR', 'RDR1.DocEntry = ORDR.DocEntry', 'left')
    ->where('RDR1.DocEntry', $docEntry)
    ->where('ORDR.DocStatus', 'O')
    ->where('RDR1.OpenQty >', 0)
    ->count_all_results();

    if($rs > 0)
    {
      return 'P';
    }

    return 'F';
  }



  public function get_so_open_qty($code)
  {
    //--- Do ถูกดึงไปเปิด AR/Inv ครบแล้วหรือยัง
    $rs = $this->ms
    ->select_sum('RDR1.OpenQty')
    ->from('RDR1')
    ->join('ORDR', 'RDR1.DocEntry = ORDR.DocEntry', 'left')
    ->where('ORDR.U_WEB_ORNO', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->OpenQty;
    }

    return 0;
  }



  public function get_temp_data($code) //-- web order
  {
    $rs = $this->mc
    ->select('U_WEB_ORNO, CardCode, CardName, F_WebDate, F_SapDate, F_Sap, Message')
    ->where('U_WEB_ORNO', $code)
    ->order_by('DocEntry', 'DESC')
    ->get('ORDR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_order($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_WEB_ORNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('ORDR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function drop_temp_exists_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('RDR1');
    $this->mc->where('DocEntry', $docEntry)->delete('ORDR');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }



  public function add_temp_order(array $ds = array())
  {
    if($this->mc->insert('ORDR', $ds))
    {
      return $this->mc->insert_id();
    }

    return FALSE;
  }



  public function add_temp_detail(array $ds = array())
  {
    return $this->mc->insert('RDR1', $ds);
  }



  public function get_temp_status($code)
  {
    $rs = $this->mc->select('DocEntry, F_Sap, F_SapDate, Message')->where('U_WEB_ORNO', $code)->order_by('DocEntry', 'DESC')->get('ORDR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }




  public function get_open_qty($code, $item_code)
  {
    $rs = $this->ms
    ->select_sum('RDR1.OpenQty')
    ->from('RDR1')
    ->join('ORDR', 'RDR1.DocEntry = ORDR.DocEntry', 'left')
    ->where('ORDR.U_WEB_ORNO', $code)
    ->where('ItemCode', $item_code)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->OpenQty;
    }

    return 0.00;
  }


  public function get_do_no($code, $item_code)
  {
    $docNum = "";

    $rs = $this->ms
    ->select('ODLN.DocNum')
    ->from('DLN1')
    ->join('ODLN', 'DLN1.DocEntry = ODLN.DocEntry', 'inner')
    ->where('ODLN.U_WEB_ORNO', $code)
    ->where('ODLN.CANCELED', 'N')
    ->where('DLN1.ItemCode', $item_code)
    ->get();

    if($rs->num_rows() > 0)
    {
      $arr = array();
      $i = 1;
      foreach($rs->result() as $ra)
      {
        if(!isset($arr[$ra->DocNum]))
        {
          $arr[$ra->DocNum] = $ra->DocNum;
          $docNum .= $i == 1 ? $ra->DocNum : "<br/>".$ra->DocNum;
          $i++;
        }
      }
    }

    return $docNum;
  }


  public function get_inv_no_and_date($code, $item_code)
  {
    $rs = $this->ms
    ->select('OINV.DocNum, OINV.DocDate')
    ->from('INV1')
    ->join('OINV', 'INV1.DocEntry = OINV.DocEntry', 'inner')
    ->where('OINV.U_WEB_ORNO', $code)
    ->where('OINV.CANCELED', 'N')
    ->where('INV1.ItemCode', $item_code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_do_open_qty($code)
  {
    //--- Do ถูกดึงไปเปิด AR/Inv ครบแล้วหรือยัง
    $rs = $this->ms
    ->select_sum('DLN1.OpenQty')
    ->from('DLN1')
    ->join('ODLN', 'DLN1.DocEntry = ODLN.DocEntry', 'left')
    ->where('ODLN.U_WEB_ORNO', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->OpenQty;
    }

    return 0;
  }


  public function count_do_open_staus($code)
  {
    return $this->ms
    ->where('DocStatus', 'O')
    ->where('CANCELED', 'N')
    ->where('U_WEB_ORNO', $code)
    ->count_all_results('ODLN');
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    return $rs->row()->code;
  }


  public function get_non_so_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('Status', array(1,3))
    ->where('DocNum IS NULL', NULL, FALSE)
    ->where('Approved', 'A')
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_non_do_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('Status', 2)
    ->where('DocNum IS NOT NULL', NULL, FALSE)
    ->where('Approved', 'A')
    ->where('SO_Status IS NOT NULL', NULL, FALSE)
    ->where('SO_Status !=', 'D')
    ->group_start()
    ->where('DeliveryNo IS NULL', NULL, FALSE)
    ->or_where('DO_Status !=', 'F')
    ->group_end()
    ->order_by('SO_Status', 'ASC')
    ->order_by('last_do_sync', 'ASC')
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_delivery_order($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_WEB_ORNO', $code)
    ->where('CANCELED', 'N')
    ->get('ODLN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('Status', 2)
    ->where('DocNum IS NOT NULL', NULL, FALSE)
    ->where('Approved', 'A')
    ->where('SO_Status IS NOT NULL', NULL, FALSE)
    ->where('SO_Status !=', 'D')
    ->where('DeliveryNo IS NOT NULL', NULL, FALSE)
    ->group_start()
    ->where('InvoiceNo IS NULL', NULL, FALSE)
    ->or_where('INV_Status !=', 'F')
    ->group_end()
    ->order_by('SO_Status', 'ASC')
    ->order_by('last_inv_sync', 'ASC')
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_invoice_order($code)
  {
    $rs = $this->ms
    ->select('DocNum, DocDate')
    ->where('U_WEB_ORNO', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'ASC')
    ->get('OINV');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class

 ?>
