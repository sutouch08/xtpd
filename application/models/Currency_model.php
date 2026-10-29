<?php
class Currency_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_list()
  {
    $qr = "SELECT CurrCode AS Code FROM OCRN WHERE CurrCode = 'THB' OR CurrCode LIKE '__S' ORDER BY CurrCode ASC";
    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}

 ?>
