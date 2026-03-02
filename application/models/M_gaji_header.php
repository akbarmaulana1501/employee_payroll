<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_gaji_header extends CI_Model
{
    var $table          = 'gaji_header';
    var $id             = 'id_gaji_header';
    var $column_order   = array('id_gaji_header', 'id_pegawai', 'periode', 'status_konfirmasi', 'created_at');
    var $column_search  = array('id_gaji_header', 'id_pegawai', 'periode');
    var $order          = array('id_gaji_header' => 'DESC');

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
        $this->db->select('g.*, p.nama');
        $this->db->from($this->table . ' g');
        $this->db->join('pegawai p', 'g.id_pegawai = p.id_pegawai', 'left');

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

    public function get_all_with_pegawai()
    {
        $this->db->select('g.*, p.nama');
        $this->db->from($this->table . ' g');
        $this->db->join('pegawai p', 'g.id_pegawai = p.id_pegawai', 'left');

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

    public function update_gaji_summary($id_gaji_header)
    {
        // Hitung total pendapatan tetap (gapok + tunjangan tetap)
        $this->db->select_sum('jumlah');
        $this->db->where('id_gaji_header', $id_gaji_header);
        $this->db->where_in('jenis_komponen', ['gapok', 'tunjangan_tetap']);
        $total_pendapatan_tetap = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

        // Hitung total pendapatan kotor (gapok + tunjangan tetap + insentif)
        $this->db->select_sum('jumlah');
        $this->db->where('id_gaji_header', $id_gaji_header);
        $this->db->where_in('jenis_komponen', ['gapok', 'tunjangan_tetap', 'insentif']);
        $total_pendapatan_kotor = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

        // Ambil jumlah gaji pokok untuk perhitungan potongan standar
        $this->db->select('jumlah');
        $this->db->where('id_gaji_header', $id_gaji_header);
        $this->db->where('jenis_komponen', 'gapok');
        $gaji_pokok = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

        // Hitung total potongan standar berdasarkan potongan yang ada di master_potongan
        $potongans = $this->db->get('master_potongan')->result();

        $total_potongan_standar = 0;
        foreach ($potongans as $potongan) {
            if ($potongan->tipe_potongan == 'persen') {
                $nilai_potongan = $gaji_pokok * ($potongan->persentase / 100);
                if ($potongan->maksimal !== null) {
                    $nilai_potongan = min($nilai_potongan, $potongan->maksimal);
                }
                $total_potongan_standar += $nilai_potongan;
            } elseif ($potongan->tipe_potongan == 'nominal') {
                $total_potongan_standar += $potongan->nominal; // asumsi ada field nominal
            }
        }

        // Hitung total potongan opsional
        $this->db->select_sum('jumlah');
        $this->db->where('id_gaji_header', $id_gaji_header);
        $this->db->where('jenis_komponen', 'potongan_opsional');
        $total_potongan_opsional = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

        // Total potongan = standar + opsional
        $total_potongan = $total_potongan_standar + $total_potongan_opsional;

        // Hitung gaji bersih
        $gaji_bersih = $total_pendapatan_kotor - $total_potongan;

        // Update gaji header
        $data_update = [
            'total_pendapatan_tetap' => $total_pendapatan_tetap,
            'total_pendapatan_kotor' => $total_pendapatan_kotor,
            'total_potongan' => $total_potongan,
            'gaji_bersih' => $gaji_bersih
        ];

        $this->db->where('id_gaji_header', $id_gaji_header);
        return $this->db->update('gaji_header', $data_update);
    }


    // public function update_gaji_summary($id_gaji_header)
    // {
    //     $this->db->select_sum('jumlah');
    //     $this->db->where('id_gaji_header', $id_gaji_header);
    //     $this->db->where_in('jenis_komponen', ['gapok', 'tunjangan_tetap']);
    //     $total_pendapatan_tetap = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

    //     $this->db->select_sum('jumlah');
    //     $this->db->where('id_gaji_header', $id_gaji_header);
    //     $this->db->where_in('jenis_komponen', ['gapok', 'tunjangan_tetap', 'insentif']);
    //     $total_pendapatan_kotor = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

    //     $this->db->select('jumlah');
    //     $this->db->where('id_gaji_header', $id_gaji_header);
    //     $this->db->where('jenis_komponen', 'gapok');
    //     $gaji_pokok = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

    //     $potongans = $this->db->get('master_potongan')->result();

    //     $total_potongan_standar = 0;
    //     foreach ($potongans as $potongan) {
    //         if ($potongan->tipe_potongan == 'persen') {
    //             $nilai_potongan = $gaji_pokok * ($potongan->persentase / 100);
    //             if ($potongan->maksimal !== null) {
    //                 $nilai_potongan = min($nilai_potongan, $potongan->maksimal);
    //             }
    //             $total_potongan_standar += $nilai_potongan;
    //         } elseif ($potongan->tipe_potongan == 'nominal') {
    //             $total_potongan_standar += $potongan->nominal; // asumsi ada field nominal
    //         }
    //     }

    //     $this->db->select_sum('jumlah');
    //     $this->db->where('id_gaji_header', $id_gaji_header);
    //     $this->db->where('jenis_komponen', 'potongan_opsional');
    //     $total_potongan_opsional = $this->db->get('gaji_detail')->row()->jumlah ?? 0;

    //     $total_potongan = $total_potongan_standar + $total_potongan_opsional;
    //     $gaji_bersih = $total_pendapatan_kotor - $total_potongan;

    //     $data_update = [
    //         'total_pendapatan_tetap' => $total_pendapatan_tetap,
    //         'total_pendapatan_kotor' => $total_pendapatan_kotor,
    //         'total_potongan' => $total_potongan,
    //         'gaji_bersih' => $gaji_bersih
    //     ];

    //     $this->db->where('id_gaji_header', $id_gaji_header);
    //     return $this->db->update('gaji_header', $data_update);
    // }
}
