<?php 
class Sales_person_model extends CI_Model
{
  private $table = 'OSLP';

  public function __construct()
  {
    parent::__construct();
  }

  public function get($id)
  {
    $rs = $this->ms->where('SlpCode', $id)->get($this->table);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_name($id)
  {
    $rs = $this->ms->select('SlpName')->where('SlpCode', $id)->get($this->table);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->SlpName;
    }

    return NULL;
  }

  public function get_all()
  {
    $rs = $this->ms->get($this->table);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //-- end class