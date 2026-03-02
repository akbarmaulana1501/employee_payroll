<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tunjangan_kesra extends CI_Model
{
    var $table = 'master_tunjangan_kesra';
    var $column_order = ['id_tunjangan', 'status_pegawai', 'tunjangan'];
    var $column_search = ['id_tunjangan', 'status_pegawai'];
    var $order = ['id_tunjangan' => 'asc'];

    private function _get_datatables_query()
    {
        $this->db->from($this->table);

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
        return $this->db->get_where($this->table, ['id_tunjangan' => $id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id_tunjangan' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id_tunjangan' => $id]);
    }

    public function is_duplicate($status_pegawai, $id = null)
    {
        $this->db->where('status_pegawai', $status_pegawai);
        if ($id) {
            $this->db->where('id_tunjangan !=', $id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }

    public function get_all()
    {
        return $this->db->get($this->table)->result();
    }
}
