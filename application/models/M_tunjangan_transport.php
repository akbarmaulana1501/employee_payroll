<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tunjangan_transport extends CI_Model
{
    var $table          = 'tunjangan_transport';
    var $id             = 'id_tunjangan_transport';
    var $column_order   = array(null, 'tunjangan_transport.id_tunjangan_transport', 'unit_usaha.nm_unit_usaha', 'unit_level.nm_unit_level', 'pegawai.nama', 'tunjangan_transport.nominal');
    var $column_search  = array('tunjangan_transport.id_tunjangan_transport', 'unit_usaha.nm_unit_usaha', 'unit_level.nm_unit_level', 'pegawai.nama');
    var $order          = array('tunjangan_transport.id_tunjangan_transport' => 'ASC');

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
        $this->db->select('tunjangan_transport.*, pegawai.nama');
        $this->db->from($this->table);
        // $this->db->join('unit_usaha', 'unit_usaha.id_unit_usaha = tunjangan_transport.id_unit_usaha', 'left');
        // $this->db->join('unit_level', 'unit_level.id_unit_level = tunjangan_transport.id_unit_level', 'left');
        $this->db->join('pegawai', 'pegawai.id_pegawai = tunjangan_transport.id_pegawai', 'left');

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
        return $this->db->get_where($this->table, array($this->id => $id))->row();
    }

    public function get_all()
    {
        return $this->db->order_by($this->id, 'ASC')->get($this->table)->result();
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

    public function get_data_thr()
    {
        $this->db->select('uu.id_unit_usaha,ul.id_unit_level,p.id_pegawai');
        $this->db->from('unit_usaha uu,unit_level ul,pegawai p,tunjangan_transport mthr');
        $this->db->where('uu.id_unit_usaha = mthr.id_unit_usaha');
        $this->db->where('ul.id_unit_level = mthr.id_unit_level');
        $this->db->where('p.id_pegawai = mthr.id_pegawai');
        return $this->db->get()->result();
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }
}
