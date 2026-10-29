<?php
class Warehouse_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_warehouse_list()
  {
    $rs = $this->ms->select('WhsCode AS code, WhsName AS name')->get('OWHS');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}

 ?>
