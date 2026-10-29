<?php
class Auto_cancel_orders extends CI_Controller
{
  private $limit = 100;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('orders_model');
  }

  public function index()
  {
    if(is_true(getConfig('ORDER_AUTO_CANCEL')))
    {
      $list = $this->get_cancel_list();

      if (!empty($list))
      {
        $i = 1;
        $j = 0;
        $orders = [];

        foreach ($list as $rs)
        {
          $orders[$j][] = $rs->code;
          $i++;

          if($i > $this->limit)
          {
            $i = 1;
            $j++;
          }
        }

        if (! empty($orders))
        {
          $arr = array(
            'Status' => -1,
            'DocNum' => NULL,
            'Message' => NULL,
            'sap_date' => NULL,
            'temp_date' => NULL,
            'isCancel' => 1,
            'cancel_by' => 'system',
            'cancel_date' => date('Y-m-d H:i:s')
          );

          foreach($orders as $order)
          {
            $cancelled = $this->db->where_in('code', $order)->update('orders', $arr);

            if ($cancelled)
            {
              $log = array(
                'orders_code' => json_encode($order),
                'status' => 'success'
              );

              $this->db->insert('order_cancel_logs', $log);
            }
            else
            {
              $log = array(
                'orders_code' => json_encode($order),
                'status' => 'failed'
              );

              $this->db->insert('order_cancel_logs', $log);
            }
          }          
        }
      }
      else
      {
        $log = array(
          'orders_code' => NULL,
          'status' => 'no orders to cancel'
        );

        $this->db->insert('order_cancel_logs', $log);
      }      
    }    
  }


  private function get_cancel_list()
  {
    $days = intval(getConfig('ORDER_EXPIRATION'));
    $date = date('Y-m-d 00:00:00', strtotime("-{$days} days"));    

    $rs = $this->db
    ->select('code')
    ->where('status', 0)
    ->where('credit_issue', 1)
    ->where('is_over_due', 1)
    ->where('credit_case_id IS NOT NULL', NULL, FALSE)
    ->where('request_date <', $date)
    ->group_start()
    ->where('credit_approval', 'R')
    ->or_where('reply_date IS NULL', NULL, FALSE)
    ->group_end()
    ->order_by('date_add', 'ASC')
    ->get('orders');    

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}