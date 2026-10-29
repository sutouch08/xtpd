<?php
class Credit_approval_model extends CI_Model
{
  private $tb = "orders";
  private $td = "order_detail";
  private $log = "credit_approve_logs";

  public function __construct()
  {
    parent::__construct();
  }

  public function add_log(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->log, $ds);
    }

    return FALSE;
  }

  public function get_logs($code)
  {
    $rs = $this->db->where('order_code', $code)->order_by('id', 'DESC')->get($this->log);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
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
    $rs = $this->db
    ->select('o.*, r.has_document, r.reply_status')
    ->from('orders AS o')
    ->join('payment_request_order AS r', 'o.code = r.code', 'left')
    ->where('o.code', $code)
    ->get();

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
    if( ! empty($ds) && !empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  function count_rows(array $ds = array())
  {
    $this->db      
      ->from('orders AS o')
      ->join('payment_request_order AS r', 'o.code = r.code', 'left')
      ->where('o.status >=', 0)
      ->where('o.credit_issue', 1);

    if (isset($ds['status']))
    {
      // Credit_approval && credit_case_id && reply_status
      // O = รอดำเนินการ, N = รอเอกสาร, R = รอตรวจสอบ, P = รออนุมัติ
      if ($ds['status'] == 'all')
      {
        $this->db->where_not_in('o.credit_approval', ['A', 'R']);
      }
      else if ($ds['status'] == 'AA')
      {
        $this->db->where_in('o.credit_approval', ['A', 'R']);
      }
      else if ($ds['status'] == 'AR')
      {
        $this->db->where('o.credit_approval', 'R');
      }
      else if ($ds['status'] == 'AP')
      {
        $this->db->where('o.credit_approval', 'A');
      }
      else if ($ds['status'] == 'O')
      {
        $this->db
          ->group_start()
          ->where('o.credit_approval', 'O')
          ->where('o.credit_case_id IS NULL', NULL, FALSE)
          ->where('r.reply_status IS NULL', NULL, FALSE)
          ->group_end();
      }
      else if ($ds['status'] == 'N')
      {
        $this->db
          ->group_start()
          ->where('o.credit_approval', 'O')
          ->where('o.credit_case_id IS NOT NULL', NULL, FALSE)
          ->where('r.reply_status', 'N')
          ->group_end();
      }
      else if ($ds['status'] == 'R')
      {
        $this->db
          ->group_start()
          ->where('o.credit_approval', 'O')
          ->where('o.credit_case_id IS NOT NULL', NULL, FALSE)
          ->where('r.reply_status', 'R')
          ->group_end();
      }
      else if ($ds['status'] == 'P')
      {
        $this->db->where('o.credit_approval', 'P');
      }
    }

    if (! empty($ds['WebCode']))
    {
      $this->db->like('o.code', $ds['WebCode']);
    }

    if (! empty($ds['CardCode']))
    {
      $this->db->group_start();
      $this->db->like('o.CardCode', $ds['CardCode']);
      $this->db->or_like('o.CardName', $ds['CardCode']);
      $this->db->group_end();
    }

    if (isset($ds['user_id']) && $ds['user_id'] != 'all')
    {
      $this->db->where('o.user_id', $ds['user_id']);
    }

    if (! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('o.DocDate >=', from_date($ds['from_date']));
      $this->db->where('o.DocDate <=', to_date($ds['to_date']));
    }

    if ($ds['credit_approval'] !== 'all')
    {
      $this->db->where('o.credit_approval', $ds['credit_approval']);
    }
    
    if ($ds['is_overdue'] !== 'all')
    {
      $this->db->where('o.is_over_due', $ds['is_overdue']);
    }

    return $this->db->count_all_results();
  }


  function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('o.*, r.has_document, r.reply_status')
    ->from('orders AS o')
    ->join('payment_request_order AS r', 'o.code = r.code', 'left')
    ->where('o.status >=', 0)
    ->where('o.credit_issue', 1);
    //->where_not_in('o.credit_approval', ['A', 'R']);

    if (isset($ds['status']))
    {
      // Credit_approval && credit_case_id && reply_status
      // O = รอดำเนินการ, N = รอเอกสาร, R = รอตรวจสอบ, P = รออนุมัติ
      if($ds['status'] == 'all')
      {
        $this->db->where_not_in('o.credit_approval', ['A', 'R']);
      }
      else if ($ds['status'] == 'AA')
      {
        $this->db->where_in('o.credit_approval', ['A', 'R']);
      }
      else if ($ds['status'] == 'AR')
      {
        $this->db->where('o.credit_approval', 'R');
      }
      else if ($ds['status'] == 'AP')
      {
        $this->db->where('o.credit_approval', 'A');
      }
      else if ($ds['status'] == 'O')
      {
        $this->db
          ->group_start()
          ->where('o.credit_approval', 'O')
          ->where('o.credit_case_id IS NULL', NULL, FALSE)
          ->where('r.reply_status IS NULL', NULL, FALSE)
          ->group_end();
      }
      else if ($ds['status'] == 'N')
      {
        $this->db
          ->group_start()
          ->where('o.credit_approval', 'O')
          ->where('o.credit_case_id IS NOT NULL', NULL, FALSE)
          ->where('r.reply_status', 'N')
          ->group_end();
      }
      else if ($ds['status'] == 'R')
      {
        $this->db
          ->group_start()
          ->where('o.credit_approval', 'O')
          ->where('o.credit_case_id IS NOT NULL', NULL, FALSE)
          ->where('r.reply_status', 'R')
          ->group_end();
      }
      else if ($ds['status'] == 'P')
      {
        $this->db->where('o.credit_approval', 'P');
      }      
    }

    if( ! empty($ds['WebCode']))
    {
      $this->db->like('o.code', $ds['WebCode']);
    }

    if( ! empty($ds['CardCode']))
    {
      $this->db->group_start();
      $this->db->like('o.CardCode', $ds['CardCode']);
      $this->db->or_like('o.CardName', $ds['CardCode']);
      $this->db->group_end();
    }

    if(isset($ds['user_id']) && $ds['user_id'] != 'all')
    {
      $this->db->where('o.user_id', $ds['user_id']);
    }
        
    if( ! empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('o.DocDate >=', from_date($ds['from_date']));
      $this->db->where('o.DocDate <=',to_date($ds['to_date']));
    }    

    if($ds['credit_approval'] !== 'all')
    {
      $this->db->where('o.credit_approval', $ds['credit_approval']);
    }

    if($ds['is_overdue'] !== 'all')
    {
      $this->db->where('o.is_over_due', $ds['is_overdue']);
    }    

    $this->db->order_by('o.code', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


} //--- end class

 ?>
