<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_potongan_opsional extends CI_Model 
{
    var $table          = 'potongan_opsional';
    var $id             = 'id_potongan_opsional';
    var $column_order   = array('id_potongan_opsional', 'id_gaji_header', 'nama_potongan', 'jumlah');
    var $column_search  = array('id_potongan_opsional', 'nama_potongan');
    var $order          = array('id_potongan_opsional' => 'ASC');

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
        $this->db->from($this->table);

        $i = 0;
        foreach ($this->column_search as $item) 
        {
            if($_POST['search']['value']) 
            {
                if($i===0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if(count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if(isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array($this->id => $id))->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where($this->id, $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->delete($this->table);
    }

    public function generate_id()
    {
        $this->db->select_max($this->id);
        $query = $this->db->get($this->table);
        $row = $query->row();

        if(!$row || !$row->{$this->id}) {
            return 'PO_001';
        } else {
            $last_id = $row->{$this->id};
            $num = (int) substr($last_id, 3);
            $num++;
            return 'PO_' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
    }
}
