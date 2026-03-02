<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pegawai_potongan extends CI_Model
{
    var $table = 'pegawai_potongan';
    var $column_order = ['id_pegawai_potongan', 'id_pegawai_penggajian', 'id_potongan', 'nominal'];
    var $column_search = ['id_pegawai_potongan', 'id_pegawai_penggajian', 'id_potongan'];
    var $order = ['id_pegawai_potongan' => 'asc'];

    private function _get_datatables_query()
    {
        $this->db->from($this->table);
        $this->db->select('pegawai_potongan.*, master_potongan.nama_potongan');
        $this->db->join('master_potongan', 'master_potongan.id_potongan = pegawai_potongan.id_potongan', 'left');

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

        // Order
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
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
        return $this->db->get()->num_rows();
    }

    public function count_all()
    {
        return $this->db->from($this->table)->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id_pegawai_potongan', $id);
        $this->db->join('master_potongan', 'master_potongan.id_potongan = pegawai_potongan.id_potongan', 'left');
        return $this->db->get()->row();
    }

    public function get_by_penggajian($id_pegawai_penggajian)
    {
        $this->db->from($this->table);
        $this->db->where('id_pegawai_penggajian', $id_pegawai_penggajian);
        $this->db->join('master_potongan', 'master_potongan.id_potongan = pegawai_potongan.id_potongan', 'left');
        return $this->db->get()->result();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id_pegawai_potongan' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id_pegawai_potongan' => $id]);
    }

    public function delete_by_penggajian($id_pegawai_penggajian)
    {
        return $this->db->delete($this->table, ['id_pegawai_penggajian' => $id_pegawai_penggajian]);
    }
}
