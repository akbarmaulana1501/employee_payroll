<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_gaji_detail extends CI_Model
{
    var $table = 'gaji_detail';
    var $id = 'id_gaji_detail';

    // Kolom untuk datatables
    var $column_order = array(null, 'id_gaji_header', 'jenis_komponen', 'nama_komponen', 'jumlah');
    var $column_search = array('id_gaji_header', 'jenis_komponen', 'nama_komponen');
    var $order = array('id_gaji_detail' => 'DESC');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_gaji_header', 'gaji_header');
    }

    private function _get_datatables_query($id_gaji_header = null)
    {
        $this->db->from($this->table);
        if ($id_gaji_header) {
            $this->db->where('id_gaji_header', $id_gaji_header);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value']) && $_POST['search']['value']) {
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
            $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($id_gaji_header = null)
    {
        $this->_get_datatables_query($id_gaji_header);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($id_gaji_header = null)
    {
        $this->_get_datatables_query($id_gaji_header);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id_gaji_header = null)
    {
        $this->db->from($this->table);
        if ($id_gaji_header) {
            $this->db->where('id_gaji_header', $id_gaji_header);
        }
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array($this->id => $id))->row();
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

    public function get_total_by_header($id_gaji_header)
    {
        $this->db->select_sum('jumlah');
        $this->db->where('id_gaji_header', $id_gaji_header);
        $query = $this->db->get($this->table);
        return $query->row()->jumlah ?? 0;
    }

    private function id_gaji_detailOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_gaji_detail, 4) AS UNSIGNED)) AS maxKode FROM gaji_detail";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'GD_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }


    public function generate_gaji_detail_otomatis($id_gaji_header, $id_pegawai)
    {
        $this->db->where('id_gaji_header', $id_gaji_header)->delete($this->table);
        $this->db->select('id_unit_level');
        $this->db->from('pegawai_penempatan');
        $this->db->where('id_pegawai', $id_pegawai);
        $penempatan = $this->db->get()->row();

        if (!$penempatan) return false;

        $id_unit_level = $penempatan->id_unit_level;

        $this->db->select('gaji_pokok');
        $this->db->from('master_gaji_pokok');
        $this->db->where('id_unit_level', $id_unit_level);
        $gaji_pokok_data = $this->db->get()->row();

        $gaji_pokok = $gaji_pokok_data ? $gaji_pokok_data->gaji_pokok : 0;

        $this->insert([
            'id_gaji_detail' => $this->id_gaji_detailOtomatis(),
            'id_gaji_header' => $id_gaji_header,
            'jenis_komponen' => 'gapok',
            'nama_komponen' => 'Gaji Pokok',
            'jumlah' => $gaji_pokok,
        ]);

        $tunjangan_tetap = $this->db->get_where('master_tunjangan', ['jenis_tunjangan' => 'tetap'])->result();

        foreach ($tunjangan_tetap as $t) {
            $this->insert([
                'id_gaji_detail' => $this->id_gaji_detailOtomatis(),
                'id_gaji_header' => $id_gaji_header,
                'jenis_komponen' => 'tunjangan_tetap',
                'nama_komponen' => $t->nama_tunjangan,
                'jumlah' => $t->jumlah,
            ]);
        }

        // $insentif = $this->db->get('master_insentif')->result();

        // foreach ($insentif as $ins) {
        //     $this->insert([
        //         'id_gaji_detail' => $this->id_gaji_detailOtomatis(),
        //         'id_gaji_header' => $id_gaji_header,
        //         'jenis_komponen' => 'insentif',
        //         'nama_komponen' => $ins->nama_insentif,
        //         'jumlah' => 0,
        //     ]);
        // }

        return true;
    }

    // public function get_potongan_detail($encrypted_id_gaji_header)
    // {
    //     $id_gaji_header = decrypt_url($encrypted_id_gaji_header);

    //     $gaji_pokok = $this->db->select('jumlah')
    //         ->where(['id_gaji_header' => $id_gaji_header, 'jenis_komponen' => 'gapok'])
    //         ->get('gaji_detail')->row()->jumlah ?? 0;

    //     $potongan_master = $this->db->get('master_potongan')->result();

    //     $detail = [];
    //     $total_potongan = 0;

    //     // Potongan master (persen atau nominal)
    //     foreach ($potongan_master as $p) {
    //         $pot = 0;
    //         $dasar_hitung = $gaji_pokok; // default dasar hitung = gaji pokok

    //         if ($p->tipe_potongan == 'persen') {
    //             $pot = $gaji_pokok * ($p->persentase / 100);
    //             if ($p->maksimal && $pot > $p->maksimal) {
    //                 $pot = $p->maksimal;
    //             }
    //         } else {
    //             $pot = $p->persentase; // nominal potongan
    //             $dasar_hitung = $pot;
    //         }

    //         $total_potongan += $pot;

    //         $detail[] = [
    //             'nama_potongan' => $p->nama_potongan,
    //             'persentase' => $p->persentase,
    //             'tipe_potongan' => $p->tipe_potongan,
    //             'jumlah_potongan' => $pot,
    //             'keterangan' => "($p->persentase% dari " . number_format($gaji_pokok, 0, ",", ".") . ($p->maksimal ? ", maksimal " . number_format($p->maksimal, 0, ",", ".") : "") . ")",
    //             'dasar_hitung' => $dasar_hitung,
    //         ];
    //     }

    //     // Hitung PPh Pasal 21
    //     $ptkp_tahunan = 54000000;
    //     $penghasilan_tahunan = $gaji_pokok * 12;
    //     $pkp = max($penghasilan_tahunan - $total_potongan * 12 - $ptkp_tahunan, 0);
    //     $pph_rate = 0.05;
    //     $pph = $pkp * $pph_rate / 12;
    //     $total_potongan += $pph;

    //     $detail[] = [
    //         'nama_potongan' => 'PPh Pasal 21',
    //         'persentase' => $pph_rate * 100,
    //         'tipe_potongan' => 'persen',
    //         'jumlah_potongan' => $pph,
    //         'keterangan' => "Dihitung dari PKP (setelah potongan BPJS dan PTKP)",
    //         'dasar_hitung' => "PKP setelah potongan BPJS dan PTKP"
    //     ];

    //     $potongan_opsional = $this->db
    //         ->select('nama_komponen, jumlah')
    //         ->where(['id_gaji_header' => $id_gaji_header, 'jenis_komponen' => 'potongan_opsional'])
    //         ->get('gaji_detail')->result();

    //     foreach ($potongan_opsional as $pops) {
    //         $total_potongan += $pops->jumlah;

    //         $detail[] = [
    //             'nama_potongan' => $pops->nama_komponen,
    //             'persentase' => '-',
    //             'tipe_potongan' => 'nominal',
    //             'jumlah_potongan' => $pops->jumlah,
    //             'keterangan' => 'Potongan opsional manual',
    //             'dasar_hitung' => $pops->jumlah,
    //         ];
    //     }
    //     return ['total' => $total_potongan, 'detail' => $detail];
    // }

    public function get_potongan_detail($encrypted_id_gaji_header)
    {
        $id_gaji_header = decrypt_url($encrypted_id_gaji_header);

        $gaji_pokok = $this->db->select('jumlah')
            ->where(['id_gaji_header' => $id_gaji_header, 'jenis_komponen' => 'gapok'])
            ->get('gaji_detail')->row()->jumlah ?? 0;

        $potongan_master = $this->db->get('master_potongan')->result();

        $potongan_tetap = [];
        $total_potongan = 0;

        foreach ($potongan_master as $p) {
            $pot = 0;
            $dasar_hitung = $gaji_pokok;

            if ($p->tipe_potongan == 'persen') {
                $pot = $gaji_pokok * ($p->persentase / 100);
                if ($p->maksimal && $pot > $p->maksimal) {
                    $pot = $p->maksimal;
                }
            } else {
                $pot = $p->persentase; // nominal
                $dasar_hitung = $pot;
            }

            $total_potongan += $pot;

            $potongan_tetap[] = [
                'nama_potongan' => $p->nama_potongan,
                'persentase' => $p->persentase,
                'tipe_potongan' => $p->tipe_potongan,
                'jumlah_potongan' => $pot,
                'keterangan' => "($p->persentase% dari " . number_format($gaji_pokok, 0, ",", ".") . ($p->maksimal ? ", maksimal " . number_format($p->maksimal, 0, ",", ".") : "") . ")",
                'dasar_hitung' => $dasar_hitung,
            ];
        }

        // Hitung PPh Pasal 21
        $ptkp_tahunan = 54000000;
        $penghasilan_tahunan = $gaji_pokok * 12;
        $pkp = max($penghasilan_tahunan - $total_potongan * 12 - $ptkp_tahunan, 0);
        $pph_rate = 0.05;
        $pph = $pkp * $pph_rate / 12;
        $total_potongan += $pph;

        $potongan_tetap[] = [
            'nama_potongan' => 'PPh Pasal 21',
            'persentase' => $pph_rate * 100,
            'tipe_potongan' => 'persen',
            'jumlah_potongan' => $pph,
            'keterangan' => "Dihitung dari PKP (setelah potongan BPJS dan PTKP)",
            'dasar_hitung' => "PKP setelah potongan BPJS dan PTKP"
        ];

        // Ambil potongan opsional manual dan tambahkan id_gaji_detail
        $potongan_opsional = $this->db
            ->select('id_gaji_detail, nama_komponen, jumlah')
            ->where(['id_gaji_header' => $id_gaji_header, 'jenis_komponen' => 'potongan_opsional'])
            ->get('gaji_detail')->result();

        $detail_opsional = [];
        foreach ($potongan_opsional as $pops) {
            $total_potongan += $pops->jumlah;

            $detail_opsional[] = [
                'id_gaji_detail' => $pops->id_gaji_detail,  // Tambahkan id_gaji_detail
                'nama_potongan' => $pops->nama_komponen,
                'persentase' => null,
                'tipe_potongan' => 'nominal',
                'jumlah_potongan' => $pops->jumlah,
                'keterangan' => 'Potongan opsional',
                'dasar_hitung' => $pops->jumlah,
            ];
        }

        return [
            'total' => $total_potongan,
            'potongan_tetap' => $potongan_tetap,
            'potongan_opsional' => $detail_opsional
        ];
    }


    public function get_all_by_header($id_gaji_header)
    {
        $this->db->where('id_gaji_header', $id_gaji_header);
        $this->db->where('jenis_komponen !=', 'potongan_opsional');
        return $this->db->get($this->table)->result_array();
    }


    public function insert_potongan_opsional($id_gaji_header, $nama_potongan, $jumlah)
    {
        $data = [
            'id_gaji_detail' => $this->id_gaji_detailOtomatis(),
            'id_gaji_header' => $id_gaji_header,
            'jenis_komponen' => 'potongan_opsional',
            'nama_komponen' => $nama_potongan,
            'jumlah' => $jumlah,
        ];

        return $this->insert($data);
    }
}

    // public function kalkulasi_gaji($id_gaji_header)
    // {
    //     $details = $this->get_datatables($id_gaji_header);

    //     $gaji_pokok = 0;
    //     $tunjangan_tetap = 0;
    //     $insentif = 0;
    //     $potongan_opsional = 0;

    //     $potongan_master = $this->db->get('master_potongan')->result();

    //     $bpjs_rate = 0;
    //     $jht_rate = 0;
    //     $jp_rate = 0;
    //     $jp_maksimal = PHP_INT_MAX;
    //     $pph21_rate = 0.05; // asumsi 5% contoh

    //     foreach ($potongan_master as $p) {
    //         if ($p->nama_potongan == 'BPJS Kesehatan') $bpjs_rate = $p->persentase / 100;
    //         if ($p->nama_potongan == 'Jaminan Hari Tua') $jht_rate = $p->persentase / 100;
    //         if ($p->nama_potongan == 'Jaminan Pensiun') {
    //             $jp_rate = $p->persentase / 100;
    //             $jp_maksimal = $p->maksimal ?: PHP_INT_MAX;
    //         }
    //     }

    //     foreach ($details as $d) {
    //         switch ($d->jenis_komponen) {
    //             case 'gapok':
    //                 $gaji_pokok += $d->jumlah;
    //                 break;
    //             case 'tunjangan_tetap':
    //                 $tunjangan_tetap += $d->jumlah;
    //                 break;
    //             case 'insentif':
    //                 $insentif += $d->jumlah;
    //                 break;
    //             case 'potongan_opsional':
    //                 $potongan_opsional += $d->jumlah;
    //                 break;
    //         }
    //     }

    //     $total_pendapatan_tetap = $gaji_pokok + $tunjangan_tetap;
    //     $total_pendapatan_kotor = $total_pendapatan_tetap + $insentif;

    //     $pot_bpjs = $gaji_pokok * $bpjs_rate;
    //     $pot_jht = $gaji_pokok * $jht_rate;
    //     $pot_jp = min($gaji_pokok * $jp_rate, $jp_maksimal);

    //     // Hitung PPh 21 sederhana
    //     $ptkp_tahunan = 54000000;
    //     $penghasilan_tahunan = $total_pendapatan_kotor * 12;
    //     $pkp = max($penghasilan_tahunan - ($pot_bpjs * 12) - $ptkp_tahunan, 0);
    //     $pot_pph21 = $pkp * $pph21_rate / 12;

    //     $total_potongan = $pot_bpjs + $pot_jht + $pot_jp + $pot_pph21 + $potongan_opsional;
    //     $gaji_bersih = $total_pendapatan_kotor - $total_potongan;


    //     $this->gaji_header->update($id_gaji_header, [
    //         'total_pendapatan_tetap' => $total_pendapatan_tetap,
    //         'total_pendapatan_kotor' => $total_pendapatan_kotor,
    //         'total_potongan' => $total_potongan,
    //         'gaji_bersih' => $gaji_bersih
    //     ]);
    // }
