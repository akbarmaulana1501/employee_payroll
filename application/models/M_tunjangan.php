<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tunjangan extends CI_Model
{
    var $table          = 'master_tunjangan';
    var $id             = 'id_tunjangan';
    var $column_order   = array('id_tunjangan', 'nama_tunjangan');
    var $column_search  = array('id_tunjangan', 'nama_tunjangan');
    var $order          = array('id_tunjangan' => 'ASC');

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
        $this->db->from($this->table);

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
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


    public function get_all()
    {
        return $this->db->get($this->table)->result();
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
}

    // public function get_total_tunjangan_tetap_by_pegawai($id_pegawai)
    // {
    //     $this->db->select_sum('jumlah');
    //     $this->db->where('jenis_tunjangan', 'tetap');
    //     $query = $this->db->get($this->table);
    //     if ($query->num_rows() > 0) {
    //         return $query->row()->jumlah ?? 0;
    //     }
    //     return 0;
    // }