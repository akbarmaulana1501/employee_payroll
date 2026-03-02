<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pegawai_perumahan extends CI_Model
{
    var $table = 'pegawai_perumahan';
    var $column_order = ['id_pegawai_perumahan', 'id_pegawai', 'id_tunjangan', 'nominal'];
    var $column_search = ['id_pegawai_perumahan', 'id_pegawai', 'id_tunjangan', 'nominal'];
    var $order = ['id_pegawai_perumahan' => 'asc'];

    private function _get_datatables_query()
    {
        $this->db->from($this->table);
        // Join pegawai dan master_tunjangan_perumahan untuk tampil nama di datatable
        $this->db->join('pegawai', 'pegawai.id_pegawai = pegawai_perumahan.id_pegawai', 'left');
        $this->db->join('master_tunjangan_perumahan', 'master_tunjangan_perumahan.id_tunjangan = pegawai_perumahan.id_tunjangan', 'left');
        $this->db->select('pegawai_perumahan.*, pegawai.nama as nama_pegawai, master_tunjangan_perumahan.tunjangan as nominal_master, master_tunjangan_perumahan.id_eselon');

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
        return $this->db->count_all($this->table);
    }

    public function get_by_id($id)
    {
        $this->db->where('id_pegawai_perumahan', $id);
        return $this->db->get($this->table)->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id_pegawai_perumahan' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id_pegawai_perumahan' => $id]);
    }

    // Cek duplikasi: kombinasi id_pegawai & id_tunjangan
    public function is_duplicate($id_pegawai, $id_tunjangan, $id = null)
    {
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('id_tunjangan', $id_tunjangan);
        if ($id) {
            $this->db->where('id_pegawai_perumahan !=', $id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }

    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }
}
