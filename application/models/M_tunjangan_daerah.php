<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tunjangan_daerah extends CI_Model
{
    private $table = 'master_tunjangan_daerah';
    private $column_order = ['id_tunjangan', 'id_unit_usaha', 'status_pegawai', 'tunjangan'];
    private $column_search = ['id_tunjangan', 'status_pegawai', 'unit_usaha.nm_unit_usaha'];
    private $order = ['id_tunjangan' => 'asc'];

    private function _get_datatables_query()
    {
        $this->db->from("$this->table td");
        $this->db->join('unit_usaha', 'unit_usaha.id_unit_usaha = td.id_unit_usaha', 'left');

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
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        return $this->db->get()->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->db->count_all_results();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id_tunjangan', $id);
        return $this->db->get()->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id_tunjangan', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id_tunjangan', $id);
        return $this->db->delete($this->table);
    }

    public function is_duplicate($id_unit_usaha, $status_pegawai, $exclude_id = null)
    {
        $this->db->from($this->table);
        $this->db->where('id_unit_usaha', $id_unit_usaha);
        $this->db->where('status_pegawai', $status_pegawai);
        if ($exclude_id !== null) {
            $this->db->where('id_tunjangan !=', $exclude_id);
        }
        return $this->db->get()->num_rows() > 0;
    }
}
