<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pegawai_penempatan extends CI_Model
{
    var $table = 'pegawai_penempatan';

    public function __construct()
    {
        parent::__construct();
    }


    // public function get_unit_usaha_by_bisnis($id_unit_bisnis)
    // {
    //     $this->db->distinct();
    //     $this->db->select('p.id_unit_usaha, u.nm_unit_usaha');
    //     $this->db->from($this->table . ' p');
    //     $this->db->join('unit_usaha u', 'p.id_unit_usaha = u.id_unit_usaha');
    //     $this->db->where('p.id_unit_bisnis', $id_unit_bisnis);
    //     $query = $this->db->get();
    //     return $query->result();
    // }


    // public function get_unit_organisasi_by_usaha($id_unit_usaha)
    // {
    //     $this->db->distinct();
    //     $this->db->select('p.id_unit_organisasi, o.nm_unit_organisasi');
    //     $this->db->from($this->table . ' p');
    //     $this->db->join('unit_organisasi o', 'p.id_unit_organisasi = o.id_unit_organisasi');
    //     $this->db->where('p.id_unit_usaha', $id_unit_usaha);
    //     return $this->db->get()->result();
    // }
    // public function get_unit_kerja_by_organisasi($id_unit_organisasi)
    // {
    //     $this->db->distinct();
    //     $this->db->select('p.id_unit_kerja, u.nm_unit_kerja');
    //     $this->db->from($this->table . ' p');
    //     $this->db->join('unit_kerja u', 'p.id_unit_kerja = u.id_unit_kerja');
    //     $this->db->where('p.id_unit_organisasi', $id_unit_organisasi);
    //     return $this->db->get()->result();
    // }

    public function get_pegawai_by_unit_kerja($id_unit_kerja)
    {
        return $this->db->select('p.id_pegawai, p.nama')
            ->join('pegawai p', 'p.id_pegawai = pegawai_penempatan.id_pegawai')
            ->where('pegawai_penempatan.id_unit_kerja', $id_unit_kerja)
            ->get($this->table)
            ->result();
    }

    public function get_pegawai_by_unit($unit_usaha, $unit_kerja)
    {
        $this->db->select('pp.id_pegawai, p.nama');
        $this->db->from('pegawai_penempatan pp');
        $this->db->join('pegawai p', 'pp.id_pegawai = p.id_pegawai', 'left');
        $this->db->where('pp.id_unit_usaha', $unit_usaha);
        $this->db->where('pp.id_unit_kerja', $unit_kerja);

        $query = $this->db->get();
        return $query->result();
    }

    public function get_unit_level_by_unit($unit_usaha, $unit_kerja)
    {
        $this->db->select('DISTINCT pp.id_unit_level, ul.nm_unit_level');
        $this->db->from('pegawai_penempatan pp');
        $this->db->join('unit_level ul', 'pp.id_unit_level = ul.id_unit_level', 'left');
        $this->db->where('pp.id_unit_usaha', $unit_usaha);
        $this->db->where('pp.id_unit_kerja', $unit_kerja);

        $query = $this->db->get();
        return $query->result();
    }


    // yang dipake
    public function get_pegawai_by_unit_usaha_unit_kerja($unit_usaha, $unit_kerja)
    {
        $this->db->select('pegawai.id_pegawai, pegawai.nama');
        $this->db->from('pegawai_penempatan');
        $this->db->join('pegawai', 'pegawai.id_pegawai = pegawai_penempatan.id_pegawai');
        $this->db->where('pegawai_penempatan.id_unit_usaha', $unit_usaha);
        $this->db->where('pegawai_penempatan.id_unit_kerja', $unit_kerja);
        $query = $this->db->get();

        return $query->result();
    }

    public function get_unit_level_by_pegawai($id_pegawai)
    {
        $this->db->select('pegawai_penempatan.id_unit_level, unit_level.nm_unit_level');
        $this->db->from('pegawai_penempatan');
        $this->db->join('unit_level', 'unit_level.id_unit_level = pegawai_penempatan.id_unit_level');
        $this->db->where('pegawai_penempatan.id_pegawai', $id_pegawai);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }
}
