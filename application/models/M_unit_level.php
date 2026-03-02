<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_unit_level extends CI_Model
{
    var $table          = 'unit_level';
    var $column_order   = array(null, 'id_unit_level', 'nm_unit_level', null);
    var $column_search  = array('id_unit_level', 'nm_unit_level');
    var $order          = array('id_unit_level' => 'asc');

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
                if ($i === 0) // first loop
                {
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

    public function get_by_id($id_unit_level)
    {
        $this->db->where('id_unit_level', $id_unit_level);
        return $this->db->get($this->table)->row();
    }
    public function get_all()
    {
        return $this->db->order_by('id_unit_level', 'ASC')->get($this->table)->result();
    }


    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id_unit_level', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id_unit_level', $id);
        return $this->db->delete($this->table);
    }
}
