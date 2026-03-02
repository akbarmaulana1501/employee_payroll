<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tunjangan_fungsional extends CI_Model
{
    var $table = 'master_tunjangan_fungsional';
    var $id = 'id_tunjangan';
    var $column_order = ['m.id_tunjangan', 'e.nama_eselon', 'u.nama_unit_usaha', 'm.tunjangan'];
    var $column_search = ['m.id_tunjangan', 'e.nama_eselon', 'u.nama_unit_usaha'];
    var $order = ['m.id_tunjangan' => 'ASC'];

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
        $this->db->select('m.*, e.id_eselon AS nama_eselon, e.jenis_eselon AS jenis_eselon, u.nm_unit_usaha AS nama_unit_usaha'); // <-- tambahkan SELECT ini
        $this->db->from("$this->table m");
        $this->db->join('pegawai_eselon e', 'e.id_eselon = m.id_eselon', 'left');
        $this->db->join('unit_usaha u', 'u.id_unit_usaha = m.id_unit_usaha', 'left');

        $search = $_POST['search']['value'] ?? null;
        $i = 0;
        foreach ($this->column_search as $item) {
            if ($search) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search);
                } else {
                    $this->db->or_like($item, $search);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $colIdx = $_POST['order'][0]['column'];
            $colDir = $_POST['order'][0]['dir'];
            $this->db->order_by($this->column_order[$colIdx], $colDir);
        } else {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }


    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->db->get()->num_rows();
    }

    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    public function get_by_id($id)
    {
        $this->db->from("$this->table m");
        $this->db->join('pegawai_eselon e', 'e.id_eselon = m.id_eselon', 'left');
        $this->db->join('unit_usaha u', 'u.id_unit_usaha = m.id_unit_usaha', 'left');
        $this->db->where('m.' . $this->id, $id);
        return $this->db->get()->row();
    }

    public function get_all()
    {
        $this->db->from("$this->table m");
        $this->db->join('pegawai_eselon e', 'e.id_eselon = m.id_eselon', 'left');
        $this->db->join('unit_usaha u', 'u.id_unit_usaha = m.id_unit_usaha', 'left');
        return $this->db->get()->result();
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

    public function is_duplicate($id_eselon, $id_unit_usaha, $exclude_id = null)
    {
        $this->db->where('id_eselon', $id_eselon);
        $this->db->where('id_unit_usaha', $id_unit_usaha);
        if ($exclude_id) {
            $this->db->where("{$this->id} !=", $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
}
