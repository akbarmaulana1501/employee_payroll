<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tunjangan_jabatan extends CI_Model
{
    var $table = 'tunjangan_jabatan';
    var $id = 'id_tunjangan_jabatan';
    var $column_order = array('id_tunjangan_jabatan', 'id_unit_level', 'id_unit_usaha', 'jumlah');
    var $column_search = array('id_unit_level', 'id_unit_usaha');
    var $order = array('id_tunjangan_jabatan' => 'ASC');

    private function _get_datatables_query()
    {
        $this->db->from($this->table);
        $this->db->join('unit_level', 'unit_level.id_unit_level = tunjangan_jabatan.id_unit_level', 'left');
        $this->db->join('unit_usaha', 'unit_usaha.id_unit_usaha = tunjangan_jabatan.id_unit_usaha', 'left');

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
        } else {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
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
        return $this->db->get()->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, [$this->id => $id])->row();
    }

    public function get_all()
    {
        return $this->db->order_by('id_tunjangan_jabatan', 'ASC')->get($this->table)->result();
    }

    public function get_tunjangan_by_unit_level_usaha($unit_level, $unit_usaha)
    {
        $this->db->select('tj.id_tunjangan_jabatan, tj.nama_tunjangan_jabatan');
        $this->db->from('tunjangan_jabatan tj');
        $this->db->where('tj.unit_level', $unit_level);
        $this->db->where('tj.unit_usaha', $unit_usaha);
        $query = $this->db->get();
        return $query->result();
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

    public function is_duplicate($unit_level, $unit_usaha, $exclude_id = null)
    {
        $this->db->where('id_unit_level', $unit_level);
        $this->db->where('id_unit_usaha', $unit_usaha);
        if ($exclude_id) {
            $this->db->where('id_tunjangan_jabatan !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }

    public function get_tunjangan_jabatan_by_unit_level_unit_usaha($unit_level, $unit_usaha)
    {
        $this->db->select('id_tunjangan_jabatan, jumlah');
        $this->db->from('tunjangan_jabatan');
        $this->db->where('id_unit_level', $unit_level);
        $this->db->where('id_unit_usaha', $unit_usaha);
        $query = $this->db->get();

        return $query->result();
    }
}
