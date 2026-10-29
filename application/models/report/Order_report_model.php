<?php
class Order_report_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function get_report(array $ds = array())
  {
    $this->db
    ->select('o.*, od.ItemCode, od.ItemName, od.Qty, od.UomCode, od.stdPrice, od.SellPrice, od.DiscPrcnt, od.LineTotal')
    ->from('order_detail AS od')
    ->join('orders AS o', 'od.order_code = o.code', 'left');

    if(!empty($ds))
    {
      if(!empty($ds['fromDate']) && !empty($ds['toDate']))
      {
        $this->db->where('DocDate >=', from_date($ds['fromDate']));
        $this->db->where('DocDate <=', to_date($ds['toDate']));
      }

      if(empty($ds['allCustomer']))
      {
        if(!empty($ds['customerFrom']) && !empty($ds['customerTo']))
        {
          $this->db->where('CardCode >=', $ds['customerFrom'])->where('CardCode <=', $ds['customerTo']);
        }
      }

      if(empty($ds['allCustomerGroup']) && !empty($ds['groupList']))
      {
        $this->db->where_in('CardGroup', $ds['groupList']);
      }

      if(empty($ds['allApprover']) && !empty($ds['approverList']))
      {
        $this->db->where_in('Approver', $ds['approverList']);
      }

      if(isset($ds['approval_status']) && $ds['approval_status'] !== 'all')
      {
        if($ds['approval_status'] == 'AP')
        {
          $this->db->where('Approved', 'A')->where('Approval_status', 'P');
        }
        else
        {
          $this->db->where('Approved', $ds['approval_status']);
        }
      }
    }

    $this->db->order_by('DocDate', 'ASC')->order_by('code', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return NULL;
  }




  public function get_report_by_sale_id($sale_id, $fromDate, $toDate, $status)
  {
    $this->db
    ->select('o.*, od.ItemCode, od.ItemName, od.Qty, od.UomCode, od.stdPrice, od.SellPrice, od.DiscPrcnt, od.LineTotal')
    ->from('order_detail AS od')
    ->join('orders AS o', 'od.order_code = o.code', 'left')
    ->where('o.SlpCode', $sale_id)
    ->where('DocDate >=', from_date($fromDate))
    ->where('DocDate <=', to_date($toDate));

    if($status != 'all')
    {
      if($status == 'AP')
      {
        $this->db->where('o.Approved', 'A')->where('o.Approval_status', 'P');
      }
      else
      {
        $this->db->where('o.Approved', $status);
      }
    }


    $this->db->order_by('o.DocDate', 'ASC')->order_by('code', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return NULL;
  }


  public function get_report_by_user_id($user_id, $fromDate, $toDate, $status)
  {
    $this->db
    ->select('o.*, od.ItemCode, od.ItemName, od.Qty, od.UomCode, od.stdPrice, od.SellPrice, od.DiscPrcnt, od.LineTotal')
    ->from('order_detail AS od')
    ->join('orders AS o', 'od.order_code = o.code', 'left')
    ->where('o.user_id', $user_id)
    ->where('DocDate >=', from_date($fromDate))
    ->where('DocDate <=', to_date($toDate));

    if($status != 'all')
    {
      if($status == 'AP')
      {
        $this->db->where('o.Approved', 'A')->where('o.Approval_status', 'P');
      }
      else
      {
        $this->db->where('o.Approved', $status);
      }
    }


    $this->db->order_by('o.DocDate', 'ASC')->order_by('code', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }


    return NULL;
  }




  public function get_sum_order($user_id, $from_date, $to_date)
  {
    $rs = $this->db
    ->select_sum('DocTotal')
    ->where('user_id', $user_id)
    ->where('DocDate >=', from_date($from_date))
    ->where('DocDate <=', to_date($to_date))
    ->get('orders');


    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocTotal;
    }

    return 0;
  }


  public function get_sum_pending($user_id, $from_date, $to_date)
  {
    $rs = $this->db
    ->select_sum('DocTotal')
    ->where('user_id', $user_id)
    ->where('DocDate >=', from_date($from_date))
    ->where('DocDate <=', to_date($to_date))
    ->where('Approved', 'P')
    ->get('orders');


    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocTotal;
    }

    return 0;
  }


  public function get_sum_approved($user_id, $from_date, $to_date)
  {
    $rs = $this->db
    ->select_sum('order_detail.LineTotal')
    ->from('order_detail')
    ->join('orders', 'order_detail.order_code = orders.code', 'left')
    ->where('orders.user_id', $user_id)
    ->where('orders.DocDate >=', from_date($from_date))
    ->where('orders.DocDate <=', to_date($to_date))
    ->where('order_detail.status', 'A')
    ->where_in('orders.status', array(1, 2))
    ->get();


    if($rs->num_rows() === 1)
    {
      return $rs->row()->LineTotal;
    }

    return 0;
  }


  public function get_sum_rejected($user_id, $from_date, $to_date)
  {
    $rs = $this->db
    ->select_sum('order_detail.LineTotal')
    ->from('order_detail')
    ->join('orders', 'order_detail.order_code = orders.code', 'left')
    ->where('orders.user_id', $user_id)
    ->where('orders.DocDate >=', from_date($from_date))
    ->where('orders.DocDate <=', to_date($to_date))
    ->where('order_detail.status', 'R')
    ->get();


    if($rs->num_rows() === 1)
    {
      return $rs->row()->LineTotal;
    }

    return 0;
  }


} //--- end class


 ?>
