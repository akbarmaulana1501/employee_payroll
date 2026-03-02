<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai_penggajian extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_pegawai_penggajian', 'gaji');
    }

    public function index()
    {
        $this->data['title'] = 'Pegawai Penggajian';
        $this->data['TombolCreate'] = '
    <h4 class="page-title">
        <button type="button" class="btn btn-sm btn-primary" onclick="add()">
            <i class="fe-plus-square"></i> Create
        </button>
        <button type="button" class="btn btn-sm btn-primary" onclick="reload_table()">
            <i class="fe-refresh-cw"></i> Reload
        </button>
        <button type="button" class="btn btn-sm btn-primary" onclick="generate_penggajian()">
            <i class="fe-settings"></i> Generate Penggajian
        </button>
    </h4>';

        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Pegawai Penggajian';
        $this->data['ml'] = 'Daftar Penggajian Pegawai';
        $this->data['unit_usaha'] = $this->db->get('unit_usaha')->result();
        $this->data['unit_kerja'] = $this->db->get('unit_kerja')->result();
        $this->data['kode'] = $this->db->get('master_tunjangan_kode')->result();
        $this->data['insentif'] = $this->db->get('master_tunjangan_insentif')->result();
        $this->data['list_pegawai'] = $this->db->get('pegawai')->result();
        $this->data['id_pegawai'] = $this->db
            ->select('pegawai.id_pegawai')
            ->from('pegawai')
            ->join('master_gaji_pokok', 'master_gaji_pokok.id_pegawai = pegawai.id_pegawai')
            ->where('master_gaji_pokok.nominal IS NOT NULL') // opsional, hanya yang ada gaji_pokok
            ->get()
            ->result();

        $this->template->load('templates/master', 'admin/master/pegawai_penggajian/list', $this->data);
    }


    // public function generate_penggajian()
    // {
    //     // Ambil dari post
    //     $id_pegawai = $this->input->post('id_pegawai', true);

    //     // Jika yang dikirim string JSON, decode dulu
    //     if (is_string($id_pegawai)) {
    //         $decoded = json_decode($id_pegawai, true);
    //         if (json_last_error() === JSON_ERROR_NONE) {
    //             $id_pegawai = $decoded;
    //         }
    //     }

    //     // Pastikan $id_pegawai adalah array dan tidak kosong
    //     if (empty($id_pegawai) || !is_array($id_pegawai)) {
    //         echo json_encode([
    //             'status'  => false,
    //             'message' => 'Format id_pegawai tidak valid atau kosong'
    //         ]);
    //         return;
    //     }

    //     // Pastikan setiap elemen punya key 'id_pegawai'
    //     $ids = [];
    //     foreach ($id_pegawai as $item) {
    //         if (is_array($item) && isset($item['id_pegawai'])) {
    //             $ids[] = $item['id_pegawai'];
    //         }
    //     }

    //     if (empty($ids)) {
    //         echo json_encode([
    //             'status'  => false,
    //             'message' => 'Tidak ada ID pegawai yang valid'
    //         ]);
    //         return;
    //     }

    //     // Debug
    //     // var_dump($ids); die;

    //     // Kirim ke model
    //     $results = $this->gaji->generate_all_penggajian($ids);

    //     echo json_encode([
    //         'status'  => true,
    //         'message' => 'Proses generate selesai',
    //         'data'    => $results
    //     ]);
    // }

    public function generate_penggajian()
    {
        $id_pegawai_arr = $this->input->post('id_pegawai');
        $id_pegawai = array_column($id_pegawai_arr, 'id_pegawai');

        $data_pegawai = $this->gaji->generate_gaji($id_pegawai);

        $bulan = date('m');
        $tahun = date('Y');

        foreach ($data_pegawai as $pegawai) {
            // Cek apakah data sudah ada
            $cek = $this->db->get_where('pegawai_penggajian', [
                'id_pegawai' => $pegawai->id_pegawai,
                'bulan'      => $bulan,
                'tahun'      => $tahun
            ])->row();

            if ($cek) {
                $messages[] = [
                    'status'  => 'warning',
                    'message' => "⚠️ Data penggajian untuk Pegawai {$pegawai->id_pegawai} bulan {$bulan}-{$tahun} sudah ada!"
                ];
                continue;
            }

            // Ambil semua tunjangan untuk 1 pegawai
            $fungsional = $this->db->get_where('master_tunjangan_fungsional', ['id_eselon' => $pegawai->id_eselon])->row();
            $kesra      = $this->db->get_where('master_tunjangan_kesra', ['status_pegawai' => $pegawai->status_pegawai])->row();
            $komunikasi = $this->db->get_where('master_tunjangan_komunikasi', ['id_eselon' => $pegawai->id_eselon])->row();
            $daerah     = $this->db->get_where('master_tunjangan_daerah', ['status_pegawai' => $pegawai->status_pegawai])->row();
            $perumahan  = $this->db->get_where('master_tunjangan_perumahan', ['id_eselon' => $pegawai->id_eselon])->row();
            $penugasan  = $this->db->get_where('tunjangan_penugasan', ['id_pegawai' => $pegawai->id_pegawai])->row();
            $kasir      = $this->db->get_where('tunjangan_kasir', ['id_pegawai' => $pegawai->id_pegawai])->row();
            $transport  = $this->db->get_where('tunjangan_transport', ['id_pegawai' => $pegawai->id_pegawai])->row();
            $kompensasi = $this->db->get_where('kompensasi', ['id_pegawai' => $pegawai->id_pegawai])->row();
            $gaji_pokok = $this->db->get_where('master_gaji_pokok', ['id_pegawai' => $pegawai->id_pegawai])->row();


            // Susun data untuk insert
            $id_pegawai_penggajian = $this->generate_id();
            $data = [
                'id_pegawai_penggajian'   => $id_pegawai_penggajian,
                'id_pegawai'              => $pegawai->id_pegawai,
                'bulan'                   => $bulan,
                'tahun'                   => $tahun,
                'id_gaji_pokok'           => $gaji_pokok ? $gaji_pokok->id_gaji_pokok : null,
                'id_tunjangan_fungsional' => $fungsional ? $fungsional->id_tunjangan : null,
                'id_tunjangan_kesra'      => $kesra ? $kesra->id_tunjangan : null,
                'id_tunjangan_komunikasi' => $komunikasi ? $komunikasi->id_tunjangan : null,
                'id_tunjangan_daerah'     => $daerah ? $daerah->id_tunjangan : null,
                'id_tunjangan_perumahan'  => $perumahan ? $perumahan->id_tunjangan : null,
                'id_tunjangan_penugasan'  => $penugasan ? $penugasan->id_tunjangan_penugasan : null,
                'id_tunjangan_kasir'      => $kasir ? $kasir->id_tunjangan_kasir : null,
                'id_tunjangan_transport'  => $transport ? $transport->id_tunjangan_transport : null,
                'id_kompensasi'           => $kompensasi ? $kompensasi->id_kompensasi : null,
            ];

            // Insert data baru
            $this->gaji->insert($data);
            // echo "✅ Data penggajian untuk Pegawai {$pegawai->id_pegawai} berhasil ditambahkan.<br>";


            $insentif = $this->db
                ->select('pegawai_insentif.*, pegawai_penggajian.id_pegawai_penggajian, master_tunjangan_insentif.id_tunjangan')
                ->from('pegawai_insentif')
                ->join('pegawai_penggajian', 'pegawai_penggajian.id_pegawai = pegawai_insentif.id_pegawai', 'left')
                ->join('master_tunjangan_insentif', 'master_tunjangan_insentif.id_tunjangan = pegawai_insentif.id_tunjangan', 'left')
                ->where('pegawai_insentif.id_pegawai', $pegawai->id_pegawai)
                ->get()
                ->row();

            if ($insentif && $insentif->id_pegawai_penggajian && $insentif->id_tunjangan) {
                $data_insentif = [
                    'id_pegawai_penggajian_insentif' => $this->generate_id_penggajian_insentif(),
                    'id_pegawai_penggajian'          => $insentif->id_pegawai_penggajian,
                    'id_tunjangan_insentif'          => $insentif->id_tunjangan, // foreign key valid
                    'nominal'                        => (float) $insentif->nominal
                ];
                $this->gaji->insert_insentif_batch2($data_insentif);
            }

            $kode = $this->db
                ->select('pegawai_kode.*, pegawai_penggajian.id_pegawai_penggajian, master_tunjangan_kode.id_master')
                ->from('pegawai_kode')
                ->join('pegawai_penggajian', 'pegawai_penggajian.id_pegawai = pegawai_kode.id_pegawai', 'left')
                ->join('master_tunjangan_kode', 'master_tunjangan_kode.id_master = pegawai_kode.id_master', 'left')
                ->where('pegawai_kode.id_pegawai', $pegawai->id_pegawai)
                ->get()
                ->row();

            if ($kode && $kode->id_pegawai_penggajian && $kode->id_master) {
                $data_kode = [
                    'id_pegawai_penggajian_kode' => $this->generate_id_penggajian_kode(),
                    'id_pegawai_penggajian'      => $kode->id_pegawai_penggajian,
                    'id_tunjangan_kode'          => $kode->id_master, // foreign key valid
                    'nominal'                    => (float) $kode->nominal
                ];
                $this->gaji->insert_kode_batch2($data_kode);
            }

            $rincian_potongan = $this->db->select('id_potongan,tipe_potongan,persentase')
                ->from('master_potongan')
                ->get()
                ->result();

            $data_potongan = [];
            foreach ($rincian_potongan as $potongan) {
                $data_potongan[] = [
                    'id_pegawai_potongan'   => $this->generate_id_pegawai_potongan(),
                    'id_pegawai_penggajian' => $id_pegawai_penggajian,
                    'id_potongan'           => $potongan->id_potongan,
                    // kalau ingin pakai persentase untuk hitung nominal:
                    'nominal'               => $this->hitung_nominal_potongan($pegawai, $potongan)
                ];
            }


            $this->gaji->insert_potongan_batch2($data_potongan);

            $messages[] = [
                'status'  => 'success',
                'message' => "✅ Data penggajian untuk Pegawai {$pegawai->id_pegawai} berhasil ditambahkan."
            ];
        }
        echo json_encode($messages);
    }

    private function hitung_nominal_potongan($pegawai, $potongan, $total_penghasilan = null)
    {
        $gaji_pokok = $this->db->select('nominal')
            ->from('master_gaji_pokok')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->get()
            ->row();

        if($potongan->tipe_potongan == "fixed"){
            return $potongan->persentase;
        }else if($potongan->tipe_potongan == "persen"){
            return ($potongan->persentase/100) * $gaji_pokok->nominal;
        }
    }


    private function generate_id_penggajian_insentif()
    {
        // ambil ID terakhir
        $this->db->select('id_pegawai_penggajian_insentif')
            ->from('pegawai_penggajian_insentif')
            ->order_by('id_pegawai_penggajian_insentif', 'DESC')
            ->limit(1);
        $query = $this->db->get();
        $last_id = $query->row();

        if ($last_id) {
            // ambil angka dari format PPIxxx
            $last_number = intval(substr($last_id->id_pegawai_penggajian_insentif, 3));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1; // kalau belum ada data
        }

        // format jadi PPI001, PPI002, dst
        return 'PPI' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    }

    private function generate_id_penggajian_kode()
    {
        // ambil ID terakhir
        $this->db->select('id_pegawai_penggajian_kode')
            ->from('pegawai_penggajian_kode')
            ->order_by('id_pegawai_penggajian_kode', 'DESC')
            ->limit(1);
        $query = $this->db->get();
        $last_id = $query->row();

        if ($last_id) {
            // ambil angka dari format PPIxxx
            $last_number = intval(substr($last_id->id_pegawai_penggajian_kode, 3));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1; // kalau belum ada data
        }

        // format jadi PPI001, PPI002, dst
        return 'PPK' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    }

    private function generate_id_pegawai_potongan()
    {
        // ambil ID terakhir
        $this->db->select('id_pegawai_potongan')
            ->from('pegawai_potongan')
            ->order_by('id_pegawai_potongan', 'DESC')
            ->limit(1);
        $query = $this->db->get();
        $last_id = $query->row();

        if ($last_id) {
            // ambil angka dari format PPIxxx
            $last_number = intval(substr($last_id->id_pegawai_potongan, 3));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1; // kalau belum ada data
        }

        // format jadi PPI001, PPI002, dst
        return 'PTG' . str_pad($new_number, 3, '0', STR_PAD_LEFT);
    }


    public function ajax_list()
    {
        $list = $this->gaji->get_datatables();
        $list2 = $this->gaji->get_data_penggajian2();
        // var_dump($list2);
        // die;
        $data = [];

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $no = $_POST['start'] ?? 0;
        foreach ($list as $item) {
            $no++;
            $row = [];

            $row[] = $no;

            $row[] = '
<div class="dropdown">
  <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
    <i class="fas fa-cogs"></i> Aksi
  </button>
  <ul class="dropdown-menu">
    <li>
      <a class="dropdown-item text-info" href="javascript:void(0)" onclick="update(\'' . encrypt_url($item->id_pegawai_penggajian) . '\')">
        <i class="fas fa-edit"></i> Update
      </a>
    </li>
    <li>
      <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deletedata(\'' . encrypt_url($item->id_pegawai_penggajian) . '\')">
        <i class="fas fa-trash"></i> Delete
      </a>
    </li>
    <li>
      <a class="dropdown-item text-success" href="javascript:void(0)" onclick="kirim_email(\'' . encrypt_url($item->id_pegawai_penggajian) . '\')">
        <i class="fas fa-envelope"></i> Kirim Slip
      </a>
    </li>'
                . (!empty($item->nominal_thr) ? '
    <li>
      <a class="dropdown-item text-warning" style="color:#c77700;" href="javascript:void(0)" onclick="kirim_slip_thr(\'' . encrypt_url($item->id_pegawai_penggajian) . '\')">
        <i class="fas fa-gift"></i> Kirim Slip THR
      </a>
    </li>' : '')
                . (!empty($item->nominal_bonus) ? '
    <li>
      <a class="dropdown-item text-warning" style="color:#c77700;" href="javascript:void(0)" onclick="kirim_slip_bonus(\'' . encrypt_url($item->id_pegawai_penggajian) . '\')">
        <i class="fas fa-gift"></i> Kirim Slip Bonus
      </a>
    </li>' : '') .
                '</ul>
</div>
';


            $row[] = htmlspecialchars($item->nama);


            $row[] = isset($bulanNama[$item->bulan]) ? $bulanNama[$item->bulan] : $item->bulan;
            $row[] = $item->tahun;  // kolom tahun

            $gaji_pokok   = (float) $item->gaji_pokok;
            $fungsional   = (float) $item->fungsional;
            $kesra        = (float) $item->kesra;
            $komunikasi   = (float) $item->komunikasi;
            $daerah       = (float) $item->daerah;
            $radiasi      = (float) $item->radiasi;
            $perumahan    = (float) $item->perumahan;
            $kode         = (float) $item->jml_tunjangan_kode;
            $insentif     = (float) $item->jml_tunjangan_insentif;
            $penugasan = (float) $item->nominal_penugasan;
            $kasir = (float) $item->nominal_kasir;
            $transport = (float) $item->nominal_transport;
            $kompensasi = (float) $item->nominal_kompensasi;

            $total_pendapatan = $gaji_pokok + $fungsional + $kesra + $komunikasi + $daerah + $radiasi + $perumahan + $kode + $insentif + $penugasan + $kasir + $transport + $kompensasi;

            $potongan_transaksi = $this->gaji->get_potongan_by_penggajian($item->id_pegawai_penggajian);
            $total_potongan = 0;
            $potongan_rincian = [];
            foreach ($potongan_transaksi as $ptg) {
                $total_potongan += $ptg->nominal;
                $potongan_rincian[$ptg->nama_potongan] = $ptg->nominal;
            }

            $gaji_bersih = $total_pendapatan - $total_potongan;

            $row[] = '
            <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
                data-nama="' . htmlspecialchars($item->nama) . '"
                data-gaji_pokok="' . $gaji_pokok . '"
                data-fungsional="' . $fungsional . '"
                data-kesra="' . $kesra . '"
                data-komunikasi="' . $komunikasi . '"
                data-daerah="' . $daerah . '"
                data-radiasi="' . $radiasi . '"
                data-perumahan="' . $perumahan . '"
                data-kode="' . $kode . '"
                data-insentif="' . $insentif . '"
                data-penugasan="' . $penugasan . '"
                data-kasir="' . $kasir . '"
                data-transport="' . $transport . '"
                data-kompensasi="' . $kompensasi . '"
                data-total="' . $total_pendapatan . '"
                data-type="pendapatan"
                title="Lihat rincian pendapatan"
            >
                <i class="fas fa-list-ul mr-1 text-primary"></i>
                <span class="text-dark font-weight-medium">Rp. ' . number_format($total_pendapatan, 0, ',', '.') . '</span>
            </span>
        ';

            $row[] = '
            <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
                data-nama="' . htmlspecialchars($item->nama) . '"
                data-potongan=\'' . json_encode($potongan_rincian) . '\'
                data-total="' . $total_potongan . '"
                data-type="potongan"
                title="Lihat rincian potongan"
            >
                <i class="fas fa-list-ul mr-1 text-danger"></i>
                <span class="text-dark font-weight-medium">Rp. ' . number_format($total_potongan, 0, ',', '.') . '</span>
            </span>
        ';

            $row[] = '<strong>Rp. ' . number_format($total_pendapatan, 0, ',', '.') . '</strong>';

            $row[] = '<strong>Rp. ' . number_format($gaji_bersih, 0, ',', '.') . '</strong>';

            $row[] = ($item->status_konfirmasi == 1)
                ? '<span class="badge bg-success">Terkonfirmasi</span>'
                : '<span class="badge bg-warning">Belum</span>';


            $data[] = $row;
        }

        $output = [
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => $this->gaji->count_all(),
            "recordsFiltered" => $this->gaji->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    public function terkonfirmasi()
    {
        $this->data['title'] = 'Pegawai Penggajian Terkonfirmasi';
        $tahunNow = date('Y');
        $dropdownTahun = '
            <div class="btn-group me-1 mr-1 ml-1">
            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fe-calendar"></i> Tahun
            </button>
            <ul class="dropdown-menu" id="dropdown-tahun-list">
                <li><a class="dropdown-item filter-tahun-option" data-value="">Semua Tahun</a></li>';
        for ($i = $tahunNow; $i >= $tahunNow - 4; $i--) {
            $dropdownTahun .= '<li><a class="dropdown-item filter-tahun-option" data-value="' . $i . '">' . $i . '</a></li>';
        }
        $dropdownTahun .= '
            </ul>
            </div>
            <input type="hidden" id="filter-tahun" value="" />
            ';

        $dropdownBulan = '
            <div class="btn-group me-1 mr-1">
            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fe-clock"></i> Bulan
            </button>
            <ul class="dropdown-menu" id="dropdown-bulan-list">
                <li><a class="dropdown-item filter-bulan-option" data-value="">Semua Bulan</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="1">Januari</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="2">Februari</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="3">Maret</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="4">April</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="5">Mei</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="6">Juni</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="7">Juli</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="8">Agustus</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="9">September</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="10">Oktober</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="11">November</a></li>
                <li><a class="dropdown-item filter-bulan-option" data-value="12">Desember</a></li>
            </ul>
            </div>
            <input type="hidden" id="filter-bulan" value="" />
            ';

        $this->data['TombolCreate'] = '
        <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary me-1" onclick="reload_table_terkonfirmasi()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
                ' . $dropdownTahun . $dropdownBulan . '
                <button type="button" class="btn btn-sm btn-danger me-1" onclick="print_pdf()">
                    <i class="fe-file-text"></i> PDF
                </button>
                <button type="button" class="btn btn-sm btn-success me-1" onclick="print_excel()">
                    <i class="fe-file-text"></i> Excel
                </button>
            </h4>
            
        ';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Pegawai Penggajian Terkonfirmasi';
        $this->data['ml'] = 'Daftar Penggajian Pegawai Terkonfirmasi';
        $this->data['unit_usaha'] = $this->db->get('unit_usaha')->result();
        $this->data['unit_kerja'] = $this->db->get('unit_kerja')->result();
        $this->data['kode'] = $this->db->get('master_tunjangan_kode')->result();
        $this->data['insentif'] = $this->db->get('master_tunjangan_insentif')->result();
        $this->data['list_pegawai'] = $this->db->get('pegawai')->result();
        $this->data['id_pegawai'] = $this->db->select('id_pegawai')->limit(10)->get('pegawai')->result();

        $this->template->load('templates/master', 'admin/master/pegawai_penggajian/list_terkonfirmasi', $this->data);
    }

    public function get_terkonfirmasi_excel()
    {
        // Ambil semua pegawai penggajian terkonfirmasi
        $pegawaiList = $this->gaji->get_all_pegawai_terkonfirmasi();
        // var_dump($pegawaiList);
        // die;

        $pegawaiMap = [];

        foreach ($pegawaiList as $item) {
            $id = $item->id_pegawai_penggajian;

            // Inisialisasi pegawai
            if (!isset($pegawaiMap[$id])) {
                $pegawaiMap[$id] = [
                    'nama' => $item->nama,
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'pendapatan_rincian' => [],
                    'potongan_rincian' => [],
                    'total_pendapatan' => 0,
                    'total_potongan' => 0,
                    'gaji_kotor' => 0,
                    'gaji_bersih' => 0
                ];
            }

            // Ambil rincian pendapatan pegawai
            $pendapatan = $this->gaji->get_pendapatan_terkonfirmasi($id);
            $total_pendapatan = 0;
            $pendapatan_rincian = [];
            foreach ($pendapatan as $p) {
                $total_pendapatan += (float)$p->nominal;
                $pendapatan_rincian[$p->nama_pendapatan] = (float)$p->nominal;
            }

            // Ambil rincian potongan pegawai
            $potongan = $this->gaji->get_potongan_terkonfirmasi($id);
            $total_potongan = 0;
            $potongan_rincian = [];
            foreach ($potongan as $ptg) {
                $total_potongan += (float)$ptg->nominal;
                $potongan_rincian[$ptg->nama_potongan] = (float)$ptg->nominal;
            }

            // Simpan hasil per pegawai
            $pegawaiMap[$id]['pendapatan_rincian'] = $pendapatan_rincian;
            $pegawaiMap[$id]['potongan_rincian'] = $potongan_rincian;
            $pegawaiMap[$id]['total_pendapatan'] = $total_pendapatan;
            $pegawaiMap[$id]['total_potongan'] = $total_potongan;

            // Hitung gaji kotor & bersih
            $pegawaiMap[$id]['gaji_kotor'] = $total_pendapatan;
            $pegawaiMap[$id]['gaji_bersih'] = $total_pendapatan - $total_potongan;
        }

        // Konversi menjadi array numerik
        $result = array_values($pegawaiMap);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }








    // public function ajax_list_terkonfirmasi()
    // {
    //     $tahun = $this->input->post('tahun');
    //     $bulan = $this->input->post('bulan');
    //     $start_date = $this->input->post('start_month');
    //     $end_date   = $this->input->post('end_month');

    //     $list = $this->gaji->get_datatables(1, $tahun, $bulan, $start_date, $end_date);
    //     $data = [];

    //     $bulanNama = [
    //         1 => 'Januari',
    //         2 => 'Februari',
    //         3 => 'Maret',
    //         4 => 'April',
    //         5 => 'Mei',
    //         6 => 'Juni',
    //         7 => 'Juli',
    //         8 => 'Agustus',
    //         9 => 'September',
    //         10 => 'Oktober',
    //         11 => 'November',
    //         12 => 'Desember',
    //     ];

    //     $no = $_POST['start'] ?? 0;
    //     foreach ($list as $item) {
    //         $no++;
    //         $row = [];
    //         $row[] = $no;
    //         $row[] = htmlspecialchars($item->nama);
    //         $row[] = isset($bulanNama[$item->bulan]) ? $bulanNama[$item->bulan] : $item->bulan;
    //         $row[] = $item->tahun;

    //         $gaji_pokok   = (float) $item->gaji_pokok;
    //         $fungsional   = (float) $item->fungsional;
    //         $kesra        = (float) $item->kesra;
    //         $komunikasi   = (float) $item->komunikasi;
    //         $daerah       = (float) $item->daerah;
    //         $radiasi      = (float) $item->radiasi;
    //         $perumahan    = (float) $item->perumahan;
    //         $kode         = (float) $item->jml_tunjangan_kode;
    //         $insentif     = (float) $item->jml_tunjangan_insentif;
    //         $penugasan = (float) $item->nominal_penugasan;
    //         $kasir = (float) $item->nominal_kasir;
    //         $transport = (float) $item->nominal_transport;
    //         $kompensasi = (float) $item->nominal_kompensasi;

    //         $total_pendapatan = $gaji_pokok + $fungsional + $kesra + $komunikasi + $daerah + $radiasi + $perumahan + $kode + $insentif + $penugasan + $kasir + $transport + $kompensasi;
    //         $potongan_transaksi = $this->gaji->get_potongan_by_penggajian($item->id_pegawai_penggajian);
    //         $total_potongan = 0;
    //         $potongan_rincian = [];
    //         foreach ($potongan_transaksi as $ptg) {
    //             $total_potongan += $ptg->nominal;
    //             $potongan_rincian[$ptg->nama_potongan] = $ptg->nominal;
    //         }

    //         $gaji_bersih = $total_pendapatan - $total_potongan;

    //         $row[] = '
    //         <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
    //             data-nama="' . htmlspecialchars($item->nama) . '"
    //             data-gaji_pokok="' . $gaji_pokok . '"
    //             data-fungsional="' . $fungsional . '"
    //             data-kesra="' . $kesra . '"
    //             data-komunikasi="' . $komunikasi . '"
    //             data-daerah="' . $daerah . '"
    //             data-radiasi="' . $radiasi . '"
    //             data-perumahan="' . $perumahan . '"
    //             data-kode="' . $kode . '"
    //             data-insentif="' . $insentif . '"
    //             data-penugasan="' . $penugasan . '"
    //             data-kasir="' . $kasir . '"
    //             data-transport="' . $transport . '"
    //             data-kompensasi="' . $kompensasi . '"
    //             data-total="' . $total_pendapatan . '"
    //             data-type="pendapatan"
    //             title="Lihat rincian pendapatan"
    //         >
    //             <i class="fas fa-list-ul mr-1 text-primary"></i>
    //             <span class="text-dark font-weight-medium">Rp. ' . number_format($total_pendapatan, 0, ',', '.') . '</span>
    //         </span>
    //     ';

    //         $row[] = '
    //         <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
    //             data-nama="' . htmlspecialchars($item->nama) . '"
    //             data-potongan=\'' . json_encode($potongan_rincian) . '\'
    //             data-total="' . $total_potongan . '"
    //             data-type="potongan"
    //             title="Lihat rincian potongan"
    //         >
    //             <i class="fas fa-list-ul mr-1 text-danger"></i>
    //             <span class="text-dark font-weight-medium">Rp. ' . number_format($total_potongan, 0, ',', '.') . '</span>
    //         </span>
    //     ';

    //         $row[] = '<strong>Rp. ' . number_format($total_pendapatan, 0, ',', '.') . '</strong>';
    //         $row[] = '<strong>Rp. ' . number_format($gaji_bersih, 0, ',', '.') . '</strong>';
    //         $row[] = '<span class="badge bg-success">Terkonfirmasi</span>';
    //         $data[] = $row;
    //     }

    //     $output = [
    //         "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    //         "recordsTotal" => $this->gaji->count_all(1),
    //         "recordsFiltered" => $this->gaji->count_filtered(1),
    //         "data" => $data,
    //     ];
    //     echo json_encode($output);
    // }

    public function ajax_list_terkonfirmasi()
    {
        $tahun = $this->input->post('tahun');
        $bulan = $this->input->post('bulan');
        $start_date = $this->input->post('start_month');
        $end_date   = $this->input->post('end_month');

        // var_dump($start_date);
        // die;

        // Ambil list dari tabel pendapatan_terkonfirmasi
        $list = $this->gaji->get_datatables_terkonfirmasi($tahun, $bulan, $start_date, $end_date);
        $data = [];

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $no = $_POST['start'] ?? 0;
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = htmlspecialchars($item->nama_pegawai);
            // Jika bulan di DB disimpan sebagai nama (November) langsung tampilkan,
            // jika angka ubah ke nama
            $row[] = isset($bulanNama[$item->bulan]) ? $bulanNama[$item->bulan] : $item->bulan;
            $row[] = $item->tahun;

            // Hitung total pendapatan
            $pendapatan = $this->gaji->get_pendapatan_terkonfirmasi($item->id_pegawai_penggajian);
            $total_pendapatan = 0;
            $pendapatan_rincian = [];
            foreach ($pendapatan as $p) {
                $total_pendapatan += $p->nominal;
                $pendapatan_rincian[ucwords(strtolower($p->nama_pendapatan))] = $p->nominal;
            }

            // Hitung total potongan
            $potongan = $this->gaji->get_potongan_terkonfirmasi($item->id_pegawai_penggajian);
            $total_potongan = 0;
            $potongan_rincian = [];
            foreach ($potongan as $ptg) {
                $total_potongan += $ptg->nominal;
                $potongan_rincian[$ptg->nama_potongan] = $ptg->nominal;
            }

            $gaji_bersih = $total_pendapatan - $total_potongan;

            // var_dump($pendapatan_rincian);
            // die;


            $row[] = '
        <span class="detail-trigger"
            data-nama="' . htmlspecialchars($item->nama_pegawai) . '"
            data-pendapatan=\'' . json_encode($pendapatan_rincian) . '\'
            data-total="' . $total_pendapatan . '"
            data-type="pendapatan"
        >
            <i class="fas fa-list-ul text-primary"></i>
            <span>Rp. ' . number_format($total_pendapatan, 0, ',', '.') . '</span>
        </span>';

            $row[] = '
        <span class="detail-trigger"
            data-nama="' . htmlspecialchars($item->nama_pegawai) . '"
            data-potongan=\'' . json_encode($potongan_rincian) . '\'
            data-total="' . $total_potongan . '"
            data-type="potongan"
        >
            <i class="fas fa-list-ul text-danger"></i>
            <span>Rp. ' . number_format($total_potongan, 0, ',', '.') . '</span>
        </span>';

            $row[] = '<strong>Rp. ' . number_format($total_pendapatan, 0, ',', '.') . '</strong>';
            $row[] = '<strong>Rp. ' . number_format($gaji_bersih, 0, ',', '.') . '</strong>';
            $row[] = '<span class="badge bg-success">Terkonfirmasi</span>';

            $data[] = $row;
        }

        $output = [
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => count($list),
            "recordsFiltered" => count($list),
            "data" => $data,
        ];
        echo json_encode($output);
    }


    public function ajax_list_thr()
    {
        $tahun = $this->input->post('tahun');
        $bulan = $this->input->post('bulan');
        $start_date = $this->input->post('start_month');
        $end_date   = $this->input->post('end_month');



        $list = $this->gaji->get_datatables(1, $tahun, $bulan, $start_date, $end_date);
        // $data_bonus = $this->gaji->bonus();
        // $data_thr = $this->gaji->thr();
        $data = [];

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $no = $_POST['start'] ?? 0;
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = htmlspecialchars($item->nama);
            $row[] = isset($bulanNama[$item->bulan]) ? $bulanNama[$item->bulan] : $item->bulan;
            $row[] = $item->tahun;

            // $bonus   = (float) $item->nominal_bonus;
            $thr = (float) $item->nominal_thr;
            // $fungsional   = (float) $item->fungsional;
            // $kesra        = (float) $item->kesra;
            // $komunikasi   = (float) $item->komunikasi;
            // $daerah       = (float) $item->daerah;
            // $radiasi      = (float) $item->radiasi;
            // $perumahan    = (float) $item->perumahan;
            // $kode         = (float) $item->jml_tunjangan_kode;
            // $insentif     = (float) $item->jml_tunjangan_insentif;

            // $total_pendapatan = $gaji_pokok + $fungsional + $kesra + $komunikasi + $daerah + $radiasi + $perumahan + $kode + $insentif;
            // $potongan_transaksi = $this->gaji->get_potongan_by_penggajian($item->id_pegawai_penggajian);
            // $total_potongan = 0;
            // $potongan_rincian = [];
            // foreach ($potongan_transaksi as $ptg) {
            //     $total_potongan += $ptg->nominal;
            //     $potongan_rincian[$ptg->nama_potongan] = $ptg->nominal;
            // }

            // $gaji_bersih = $total_pendapatan - $total_potongan;

            //     $row[] = '
            //     <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
            //         data-nama="' . htmlspecialchars($item->nama) . '"
            //         data-bonus="' . $bonus . '"
            //         data-type="pendapatan"
            //         title="Lihat rincian Bonus"
            //     >
            //         <i class="fas fa-list-ul mr-1 text-primary"></i>
            //         <span class="text-dark font-weight-medium">Rp. ' . number_format($bonus, 0, ',', '.') . '</span>
            //     </span>
            // ';

            //     $row[] = '
            //     <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
            //         data-nama="' . htmlspecialchars($item->nama) . '"
            //         data-thr=\'' . json_encode($thr) . '\'
            //         data-type="potongan"
            //         title="Lihat rincian Tunjangan Hari Raya"
            //     >
            //         <i class="fas fa-list-ul mr-1 text-danger"></i>
            //         <span class="text-dark font-weight-medium">Rp. ' . number_format($thr, 0, ',', '.') . '</span>
            //     </span>
            // ';

            // $row[] = '<strong>Rp. ' . number_format($bonus, 0, ',', '.') . '</strong>';
            $row[] = '<strong>Rp. ' . number_format($thr, 0, ',', '.') . '</strong>';
            $row[] = '<span class="badge bg-success">Terkonfirmasi</span>';
            $data[] = $row;
        }

        $output = [
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => $this->gaji->count_all(1),
            "recordsFiltered" => $this->gaji->count_filtered(1),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    public function ajax_list_bonus()
    {
        $tahun = $this->input->post('tahun');
        $bulan = $this->input->post('bulan');
        $start_date = $this->input->post('start_month');
        $end_date   = $this->input->post('end_month');

        $list = $this->gaji->get_datatables(1, $tahun, $bulan, $start_date, $end_date);
        // $data_bonus = $this->gaji->bonus();
        // $data_thr = $this->gaji->thr();
        $data = [];

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $no = $_POST['start'] ?? 0;
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = htmlspecialchars($item->nama);
            $row[] = isset($bulanNama[$item->bulan]) ? $bulanNama[$item->bulan] : $item->bulan;
            $row[] = $item->tahun;

            $bonus   = (float) $item->nominal_bonus;
            // $thr = (float) $item->nominal_thr;
            // $fungsional   = (float) $item->fungsional;
            // $kesra        = (float) $item->kesra;
            // $komunikasi   = (float) $item->komunikasi;
            // $daerah       = (float) $item->daerah;
            // $radiasi      = (float) $item->radiasi;
            // $perumahan    = (float) $item->perumahan;
            // $kode         = (float) $item->jml_tunjangan_kode;
            // $insentif     = (float) $item->jml_tunjangan_insentif;

            // $total_pendapatan = $gaji_pokok + $fungsional + $kesra + $komunikasi + $daerah + $radiasi + $perumahan + $kode + $insentif;
            // $potongan_transaksi = $this->gaji->get_potongan_by_penggajian($item->id_pegawai_penggajian);
            // $total_potongan = 0;
            // $potongan_rincian = [];
            // foreach ($potongan_transaksi as $ptg) {
            //     $total_potongan += $ptg->nominal;
            //     $potongan_rincian[$ptg->nama_potongan] = $ptg->nominal;
            // }

            // $gaji_bersih = $total_pendapatan - $total_potongan;

            //     $row[] = '
            //     <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
            //         data-nama="' . htmlspecialchars($item->nama) . '"
            //         data-bonus="' . $bonus . '"
            //         data-type="pendapatan"
            //         title="Lihat rincian Bonus"
            //     >
            //         <i class="fas fa-list-ul mr-1 text-primary"></i>
            //         <span class="text-dark font-weight-medium">Rp. ' . number_format($bonus, 0, ',', '.') . '</span>
            //     </span>
            // ';

            //     $row[] = '
            //     <span class="detail-trigger" style="cursor:pointer;display:inline-flex;align-items:center;"
            //         data-nama="' . htmlspecialchars($item->nama) . '"
            //         data-thr=\'' . json_encode($thr) . '\'
            //         data-type="potongan"
            //         title="Lihat rincian Tunjangan Hari Raya"
            //     >
            //         <i class="fas fa-list-ul mr-1 text-danger"></i>
            //         <span class="text-dark font-weight-medium">Rp. ' . number_format($thr, 0, ',', '.') . '</span>
            //     </span>
            // ';

            $row[] = '<strong>Rp. ' . number_format($bonus, 0, ',', '.') . '</strong>';
            // $row[] = '<strong>Rp. ' . number_format($thr, 0, ',', '.') . '</strong>';
            $row[] = '<span class="badge bg-success">Terkonfirmasi</span>';
            $data[] = $row;
        }

        $output = [
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => $this->gaji->count_all(1),
            "recordsFiltered" => $this->gaji->count_filtered(1),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    // public function ajax_list_terkonfirmasi()
    // {
    //     $tahun      = $this->input->post('tahun');
    //     $bulan      = $this->input->post('bulan');
    //     $start_date = $this->input->post('start_month');
    //     $end_date   = $this->input->post('end_month');

    //     $list = $this->gaji->get_datatables(1, $tahun, $bulan, $start_date, $end_date);

    //     $data = [];
    //     $bulanNama = [
    //         1 => 'Januari',
    //         2 => 'Februari',
    //         3 => 'Maret',
    //         4 => 'April',
    //         5 => 'Mei',
    //         6 => 'Juni',
    //         7 => 'Juli',
    //         8 => 'Agustus',
    //         9 => 'September',
    //         10 => 'Oktober',
    //         11 => 'November',
    //         12 => 'Desember',
    //     ];

    //     $no = $_POST['start'] ?? 0;
    //     foreach ($list as $item) {
    //         $no++;
    //         $row = [];
    //         $row[] = $no;
    //         $row[] = htmlspecialchars($item->nama);
    //         $row[] = isset($bulanNama[$item->bulan]) ? $bulanNama[$item->bulan] : $item->bulan;
    //         $row[] = $item->tahun;

    //         // Hitung pendapatan & potongan
    //         $gaji_pokok   = (float) $item->gaji_pokok;
    //         $fungsional   = (float) $item->fungsional;
    //         $kesra        = (float) $item->kesra;
    //         $komunikasi   = (float) $item->komunikasi;
    //         $daerah       = (float) $item->daerah;
    //         $radiasi      = (float) $item->radiasi;
    //         $perumahan    = (float) $item->perumahan;
    //         $kode         = (float) $item->jml_tunjangan_kode;
    //         $insentif     = (float) $item->jml_tunjangan_insentif;

    //         $total_pendapatan = $gaji_pokok + $fungsional + $kesra + $komunikasi + $daerah + $radiasi + $perumahan + $kode + $insentif;
    //         $potongan_transaksi = $this->gaji->get_potongan_by_penggajian($item->id_pegawai_penggajian);

    //         $total_potongan = 0;
    //         $potongan_rincian = [];
    //         foreach ($potongan_transaksi as $ptg) {
    //             $total_potongan += $ptg->nominal;
    //             $potongan_rincian[$ptg->nama_potongan] = $ptg->nominal;
    //         }

    //         $gaji_bersih = $total_pendapatan - $total_potongan;

    //         $row[] = number_format($total_pendapatan, 0, ',', '.');
    //         $row[] = number_format($total_potongan, 0, ',', '.');
    //         $row[] = number_format($gaji_bersih, 0, ',', '.');
    //         $row[] = '<span class="badge bg-success">Terkonfirmasi</span>';

    //         $data[] = $row;
    //     }

    //     $output = [
    //         "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    //         "recordsTotal" => $this->gaji->count_all(1, $tahun, $bulan, $start_date, $end_date),
    //         "recordsFiltered" => $this->gaji->count_filtered(1, $tahun, $bulan, $start_date, $end_date),
    //         "data" => $data,
    //     ];
    //     echo json_encode($output);
    // }



    public function page()
    {
        $this->data['title'] = 'Pegawai Penggajian';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Pegawai Penggajian';
        $this->data['ml'] = 'Daftar Penggajian Pegawai';
        $this->data['unit_usaha'] = $this->db->get('unit_usaha')->result();
        $this->data['unit_kerja'] = $this->db->get('unit_kerja')->result();
        $this->data['kode'] = $this->db->get('master_tunjangan_kode')->result();
        $this->data['insentif'] = $this->db->get('master_tunjangan_insentif')->result();
        $this->data['list_pegawai'] = $this->db->get('pegawai')->result();

        $this->template->load('templates/master', 'admin/master/pegawai_penggajian/page', $this->data);
    }

    public function generate_pdf()
    {
        require_once APPPATH . 'third_party/vendor/autoload.php';


        $mpdf = new \Mpdf\Mpdf();

        $html = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            .container { width: 95%; margin: 0 auto; }
            .header { display: flex; justify-content: space-between; align-items: flex-start; }
            .logo { width: 150px; }
            .title { text-align: center; font-size: 22px; font-weight: bold; margin: 20px 0 10px 0; letter-spacing: 1px;}
            .content { display: flex; justify-content: space-between; margin-top: 20px;}
            .left, .center, .right { width: 32%; }
            .left, .center { font-size: 13px; }
            .right { font-size: 12px; }
            .quote { margin-top: 45px; text-align: center; font-style: italic; }
            .highlight { font-weight: bold; }
            table { width: 100%; border-collapse: collapse;}
            td { vertical-align: top; padding-bottom: 4px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img class="logo" src="https://i.ibb.co/gMTdkhx/logo-ihc.png" alt="IHC Logo">
            </div>
            <div class="title">SLIP GAJI</div>
            <div class="content">
                <div class="left">
                    <table>
                        <tr><td class="highlight">PENDAPATAN (Rp)</td></tr>
                        <tr><td>GAJI POKOK</td></tr>
                        <tr><td>TUNJANGAN KESRA</td></tr>
                        <tr><td>TUNJANGAN JABATAN</td></tr>
                        <tr><td>TUNJANGAN KOMUNIKASI</td></tr>
                        <tr><td>BPJSTK (PERUSAHAAN)</td></tr>
                        <tr><td>BPJSKES (PERUSAHAAN)</td></tr>
                        <tr><td>DPLK</td></tr>
                        <tr><td>PPH PSL 21</td></tr>
                        <tr><td class="highlight">TOTAL PENDAPATAN</td></tr>
                    </table>
                </div>
                <div class="center">
                    <table>
                        <tr><td class="highlight">POTONGAN WAJIB (A)</td></tr>
                        <tr><td>IURAN BPJSTK (KARY)</td></tr>
                        <tr><td>BPJSTK (PERUSAHAAN)</td></tr>
                        <tr><td>IURAN BPJSKES (KARY)</td></tr>
                        <tr><td>BPJSKES (PERUSAHAAN)</td></tr>
                        <tr><td>DPLK</td></tr>
                        <tr><td>PPH PSL 21</td></tr>
                        <tr><td class="highlight">POTONGAN LAIN-LAIN (B)</td></tr>
                        <tr><td>KOPERASI</td></tr>
                        <tr><td class="highlight">TOTAL POTONGAN</td></tr>
                        <tr><td>PENDAPATAN BERSIH</td></tr>
                        <tr><td>NO REKENING BANK</td><td>: 7149739XXX</td></tr>
                        <tr><td>NAMA BANK</td><td>: Bank Syariah Indonesia</td></tr>
                    </table>
                </div>
                <div class="right">
                    <table>
                        <tr><td>NIK</td><td>: 20200007</td></tr>
                        <tr><td>NAMA</td><td>: YAN IRAWAN</td></tr>
                        <tr><td>GOL/SGT</td><td>: 6 / 6</td></tr>
                        <tr><td>LEVEL</td><td>: AST</td></tr>
                        <tr><td>PERIODE</td><td>: APRIL 2025</td></tr>
                        <tr><td>UNIT</td><td>: A09 - INFORMATION</td></tr>
                        <tr><td>ORGAN</td><td>: TECHNOLOGY</td></tr>
                    </table>
                </div>
            </div>
            <div class="quote">
                <div>"Satu-satunya cara untuk melakukan pekerjaan hebat adalah mencintai apa yang kamu kerjakan. - Steve Jobs"</div>
                <div class="highlight">#BaktiTimahMedika<br>#BangkitTangguhMandiri</div>
            </div>
        </div>
    </body>
    </html>
    ';

        $nama_file = 'slip_gaji_20200007.pdf';
        $path_simpan = FCPATH . 'pdf/' . $nama_file;
        $mpdf->WriteHTML($html);
        $mpdf->Output($path_simpan, 'F');
        $response = [
            'status' => 'success',
            'message' => 'PDF berhasil disimpan.',
            'download_url' => base_url('pdf/' . $nama_file)
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }


    public function insert()
    {
        $this->_validate();

        // Tambahkan ini sebelum proses insert!
        $id_pegawai = $this->input->post('id_pegawai', true);
        $zis = $this->db->get_where('master_zis', ['id_pegawai' => $id_pegawai])->row();
        $iuran_sertifikat = $this->db->get_where('master_iuran_sertifikat', ['id_pegawai' => $id_pegawai])->row();


        $bulan = $this->input->post('bulan', true);
        $tahun = $this->input->post('tahun', true);

        // Query cek apakah data sudah ada
        $cek = $this->db->get_where('pegawai_penggajian', [
            'id_pegawai' => $id_pegawai,
            'bulan' => $bulan,
            'tahun' => $tahun
        ])->row();

        if ($cek) {
            // Kirim response error, bisa pakai JSON kalau insert via ajax
            echo json_encode([
                'status' => false,
                'inputerror' => ['bulan', 'tahun'],
                'error_string' => ['Penggajian pegawai ini untuk bulan & tahun tersebut sudah ada!', '']
            ]);
            return;
        }

        $id_penggajian = $this->generate_id();

        $data = [
            'id_pegawai_penggajian' => $id_penggajian,
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'bulan' => $this->input->post('bulan', true),
            'tahun' => $this->input->post('tahun', true),
            'id_gaji_pokok' => $this->input->post('id_gaji_pokok', true),
            'id_tunjangan_fungsional' => $this->input->post('id_tunjangan_fungsional') ?: null,
            'id_tunjangan_kesra' => $this->input->post('id_tunjangan_kesra') ?: null,
            'id_tunjangan_komunikasi' => $this->input->post('id_tunjangan_komunikasi') ?: null,
            'id_tunjangan_daerah' => $this->input->post('id_tunjangan_daerah') ?: null,
            'id_tunjangan_radiasi' => $this->input->post('id_tunjangan_radiasi') ?: null,
            'id_tunjangan_perumahan' => $this->input->post('id_tunjangan_perumahan') ?: null,
            'id_tunjangan_penugasan' => $this->input->post('id_tunjangan_penugasan') ?: null,
            'id_tunjangan_kasir' => $this->input->post('id_tunjangan_kasir') ?: null,
            'id_tunjangan_transport' => $this->input->post('id_tunjangan_transport') ?: null,
            'id_kompensasi' => $this->input->post('id_kompensasi') ?: null,
        ];

        $this->gaji->insert($data);

        // --- AMBIL DARI HIDDEN INPUT YANG DIISI DARI MODAL ---
        $insentif = json_decode($this->input->post('insentif_data'), true);
        if ($insentif && is_array($insentif)) {
            $insentif_batch = [];
            foreach ($insentif as $row) {
                if (!empty($row['id'])) {
                    $insentif_batch[] = [
                        'id_pegawai_penggajian'   => $id_penggajian,
                        'id_tunjangan_insentif'   => $row['id'],
                        'nominal'                 => (float) $row['nominal']
                    ];
                }
            }
            if ($insentif_batch) {
                $this->gaji->insert_insentif_batch($insentif_batch);
            }
        }

        $kode = json_decode($this->input->post('kode_data'), true);
        if ($kode && is_array($kode)) {
            $kode_batch = [];
            foreach ($kode as $row) {
                if (!empty($row['id'])) {
                    $kode_batch[] = [
                        'id_pegawai_penggajian'   => $id_penggajian,
                        'id_tunjangan_kode'       => $row['id'],
                        'nominal'                 => (float) $row['nominal']
                    ];
                }
            }
            if ($kode_batch) {
                $this->gaji->insert_kode_batch($kode_batch);
            }
        }


        $potonganList = $this->db->get('master_potongan')->result();

        // b. Ambil nominal gaji pokok dari master (pastikan input id_gaji_pokok valid)
        $gaji_pokok_row = $this->db->get_where('master_gaji_pokok', [
            'id_gaji_pokok' => $this->input->post('id_gaji_pokok', true)
        ])->row();
        $gaji_pokok = $gaji_pokok_row ? (float) $gaji_pokok_row->nominal : 0;

        // c. Ambil tunjangan lain dari master (bisa dirangkum pakai join, atau satu per satu)
        $fungsional = $this->input->post('id_tunjangan_fungsional') ?
            (float) @$this->db->get_where('master_tunjangan_fungsional', ['id_tunjangan' => $this->input->post('id_tunjangan_fungsional')])->row()->tunjangan : 0;
        $kesra = $this->input->post('id_tunjangan_kesra') ?
            (float) @$this->db->get_where('master_tunjangan_kesra', ['id_tunjangan' => $this->input->post('id_tunjangan_kesra')])->row()->tunjangan : 0;
        $komunikasi = $this->input->post('id_tunjangan_komunikasi') ?
            (float) @$this->db->get_where('master_tunjangan_komunikasi', ['id_tunjangan' => $this->input->post('id_tunjangan_komunikasi')])->row()->tunjangan : 0;
        $daerah = $this->input->post('id_tunjangan_daerah') ?
            (float) @$this->db->get_where('master_tunjangan_daerah', ['id_tunjangan' => $this->input->post('id_tunjangan_daerah')])->row()->tunjangan : 0;
        $radiasi = $this->input->post('id_tunjangan_radiasi') ?
            (float) @$this->db->get_where('master_tunjangan_radiasi', ['id_tunjangan' => $this->input->post('id_tunjangan_radiasi')])->row()->tunjangan : 0;
        $perumahan = $this->input->post('id_tunjangan_perumahan') ?
            (float) @$this->db->get_where('master_tunjangan_perumahan', ['id_tunjangan' => $this->input->post('id_tunjangan_perumahan')])->row()->tunjangan : 0;

        // d. Insentif & Kode dijumlah setelah insert batch insentif/kode, atau jika butuh, tambahkan select sum di sini

        // e. Hitung total pendapatan (misal insentif/kode = 0 untuk insert baru)
        $total_pendapatan = $gaji_pokok + $fungsional + $kesra + $komunikasi + $daerah + $radiasi + $perumahan;

        // f. Proses potongan batch
        $data_potongan_batch = [];
        foreach ($potonganList as $pot) {
            $nilai_potongan = 0;

            // Mapping otomatis ke pegawai_kode jika potongan tertentu
            if (in_array($pot->nama_potongan, [
                'BPJSTK (Perusahaan)',
                'BPJS Kesehatan (Perusahaan)',
                'DPLK',
                'PPh Pasal 21',
                'Jaga Shift Dokter'
            ])) {
                // 1. Cari id_master di master_tunjangan_kode
                $kode = $this->db->get_where('master_tunjangan_kode', [
                    'nm_tunjangan' => $pot->nama_potongan
                ])->row();

                // 2. Cari nominal di pegawai_kode (by id_pegawai & id_master)
                $pkode = null;
                if ($kode) {
                    $pkode = $this->db->get_where('pegawai_kode', [
                        'id_pegawai' => $this->input->post('id_pegawai', true),
                        'id_master'  => $kode->id_master
                    ])->row();
                }
                $nilai_potongan = $pkode ? (float)$pkode->nominal : 0;
            }
            // Potongan lain: rumus fixed/persen biasa
            else if (strtolower($pot->tipe_potongan) == 'fixed') {
                $nilai_potongan = $pot->persentase;
            } else if (strtolower($pot->tipe_potongan) == 'persen') {
                $nilai_potongan = ($pot->persentase / 100) * $gaji_pokok;
            }

            // Cek maksimal tetap dijalankan
            if ($pot->maksimal !== NULL && $nilai_potongan > $pot->maksimal) {
                $nilai_potongan = $pot->maksimal;
            }

            $data_potongan_batch[] = [
                'id_pegawai_penggajian' => $id_penggajian,
                'id_potongan'           => $pot->id_potongan,
                'nominal'               => $nilai_potongan
            ];
        }

        if ($zis && $zis->nominal > 0) {
            $id_potongan_zis = $this->db->get_where('master_potongan', ['nama_potongan' => 'ZIS'])->row('id_potongan');
            if ($id_potongan_zis) {
                $data_potongan_batch[] = [
                    'id_pegawai_penggajian' => $id_penggajian,
                    'id_potongan'           => $id_potongan_zis,
                    'nominal'               => (float)$zis->nominal
                ];
            }
        }

        if ($iuran_sertifikat && $iuran_sertifikat->nominal > 0) {
            $id_potongan_sertifikat = $this->db->get_where('master_potongan', ['nama_potongan' => 'Iuran Sertifikat'])->row('id_potongan');
            if ($id_potongan_sertifikat) {
                $data_potongan_batch[] = [
                    'id_pegawai_penggajian' => $id_penggajian,
                    'id_potongan'           => $id_potongan_sertifikat,
                    'nominal'               => (float)$iuran_sertifikat->nominal
                ];
            }
        }

        $this->gaji->insert_potongan_batch($data_potongan_batch);


        echo json_encode(["status" => TRUE]);
    }

    private function clean_currency($value)
    {
        // Kalau null, jadikan string kosong
        $value = $value ?? 0;

        // Hapus semua kecuali angka
        $value = preg_replace('/[^0-9]/', '', $value);

        return $value === '' ? 0 : (float) $value;
    }


    public function insert2()
    {
        $this->_validate();

        $id_penggajian = $this->generate_id2();

        $data = [
            'id_pegawai_penggajian' => $id_penggajian,
            'bulan'                     => $this->input->post('bulan'),
            'tahun'                     => $this->input->post('tahun'),
            'id_pegawai'                => $this->input->post('id_pegawai'),
            // 'id_eselon'                 => $this->input->post('id_eselon'),
            'tunjangan_radiasi'    => $this->clean_currency($this->input->post('tunjangan_radiasi_view')),
            'tunjangan_perumahan'  => $this->clean_currency($this->input->post('tunjangan_perumahan_view')),
            'gaji_pokok'           => $this->clean_currency($this->input->post('gaji_pokok_view')),
            'tunjangan_fungsional' => $this->clean_currency($this->input->post('tunjangan_fungsional_view')),
            'tunjangan_kesra'      => $this->clean_currency($this->input->post('tunjangan_kesra_view')),
            'tunjangan_komunikasi' => $this->clean_currency($this->input->post('tunjangan_komunikasi_view')),
            'tunjangan_daerah'     => $this->clean_currency($this->input->post('tunjangan_daerah_view')),
            'tunjangan_penugasan'  => $this->clean_currency($this->input->post('tunjangan_penugasan')),
            'tunjangan_kasir'      => $this->clean_currency($this->input->post('tunjangan_kasir')),
            'tunjangan_transport'  => $this->clean_currency($this->input->post('tunjangan_transport')),
            'tunjangan_kompensasi' => $this->clean_currency($this->input->post('kompensasi')),
            // kalau insentif & kode dalam bentuk JSON string
            'kode_master_tunjangan_insentif'  => $this->input->post('insentif_data'),
            'kode_master_tunjangan_kode'      => $this->input->post('kode_data'),
        ];

        // var_dump($data);
        // die;

        $this->gaji->insert_penggajian($data);

        echo json_encode(["status" => TRUE]);
    }


    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_pegawai_penggajian', true);

        $data = [
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'bulan' => $this->input->post('bulan', true), // pastikan field ini boleh diupdate
            'tahun' => $this->input->post('tahun', true),
            'id_gaji_pokok' => $this->input->post('id_gaji_pokok', true),
            'id_tunjangan_fungsional' => $this->input->post('id_tunjangan_fungsional') ?: null,
            'id_tunjangan_kesra' => $this->input->post('id_tunjangan_kesra') ?: null,
            'id_tunjangan_komunikasi' => $this->input->post('id_tunjangan_komunikasi') ?: null,
            'id_tunjangan_daerah' => $this->input->post('id_tunjangan_daerah') ?: null,
            'id_tunjangan_radiasi' => $this->input->post('id_tunjangan_radiasi') ?: null,
            'id_tunjangan_perumahan' => $this->input->post('id_tunjangan_perumahan') ?: null,
        ];

        $this->gaji->update($id, $data);

        // Hapus insentif & kode lama
        $this->gaji->delete_insentif_by_penggajian($id);
        $this->gaji->delete_kode_by_penggajian($id);

        // === Insert insentif batch dari JSON
        $insentif = json_decode($this->input->post('insentif_data'), true);
        if ($insentif && is_array($insentif)) {
            $insentif_batch = [];
            foreach ($insentif as $row) {
                if (!empty($row['id'])) {
                    $insentif_batch[] = [
                        'id_pegawai_penggajian'   => $id,
                        'id_tunjangan_insentif'   => $row['id'],
                        'nominal'                 => (float) $row['nominal']
                    ];
                }
            }
            if ($insentif_batch) {
                $this->gaji->insert_insentif_batch($insentif_batch);
            }
        }

        $kode = json_decode($this->input->post('kode_data'), true);
        if ($kode && is_array($kode)) {
            $kode_batch = [];
            foreach ($kode as $row) {
                if (!empty($row['id'])) {
                    $kode_batch[] = [
                        'id_pegawai_penggajian'   => $id,
                        'id_tunjangan_kode'       => $row['id'],
                        'nominal'                 => (float) $row['nominal']
                    ];
                }
            }
            if ($kode_batch) {
                $this->gaji->insert_kode_batch($kode_batch);
            }
        }

        // --- Update Potongan, logika sama persis insert ---
        $this->gaji->delete_potongan_by_penggajian($id);

        $potonganList = $this->db->get('master_potongan')->result();
        $gaji_pokok_row = $this->db->get_where('master_gaji_pokok', [
            'id_gaji_pokok' => $this->input->post('id_gaji_pokok', true)
        ])->row();
        $gaji_pokok = $gaji_pokok_row ? (float) $gaji_pokok_row->nominal : 0;

        $fungsional = $this->input->post('id_tunjangan_fungsional') ?
            (float) @$this->db->get_where('master_tunjangan_fungsional', ['id_tunjangan' => $this->input->post('id_tunjangan_fungsional')])->row()->tunjangan : 0;
        $kesra = $this->input->post('id_tunjangan_kesra') ?
            (float) @$this->db->get_where('master_tunjangan_kesra', ['id_tunjangan' => $this->input->post('id_tunjangan_kesra')])->row()->tunjangan : 0;
        $komunikasi = $this->input->post('id_tunjangan_komunikasi') ?
            (float) @$this->db->get_where('master_tunjangan_komunikasi', ['id_tunjangan' => $this->input->post('id_tunjangan_komunikasi')])->row()->tunjangan : 0;
        $daerah = $this->input->post('id_tunjangan_daerah') ?
            (float) @$this->db->get_where('master_tunjangan_daerah', ['id_tunjangan' => $this->input->post('id_tunjangan_daerah')])->row()->tunjangan : 0;
        $radiasi = $this->input->post('id_tunjangan_radiasi') ?
            (float) @$this->db->get_where('master_tunjangan_radiasi', ['id_tunjangan' => $this->input->post('id_tunjangan_radiasi')])->row()->tunjangan : 0;
        $perumahan = $this->input->post('id_tunjangan_perumahan') ?
            (float) @$this->db->get_where('master_tunjangan_perumahan', ['id_tunjangan' => $this->input->post('id_tunjangan_perumahan')])->row()->tunjangan : 0;

        $total_pendapatan = $gaji_pokok + $fungsional + $kesra + $komunikasi + $daerah + $radiasi + $perumahan;

        $data_potongan_batch = [];
        foreach ($potonganList as $pot) {
            $nilai_potongan = 0;
            // logika rumus potongan sama dengan insert
            // ... (copas saja logic dari insert)
            if (in_array($pot->nama_potongan, [
                'BPJSTK (Perusahaan)',
                'BPJS Kesehatan (Perusahaan)',
                'DPLK',
                'PPh Pasal 21',
                'Jaga Shift Dokter'
            ])) {
                $kode = $this->db->get_where('master_tunjangan_kode', [
                    'nm_tunjangan' => $pot->nama_potongan
                ])->row();
                $pkode = null;
                if ($kode) {
                    $pkode = $this->db->get_where('pegawai_kode', [
                        'id_pegawai' => $this->input->post('id_pegawai', true),
                        'id_master'  => $kode->id_master
                    ])->row();
                }
                $nilai_potongan = $pkode ? (float)$pkode->nominal : 0;
            } else if (strtolower($pot->tipe_potongan) == 'fixed') {
                $nilai_potongan = $pot->persentase;
            } else if (strtolower($pot->tipe_potongan) == 'persen') {
                $nilai_potongan = ($pot->persentase / 100) * $gaji_pokok;
            }
            if ($pot->maksimal !== NULL && $nilai_potongan > $pot->maksimal) {
                $nilai_potongan = $pot->maksimal;
            }

            $data_potongan_batch[] = [
                'id_pegawai_penggajian' => $id,
                'id_potongan'           => $pot->id_potongan,
                'nominal'               => $nilai_potongan
            ];
        }
        $this->gaji->insert_potongan_batch($data_potongan_batch);

        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = decrypt_url($this->input->post('id_pegawai_penggajian'));
        $this->gaji->delete($id);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->gaji->get_by_id(decrypt_url($id));
        // var_dump($data);
        // die;
        echo json_encode($data);
    }

    private function generate_id()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_pegawai_penggajian, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('pegawai_penggajian')->row();
        $max_id = $data->max_id + 1;
        return 'PGJ' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }

    private function generate_id2()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_pegawai_penggajian, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('pegawai_penggajian2')->row();
        $max_id = $data->max_id + 1;
        return 'PGJ' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('id_pegawai') == '') {
            $data['inputerror'][] = 'id_pegawai';
            $data['error_string'][] = 'Pegawai wajib dipilih';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function get_pegawai_by_unit()
    {
        $id_usaha = $this->input->post('id_unit_usaha');
        $id_kerja = $this->input->post('id_unit_kerja');

        $this->db->select('pegawai.id_pegawai, pegawai.nama, pegawai.id_eselon');
        $this->db->from('pegawai_penempatan');
        $this->db->join('pegawai', 'pegawai.id_pegawai = pegawai_penempatan.id_pegawai');
        $this->db->where('pegawai_penempatan.id_unit_usaha', $id_usaha);
        $this->db->where('pegawai_penempatan.id_unit_kerja', $id_kerja);
        $pegawai = $this->db->get()->result();

        echo json_encode($pegawai);
    }

    public function format_rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    public function get_gaji_pokok_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_gaji_pokok, nominal');
        $this->db->from('master_gaji_pokok');
        $this->db->where('id_pegawai', $id_pegawai);
        $gaji = $this->db->get()->row();

        if ($gaji) {
            $response = [
                'id_gaji_pokok' => $gaji->id_gaji_pokok,
                'nominal'       => $this->format_rupiah($gaji->nominal) // Format jadi Rp
            ];
            echo json_encode($response);
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_penugasan_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_tunjangan_penugasan, nominal');
        $this->db->from('tunjangan_penugasan');
        $this->db->where('id_pegawai', $id_pegawai);
        $gaji = $this->db->get()->row();

        if ($gaji) {
            $response = [
                'id_tunjangan_penugasan' => $gaji->id_tunjangan_penugasan,
                'nominal'       => $this->format_rupiah($gaji->nominal) // Format jadi Rp
            ];
            echo json_encode($response);
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_kasir_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_tunjangan_kasir, nominal');
        $this->db->from('tunjangan_kasir');
        $this->db->where('id_pegawai', $id_pegawai);
        $gaji = $this->db->get()->row();

        if ($gaji) {
            $response = [
                'id_tunjangan_kasir' => $gaji->id_tunjangan_kasir,
                'nominal'       => $this->format_rupiah($gaji->nominal) // Format jadi Rp
            ];
            echo json_encode($response);
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_transport_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_tunjangan_transport, nominal');
        $this->db->from('tunjangan_transport');
        $this->db->where('id_pegawai', $id_pegawai);
        $gaji = $this->db->get()->row();

        if ($gaji) {
            $response = [
                'id_tunjangan_transport' => $gaji->id_tunjangan_transport,
                'nominal'       => $this->format_rupiah($gaji->nominal) // Format jadi Rp
            ];
            echo json_encode($response);
        } else {
            echo json_encode(null);
        }
    }

    public function get_kompensasi_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_kompensasi, nominal');
        $this->db->from('kompensasi');
        $this->db->where('id_pegawai', $id_pegawai);
        $gaji = $this->db->get()->row();

        if ($gaji) {
            $response = [
                'id_kompensasi' => $gaji->id_kompensasi,
                'nominal'       => $this->format_rupiah($gaji->nominal) // Format jadi Rp
            ];
            echo json_encode($response);
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_perumahan_otomatis()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_tunjangan, nominal');
        $this->db->from('pegawai_perumahan');
        $this->db->where('id_pegawai', $id_pegawai);
        $pegawai_perumahan = $this->db->get()->row();

        if ($pegawai_perumahan) {
            $response = [
                'id_tunjangan' => $pegawai_perumahan->id_tunjangan,
                'tunjangan'    => $this->format_rupiah($pegawai_perumahan->nominal)
            ];
            echo json_encode($response);
            return;
        }

        // 2. Jika tidak ada, kosongkan saja (atau bisa tampilkan -)
        echo json_encode(null);
    }


    public function get_tunjangan_fungsional_otomatis()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_eselon');
        $this->db->from('pegawai');
        $this->db->where('id_pegawai', $id_pegawai);
        $pegawai = $this->db->get()->row();

        $this->db->select('id_unit_usaha');
        $this->db->from('pegawai_penempatan');
        $this->db->where('id_pegawai', $id_pegawai);
        // $this->db->where('status', 'aktif'); // <-- Uncomment jika ada field status
        $this->db->order_by('id_pegawai_penempatan', 'DESC'); // ambil penempatan terbaru (opsional)
        $this->db->limit(1);
        $penempatan = $this->db->get()->row();

        if ($pegawai && $penempatan) {
            $this->db->select('id_tunjangan, tunjangan');
            $this->db->from('master_tunjangan_fungsional');
            $this->db->where('id_eselon', $pegawai->id_eselon);
            $this->db->where('id_unit_usaha', $penempatan->id_unit_usaha);
            $tunjangan = $this->db->get()->row();

            if ($tunjangan) {
                $response = [
                    'id_tunjangan' => $tunjangan->id_tunjangan,
                    'tunjangan'    => $this->format_rupiah($tunjangan->tunjangan)
                ];
                echo json_encode($response);
            } else {
                echo json_encode(null);
            }
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_kesra_otomatis()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('status_pegawai');
        $this->db->from('pegawai');
        $this->db->where('id_pegawai', $id_pegawai);
        $pegawai = $this->db->get()->row();

        if ($pegawai) {
            $this->db->select('id_tunjangan, tunjangan');
            $this->db->from('master_tunjangan_kesra');
            $this->db->where('status_pegawai', $pegawai->status_pegawai);
            $tunjangan = $this->db->get()->row();

            if ($tunjangan) {
                $response = [
                    'id_tunjangan' => $tunjangan->id_tunjangan,
                    'tunjangan'    => $this->format_rupiah($tunjangan->tunjangan)
                ];
                echo json_encode($response);
            } else {
                echo json_encode(null);
            }
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_komunikasi_otomatis()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('id_eselon');
        $this->db->from('pegawai');
        $this->db->where('id_pegawai', $id_pegawai);
        $pegawai = $this->db->get()->row();

        if ($pegawai) {
            $this->db->select('id_tunjangan, tunjangan');
            $this->db->from('master_tunjangan_komunikasi');
            $this->db->where('id_eselon', $pegawai->id_eselon);
            $tunjangan = $this->db->get()->row();

            if ($tunjangan) {
                $response = [
                    'id_tunjangan' => $tunjangan->id_tunjangan,
                    'tunjangan'    => $this->format_rupiah($tunjangan->tunjangan)
                ];
                echo json_encode($response);
            } else {
                echo json_encode(null);
            }
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_daerah_otomatis()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $id_unit_usaha = $this->input->post('id_unit_usaha');

        $pegawai = $this->db->get_where('pegawai', ['id_pegawai' => $id_pegawai])->row();

        if ($pegawai && $id_unit_usaha) {
            $this->db->select('id_tunjangan, tunjangan');
            $this->db->from('master_tunjangan_daerah');
            $this->db->where('status_pegawai', $pegawai->status_pegawai);
            $this->db->where('id_unit_usaha', $id_unit_usaha);
            $tunjangan = $this->db->get()->row();

            if ($tunjangan) {
                $response = [
                    'id_tunjangan' => $tunjangan->id_tunjangan,
                    'tunjangan'    => $this->format_rupiah($tunjangan->tunjangan)
                ];
                echo json_encode($response);
            } else {
                echo json_encode(null);
            }
        } else {
            echo json_encode(null);
        }
    }


    public function get_tunjangan_radiasi_otomatis()
    {
        $id_pegawai = $this->input->post('id_pegawai');

        $this->db->select('pp.id_unit_usaha, pp.id_unit_kerja_sub');
        $this->db->from('pegawai_penempatan pp');
        $this->db->where('pp.id_pegawai', $id_pegawai);
        $penempatan = $this->db->get()->row();

        if ($penempatan) {
            $this->db->select('id_tunjangan, tunjangan');
            $this->db->from('master_tunjangan_radiasi');
            $this->db->where('id_unit_usaha', $penempatan->id_unit_usaha);
            $this->db->where('id_unit_kerja_sub', $penempatan->id_unit_kerja_sub);
            $tunjangan = $this->db->get()->row();

            if ($tunjangan) {
                $response = [
                    'id_tunjangan' => $tunjangan->id_tunjangan,
                    'tunjangan'    => $this->format_rupiah($tunjangan->tunjangan)
                ];
                echo json_encode($response);
            } else {
                echo json_encode(null);
            }
        } else {
            echo json_encode(null);
        }
    }

    public function get_tunjangan_detail_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $id_pegawai_penggajian = $this->input->post('id_pegawai_penggajian'); // pastikan dikirim dari frontend!
        $result = [
            'insentif' => [],
            'kode' => []
        ];

        // Kalau ada id penggajian, ambil dari pegawai_penggajian_insentif
        if ($id_pegawai_penggajian) {
            // Insentif (transaksi penggajian)
            $insentif = $this->db
                ->select('id_tunjangan_insentif as id_tunjangan, nominal')
                ->from('pegawai_penggajian_insentif')
                ->where('id_pegawai_penggajian', $id_pegawai_penggajian)
                ->get()->result();

            foreach ($insentif as $row) {
                $result['insentif'][$row->id_tunjangan] = (float)$row->nominal;
            }

            // Kode (transaksi penggajian)
            $kode = $this->db
                ->select('id_tunjangan_kode as id_master, nominal')
                ->from('pegawai_penggajian_kode')
                ->where('id_pegawai_penggajian', $id_pegawai_penggajian)
                ->get()->result();

            foreach ($kode as $row) {
                $result['kode'][$row->id_master] = (float)$row->nominal;
            }
        } else {
            // Fallback (jika data belum ada, pakai default setting awal/master)
            $insentif = $this->db
                ->select('id_tunjangan, nominal')
                ->from('pegawai_insentif')
                ->where('id_pegawai', $id_pegawai)
                ->get()->result();

            foreach ($insentif as $row) {
                $result['insentif'][$row->id_tunjangan] = (float)$row->nominal;
            }

            $kode = $this->db
                ->select('id_master, nominal')
                ->from('pegawai_kode')
                ->where('id_pegawai', $id_pegawai)
                ->get()->result();

            foreach ($kode as $row) {
                $result['kode'][$row->id_master] = (float)$row->nominal;
            }
        }

        echo json_encode($result);
    }

    public function get_data_slip_thr($id_pegawai, $periode = null)
    {
        $thr = $this->db->get_where('master_tunjangan_hari_raya', [
            'id_pegawai' => $id_pegawai
        ])->row();

        $pph21 = $this->gaji->get_potongan_kode_by_nama($id_pegawai, 'PPh Pasal 21');

        $pegawai = $this->db->get_where('pegawai', ['id_pegawai' => $id_pegawai])->row();

        $thr_nominal = $thr ? $thr->nominal : 0;
        $total_potongan = $pph21;
        $pendapatan_bersih = $thr_nominal - $total_potongan;

        return [
            'thr' => $thr_nominal,
            'pph21' => $pph21,
            'total_potongan' => $total_potongan,
            'pendapatan_bersih' => $pendapatan_bersih,
            'pegawai' => $pegawai
        ];
    }

    public function kirim_email()
    {
        $id_encrypted = $this->input->post('id_pegawai_penggajian');
        $id_pegawai_penggajian = decrypt_url($id_encrypted);

        if (!$id_pegawai_penggajian) {
            $response = [
                'status' => 'error',
                'message' => 'ID slip penggajian tidak valid!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        if (!function_exists('format_rupiah')) {
            function format_rupiah($angka)
            {
                return 'Rp. ' . number_format($angka, 0, ',', '.');
            }
        }

        $this->load->model('Gaji_model', 'gaji');
        $data = $this->gaji->get_slip_gaji_by_penggajian($id_pegawai_penggajian); // bikin sendiri model ini (lihat bawah)

        if (!$data) {
            $response = [
                'status' => 'error',
                'message' => 'Data slip gaji tidak ditemukan!'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $nama_file_pdf = 'slip_gaji_' . str_replace(' ', '_', strtolower($data['nama'])) . '.pdf';
        $path_pdf = FCPATH . 'pdf/' . $nama_file_pdf;

        ob_start();
?>
        <html>

        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }

                .wrapper {
                    width: 98%;
                    margin: 0 auto;
                }

                .header {
                    width: 100%;
                    display: flex;
                    align-items: flex-start;
                }

                .logo {
                    width: 110px;
                    margin-bottom: 4px;
                }

                .title {
                    text-align: center;
                    font-size: 22px;
                    font-weight: bold;
                    margin: 10px 0 15px 0;
                    letter-spacing: 1px;
                }

                .main-table {
                    width: 100%;
                }

                .col {
                    vertical-align: top;
                    width: 33%;
                }

                .quote {
                    margin-top: 32px;
                    text-align: right;
                    font-size: 12px;
                    font-style: italic;
                }

                .highlight {
                    font-weight: bold;
                }

                table.inner-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                table.inner-table td {
                    vertical-align: top;
                    padding-bottom: 4px;
                }
            </style>
        </head>

        <body>
            <div class="wrapper">
                <div class="header">
                    <img src="file://<?= FCPATH . 'assets/img/logoIHC.png' ?>" style="width:120px;">
                </div>
                <div class="title">SLIP GAJI</div>
                <table class="main-table">
                    <tr>
                        <!-- Kolom kiri: Pendapatan -->
                        <td class="col">
                            <table class="inner-table">
                                <tr>
                                    <td class="highlight">PENDAPATAN (Rp)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>GAJI POKOK</td>
                                    <td>: <?= format_rupiah($data['gaji_pokok']) ?></td>
                                </tr>
                                <?php if (!empty($data['tunjangan_fungsional']) && $data['tunjangan_fungsional'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN FUNGSIONAL</td>
                                        <td>: <?= format_rupiah($data['tunjangan_fungsional']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_kesra']) && $data['tunjangan_kesra'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN KESRA</td>
                                        <td>: <?= format_rupiah($data['tunjangan_kesra']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_komunikasi']) && $data['tunjangan_komunikasi'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN KOMUNIKASI</td>
                                        <td>: <?= format_rupiah($data['tunjangan_komunikasi']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_daerah']) && $data['tunjangan_daerah'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN DAERAH</td>
                                        <td>: <?= format_rupiah($data['tunjangan_daerah']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_radiasi']) && $data['tunjangan_radiasi'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN RADIASI</td>
                                        <td>: <?= format_rupiah($data['tunjangan_radiasi']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_perumahan']) && $data['tunjangan_perumahan'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN PERUMAHAN</td>
                                        <td>: <?= format_rupiah($data['tunjangan_perumahan']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['insentif']) && $data['insentif'] != 0): ?>
                                    <tr>
                                        <td>INSENTIF</td>
                                        <td>: <?= format_rupiah($data['insentif']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_penugasan']) && $data['tunjangan_penugasan'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN PENUGASAN</td>
                                        <td>: <?= format_rupiah($data['tunjangan_penugasan']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_kasir']) && $data['tunjangan_kasir'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN KASIR</td>
                                        <td>: <?= format_rupiah($data['tunjangan_kasir']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['tunjangan_transport']) && $data['tunjangan_transport'] != 0): ?>
                                    <tr>
                                        <td>TUNJANGAN TRANSPORT</td>
                                        <td>: <?= format_rupiah($data['tunjangan_transport']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['kompensasi']) && $data['kompensasi'] != 0): ?>
                                    <tr>
                                        <td>KOMPENSASI</td>
                                        <td>: <?= format_rupiah($data['kompensasi']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (!empty($data['kode_lain']) && $data['kode_lain'] != 0): ?>
                                    <tr>
                                        <td>KODE LAIN</td>
                                        <td>: <?= format_rupiah($data['kode_lain']) ?></td>
                                    </tr>
                                <?php endif; ?>

                                <tr>
                                    <td class="highlight">TOTAL PENDAPATAN</td>
                                    <td>: <?= format_rupiah($data['total_pendapatan']) ?></td>
                                </tr>
                            </table>
                        </td>
                        <!-- Kolom tengah: Potongan -->
                        <td class="col">
                            <table class="inner-table">
                                <tr>
                                    <td class="highlight">POTONGAN</td>
                                    <td></td>
                                </tr>
                                <?php foreach ($data['potongan_rincian'] as $nama => $nom):
                                    if (!empty($nom) && $nom != 0): ?>
                                        <tr>
                                            <td><?= $nama ?></td>
                                            <td>: <?= format_rupiah($nom) ?></td>
                                        </tr>
                                <?php endif;
                                endforeach; ?>
                                <tr>
                                    <td class="highlight">TOTAL POTONGAN</td>
                                    <td>: <?= format_rupiah($data['total_potongan']) ?></td>
                                </tr>
                                <tr>
                                    <td class="highlight">PENDAPATAN BERSIH</td>
                                    <td>: <?= format_rupiah($data['pendapatan_bersih']) ?></td>
                                </tr>
                                <tr>
                                    <td>NO REKENING</td>
                                    <td>: <?= $data['no_rekening'] ?></td>
                                </tr>
                                <tr>
                                    <td>BANK</td>
                                    <td>: <?= $data['nama_bank'] ?></td>
                                </tr>
                            </table>
                        </td>
                        <!-- Kolom kanan: Data Pegawai + Quote -->
                        <td class="col">
                            <table class="inner-table">
                                <tr>
                                    <td>NIK</td>
                                    <td>: <?= $data['nik'] ?></td>
                                </tr>
                                <tr>
                                    <td>NAMA</td>
                                    <td>: <?= $data['nama'] ?></td>
                                </tr>
                                <tr>
                                    <td>GOL/SGT</td>
                                    <td>: <?= $data['golongan'] ?? '-' ?> / <?= $data['sangat'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td>LEVEL</td>
                                    <td>: <?= $data['level'] ?></td>
                                </tr>
                                <tr>
                                    <td>PERIODE</td>
                                    <td>: <?= $data['periode'] ?></td>
                                </tr>
                                <tr>
                                    <td>UNIT</td>
                                    <td>: <?= $data['unit'] ?></td>
                                </tr>
                                <tr>
                                    <td>ORGAN</td>
                                    <td>: TECHNOLOGY</td>
                                </tr>
                            </table>
                            <div class="quote">
                                "Satu-satunya cara untuk melakukan pekerjaan hebat adalah mencintai apa yang kamu kerjakan. - Steve Jobs"<br>
                                <span class="highlight">#BaktiTimahMedika<br>#BangkitTangguhMandiri</span>
                            </div>
                        </td>

                    </tr>
                </table>
            </div>
        </body>

        </html>
        <?php
        $html = ob_get_clean();

        // === Generate PDF pakai mPDF ===
        require_once FCPATH . 'vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output($path_pdf, 'F');


        // === Kirim Email ===
        $this->load->library('email');
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'rezky21ti@mahasiswa.pcr.ac.id',
            'smtp_pass' => 'rihg uelh xrup aive',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        );
        $this->email->initialize($config);
        $this->email->from('rezky21ti@mahasiswa.pcr.ac.id', 'PT Bakti Timah');
        // $this->email->to($data['email_pegawai']); // otomatis ke pegawai
        $this->email->to('akbarmaulana.am826@gmail.com'); // ganti dengan email pribadi kamu

        $this->email->subject('Slip Gaji Bulan ' . $data['bulan'] . ' ' . $data['tahun']);
        $this->email->message('<h3>Yth. ' . $data['nama'] . '<br>Berikut terlampir slip gaji Anda.</h3>');
        $this->email->attach($path_pdf);

        if ($this->email->send()) {
            // === UPDATE status_konfirmasi ke 1 ===
            $this->db->where('id_pegawai_penggajian', $id_pegawai_penggajian)
                ->update('pegawai_penggajian', ['status_konfirmasi' => 1]);
            // Ambil periode dari data
            $periode = $data['periode']; // contoh: "november 2025"
            $periode = ucwords(strtolower($periode)); // "November 2025"
            list($bulan_text, $tahun) = explode(' ', $periode);

            // Mapping bulan teks ke angka
            $bulan_map = [
                'Januari' => 1,
                'Februari' => 2,
                'Maret' => 3,
                'April' => 4,
                'Mei' => 5,
                'Juni' => 6,
                'Juli' => 7,
                'Agustus' => 8,
                'September' => 9,
                'Oktober' => 10,
                'November' => 11,
                'Desember' => 12,
            ];

            $bulan = isset($bulan_map[$bulan_text]) ? $bulan_map[$bulan_text] : null;

            $pendapatan_items = [
                'GAJI POKOK' => $data['gaji_pokok'],
                'TUNJANGAN FUNGSIONAL' => $data['tunjangan_fungsional'],
                'TUNJANGAN KESRA' => $data['tunjangan_kesra'],
                'TUNJANGAN KOMUNIKASI' => $data['tunjangan_komunikasi'],
                'TUNJANGAN DAERAH' => $data['tunjangan_daerah'],
                'TUNJANGAN RADIASI' => $data['tunjangan_radiasi'],
                'TUNJANGAN PERUMAHAN' => $data['tunjangan_perumahan'],
                'INSENTIF' => $data['insentif'],
                'TUNJANGAN PENUGASAN' => $data['tunjangan_penugasan'],
                'TUNJANGAN KASIR' => $data['tunjangan_kasir'],
                'TUNJANGAN TRANSPORT' => $data['tunjangan_transport'],
                'KOMPENSASI' => $data['kompensasi'],
                'KODE LAIN' => $data['kode_lain']
            ];

            foreach ($pendapatan_items as $nama => $jumlah) {
                if (!empty($jumlah) && $jumlah != 0) {
                    $this->db->insert('pendapatan_terkonfirmasi', [
                        'id_pegawai_penggajian' => $id_pegawai_penggajian,
                        'nama_pegawai' => $data['nama'],
                        'nama_pendapatan' => $nama,
                        'nominal' => $jumlah,
                        'bulan' => $bulan,
                        'tahun' => $tahun
                    ]);
                }
            }

            // === INSERT Potongan ===
            foreach ($data['potongan_rincian'] as $nama => $jumlah) {
                if (!empty($jumlah) && $jumlah != 0) {
                    $this->db->insert('potongan_terkonfirmasi', [
                        'id_pegawai_penggajian' => $id_pegawai_penggajian,
                        'nama_pegawai' => $data['nama'],
                        'nama_potongan' => $nama,
                        'nominal' => $jumlah,
                        'bulan' => $bulan,
                        'tahun' => $tahun
                    ]);
                }
            }

            $response = [
                'status' => 'success',
                'message' => 'Slip gaji berhasil dikirim ke email pegawai & status konfirmasi sudah diupdate!'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal mengirim email! ' . $this->email->print_debugger(['headers'])
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function kirim_slip_thr()
    {
        if (!function_exists('format_rupiah')) {
            function format_rupiah($angka)
            {
                return number_format($angka, 0, ',', '.');
            }
        }

        $id_pegawai_penggajian = $this->input->post('id_pegawai_penggajian', true);
        if (!$id_pegawai_penggajian) {
            echo json_encode(['status' => 'error', 'message' => 'ID penggajian tidak dikirim!']);
            exit;
        }
        $row = $this->db->get_where('pegawai_penggajian', ['id_pegawai_penggajian' => decrypt_url($id_pegawai_penggajian)])->row();
        if (!$row) {
            echo json_encode(['status' => 'error', 'message' => 'Data penggajian tidak ditemukan!']);
            exit;
        }
        $id_pegawai = $row->id_pegawai;

        // Ambil data penempatan, pegawai, dan unit
        $penempatan = $this->db->select('unit_level.nm_unit_level, unit_organisasi.nm_unit_organisasi')
            ->from('pegawai_penempatan')
            ->join('unit_level', 'unit_level.id_unit_level = pegawai_penempatan.id_unit_level', 'left')
            ->join('unit_organisasi', 'unit_organisasi.id_unit_organisasi = pegawai_penempatan.id_unit_organisasi', 'left')
            ->where('pegawai_penempatan.id_pegawai', $id_pegawai)
            ->get()->row();

        $level = $penempatan->nm_unit_level ?? '-';
        $unit  = $penempatan->nm_unit_organisasi ?? '-';

        // Data pegawai
        $pegawai = $this->db->get_where('pegawai', ['id_pegawai' => $id_pegawai])->row();

        // THR dan PPh21 (tunjangan)
        $thr = $this->db->get_where('master_tunjangan_hari_raya', [
            'id_pegawai' => $id_pegawai
        ])->row();

        $bonus = $this->db->get_where('imbalan_jasa_produksi', [
            'id_pegawai' => $id_pegawai
        ])->row();
        $thr_nominal = $thr ? $thr->nominal : 0;
        $bonus_nominal = $bonus ? $bonus->nominal : 0;

        $id_master_pph = $this->db->get_where('master_tunjangan_kode', [
            'nm_tunjangan' => 'PPh Pasal 21'
        ])->row('id_master');
        $pph21 = 0;
        if ($id_master_pph) {
            $pph21 = $this->db->get_where('pegawai_kode', [
                'id_pegawai' => $id_pegawai,
                'id_master' => $id_master_pph
            ])->row('nominal');
            $pph21 = $pph21 ? (float)$pph21 : 0;
        }

        // Step perhitungan SLIP THR SESUAI ATASANMU
        $total_pendapatan  = $thr_nominal + $pph21;
        $total_potongan    = $pph21;
        $pendapatan_bersih = $total_pendapatan - $total_potongan; // = THR saja

        $periode = strtoupper(date('F Y'));
        $no_rekening = $pegawai->no_rek ?? '-';
        $nama_bank = $pegawai->nama_bank ?? '-';
        $nik    = $pegawai->nik ?? '-';
        $nama   = $pegawai->nama ?? '-';
        $golsgt = ($pegawai->gol ?? '-') . ' / ' . ($pegawai->sgt ?? '-');
        $organ  = $pegawai->fungsi ?? '-';

        // Generate SLIP PDF
        ob_start();
        ?>
        <html>

        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }

                .header {
                    width: 100%;
                }

                .logo {
                    width: 120px;
                }

                .title {
                    text-align: center;
                    font-size: 22px;
                    font-weight: bold;
                    margin: 10px 0 18px 0;
                    letter-spacing: 1px;
                }

                .main-table {
                    width: 100%;
                }

                .col {
                    vertical-align: top;
                    width: 33%;
                }

                .highlight {
                    font-weight: bold;
                }

                .number {
                    text-align: right;
                }

                .ramadhan {
                    font-weight: bold;
                    text-align: left;
                    font-size: 13px;
                    margin-top: 12px;
                }

                table.inner-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                table.inner-table td {
                    vertical-align: top;
                    padding-bottom: 3px;
                }
            </style>
        </head>

        <body>
            <div class="header">
                <img class="logo" src="https://i.ibb.co/gMTdkhx/logo-ihc.png" alt="IHC Logo">
            </div>
            <div class="title">SLIP THR</div>
            <table class="main-table">
                <tr>
                    <!-- Kiri: Pendapatan -->
                    <td class="col">
                        <table class="inner-table">
                            <tr>
                                <td class="highlight">PENDAPATAN (Rp)</td>
                                <td class="number"></td>
                            </tr>
                            <tr>
                                <td>THR</td>
                                <td class="number">: <?= format_rupiah($thr_nominal) ?></td>
                            </tr>
                            <tr>
                                <td>IMBALAN JASA PRODUKSI</td>
                                <td class="number">: <?= format_rupiah($bonus_nominal) ?></td>
                            </tr>
                            <tr>
                                <td>PPh PSL 21</td>
                                <td class="number">: <?= format_rupiah($pph21) ?></td>
                            </tr>
                            <tr>
                                <td class="highlight">TOTAL PENDAPATAN</td>
                                <td class="number">: <?= format_rupiah($total_pendapatan) ?></td>
                            </tr>
                        </table>
                    </td>
                    <!-- Tengah: Potongan -->
                    <td class="col">
                        <table class="inner-table">
                            <tr>
                                <td class="highlight">POTONGAN WAJIB (A)</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>PPh PSL 21</td>
                                <td class="number">: <?= format_rupiah($pph21) ?></td>
                            </tr>
                            <tr>
                                <td class="highlight">TOTAL POTONGAN</td>
                                <td class="number">: <?= format_rupiah($total_potongan) ?></td>
                            </tr>
                            <tr>
                                <td class="highlight">PENDAPATAN BERSIH</td>
                                <td class="number"><b>: <?= format_rupiah($pendapatan_bersih) ?></b></td>
                            </tr>
                            <tr>
                                <td>NO REKENING BANK</td>
                                <td class="number">: <?= $no_rekening ?></td>
                            </tr>
                            <tr>
                                <td>NAMA BANK</td>
                                <td class="number">: <?= $nama_bank ?></td>
                            </tr>
                        </table>
                    </td>
                    <!-- Kanan: Identitas Pegawai -->
                    <td class="col">
                        <table class="inner-table">
                            <tr>
                                <td>NIK</td>
                                <td>: <?= $nik ?></td>
                            </tr>
                            <tr>
                                <td>NAMA</td>
                                <td>: <?= strtoupper($nama) ?></td>
                            </tr>
                            <tr>
                                <td>GOL/SGT</td>
                                <td>: <?= $golsgt ?></td>
                            </tr>
                            <tr>
                                <td>LEVEL</td>
                                <td>: <?= $level ?></td>
                            </tr>
                            <tr>
                                <td>PERIODE</td>
                                <td>: <?= $periode ?></td>
                            </tr>
                            <tr>
                                <td>UNIT</td>
                                <td>: <?= $unit ?></td>
                            </tr>
                            <tr>
                                <td>ORGAN</td>
                                <td>: <?= $organ ?></td>
                            </tr>
                        </table>
                        <div class="ramadhan">
                            Selembut embun di pagi hari, tengadah<br>
                            tangan sepuluh jari, ucapkan salah<br>
                            setulus hati, mari kita bersama berbenah<br>
                            diri. Marhaban Ya Ramadhan. Mohon<br>
                            maaf lahir batin, selamat menunaikan
                        </div>
                    </td>
                </tr>
            </table>
        </body>

        </html>
        <?php
        $html = ob_get_clean();

        require_once FCPATH . 'vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
        $mpdf->WriteHTML($html);

        $nama_file_pdf = 'slip_thr_' . strtolower(str_replace(' ', '_', $nama)) . '.pdf';
        $path_pdf = FCPATH . 'pdf/' . $nama_file_pdf;
        $mpdf->Output($path_pdf, 'F');

        $this->load->library('email');
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'rezky21ti@mahasiswa.pcr.ac.id',
            'smtp_pass' => 'rihg uelh xrup aive',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        );
        $this->email->initialize($config);
        $this->email->from('rezky21ti@mahasiswa.pcr.ac.id', 'PT Bakti Timah');
        $this->email->to('akbarmaulana.am826@gmail.com');
        $this->email->subject('Slip THR Periode ' . $periode);
        $this->email->message('<h3>Yth. ' . $nama . '<br>Berikut terlampir slip THR Anda.</h3>');
        $this->email->attach($path_pdf);

        if ($this->email->send()) {
            $response = [
                'status' => 'success',
                'message' => 'Slip THR berhasil dikirim ke email pegawai!'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal mengirim email! ' . $this->email->print_debugger(['headers'])
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function kirim_slip_bonus()
    {
        if (!function_exists('format_rupiah')) {
            function format_rupiah($angka)
            {
                return number_format($angka, 0, ',', '.');
            }
        }

        $id_pegawai_penggajian = $this->input->post('id_pegawai_penggajian', true);
        if (!$id_pegawai_penggajian) {
            echo json_encode(['status' => 'error', 'message' => 'ID penggajian tidak dikirim!']);
            exit;
        }
        $row = $this->db->get_where('pegawai_penggajian', ['id_pegawai_penggajian' => decrypt_url($id_pegawai_penggajian)])->row();
        if (!$row) {
            echo json_encode(['status' => 'error', 'message' => 'Data penggajian tidak ditemukan!']);
            exit;
        }
        $id_pegawai = $row->id_pegawai;

        // Ambil data penempatan, pegawai, dan unit
        $penempatan = $this->db->select('unit_level.nm_unit_level, unit_organisasi.nm_unit_organisasi')
            ->from('pegawai_penempatan')
            ->join('unit_level', 'unit_level.id_unit_level = pegawai_penempatan.id_unit_level', 'left')
            ->join('unit_organisasi', 'unit_organisasi.id_unit_organisasi = pegawai_penempatan.id_unit_organisasi', 'left')
            ->where('pegawai_penempatan.id_pegawai', $id_pegawai)
            ->get()->row();

        $level = $penempatan->nm_unit_level ?? '-';
        $unit  = $penempatan->nm_unit_organisasi ?? '-';

        // Data pegawai
        $pegawai = $this->db->get_where('pegawai', ['id_pegawai' => $id_pegawai])->row();

        // THR dan PPh21 (tunjangan)
        $thr = $this->db->get_where('master_tunjangan_hari_raya', [
            'id_pegawai' => $id_pegawai
        ])->row();

        $bonus = $this->db->get_where('imbalan_jasa_produksi', [
            'id_pegawai' => $id_pegawai
        ])->row();
        $thr_nominal = $thr ? $thr->nominal : 0;
        $bonus_nominal = $bonus ? $bonus->nominal : 0;

        $id_master_pph = $this->db->get_where('master_tunjangan_kode', [
            'nm_tunjangan' => 'PPh Pasal 21'
        ])->row('id_master');
        $pph21 = 0;
        if ($id_master_pph) {
            $pph21 = $this->db->get_where('pegawai_kode', [
                'id_pegawai' => $id_pegawai,
                'id_master' => $id_master_pph
            ])->row('nominal');
            $pph21 = $pph21 ? (float)$pph21 : 0;
        }

        // Step perhitungan SLIP THR SESUAI ATASANMU
        $total_pendapatan  = $thr_nominal + $pph21;
        $total_potongan    = $pph21;
        $pendapatan_bersih = $total_pendapatan - $total_potongan; // = THR saja

        $periode = strtoupper(date('F Y'));
        $no_rekening = $pegawai->no_rek ?? '-';
        $nama_bank = $pegawai->nama_bank ?? '-';
        $nik    = $pegawai->nik ?? '-';
        $nama   = $pegawai->nama ?? '-';
        $golsgt = ($pegawai->gol ?? '-') . ' / ' . ($pegawai->sgt ?? '-');
        $organ  = $pegawai->fungsi ?? '-';

        // Generate SLIP PDF
        ob_start();
        ?>
        <html>

        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }

                .header {
                    width: 100%;
                }

                .logo {
                    width: 120px;
                }

                .title {
                    text-align: center;
                    font-size: 22px;
                    font-weight: bold;
                    margin: 10px 0 18px 0;
                    letter-spacing: 1px;
                }

                .main-table {
                    width: 100%;
                }

                .col {
                    vertical-align: top;
                    width: 33%;
                }

                .highlight {
                    font-weight: bold;
                }

                .number {
                    text-align: right;
                }

                .ramadhan {
                    font-weight: bold;
                    text-align: left;
                    font-size: 13px;
                    margin-top: 12px;
                }

                table.inner-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                table.inner-table td {
                    vertical-align: top;
                    padding-bottom: 3px;
                }
            </style>
        </head>

        <body>
            <div class="header">
                <img class="logo" src="https://i.ibb.co/gMTdkhx/logo-ihc.png" alt="IHC Logo">
            </div>
            <div class="title">SLIP THR</div>
            <table class="main-table">
                <tr>
                    <!-- Kiri: Pendapatan -->
                    <td class="col">
                        <table class="inner-table">
                            <tr>
                                <td class="highlight">PENDAPATAN (Rp)</td>
                                <td class="number"></td>
                            </tr>
                            <!-- <tr>
                                <td>THR</td>
                                <td class="number">: <?= format_rupiah($thr_nominal) ?></td>
                            </tr> -->
                            <tr>
                                <td>IMBALAN JASA PRODUKSI</td>
                                <td class="number">: <?= format_rupiah($bonus_nominal) ?></td>
                            </tr>
                            <tr>
                                <td>PPh PSL 21</td>
                                <td class="number">: <?= format_rupiah($pph21) ?></td>
                            </tr>
                            <tr>
                                <td class="highlight">TOTAL PENDAPATAN</td>
                                <td class="number">: <?= format_rupiah($total_pendapatan) ?></td>
                            </tr>
                        </table>
                    </td>
                    <!-- Tengah: Potongan -->
                    <td class="col">
                        <table class="inner-table">
                            <tr>
                                <td class="highlight">POTONGAN WAJIB (A)</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>PPh PSL 21</td>
                                <td class="number">: <?= format_rupiah($pph21) ?></td>
                            </tr>
                            <tr>
                                <td class="highlight">TOTAL POTONGAN</td>
                                <td class="number">: <?= format_rupiah($total_potongan) ?></td>
                            </tr>
                            <tr>
                                <td class="highlight">PENDAPATAN BERSIH</td>
                                <td class="number"><b>: <?= format_rupiah($pendapatan_bersih) ?></b></td>
                            </tr>
                            <tr>
                                <td>NO REKENING BANK</td>
                                <td class="number">: <?= $no_rekening ?></td>
                            </tr>
                            <tr>
                                <td>NAMA BANK</td>
                                <td class="number">: <?= $nama_bank ?></td>
                            </tr>
                        </table>
                    </td>
                    <!-- Kanan: Identitas Pegawai -->
                    <td class="col">
                        <table class="inner-table">
                            <tr>
                                <td>NIK</td>
                                <td>: <?= $nik ?></td>
                            </tr>
                            <tr>
                                <td>NAMA</td>
                                <td>: <?= strtoupper($nama) ?></td>
                            </tr>
                            <tr>
                                <td>GOL/SGT</td>
                                <td>: <?= $golsgt ?></td>
                            </tr>
                            <tr>
                                <td>LEVEL</td>
                                <td>: <?= $level ?></td>
                            </tr>
                            <tr>
                                <td>PERIODE</td>
                                <td>: <?= $periode ?></td>
                            </tr>
                            <tr>
                                <td>UNIT</td>
                                <td>: <?= $unit ?></td>
                            </tr>
                            <tr>
                                <td>ORGAN</td>
                                <td>: <?= $organ ?></td>
                            </tr>
                        </table>
                        <div class="ramadhan">
                            Selembut embun di pagi hari, tengadah<br>
                            tangan sepuluh jari, ucapkan salah<br>
                            setulus hati, mari kita bersama berbenah<br>
                            diri. Marhaban Ya Ramadhan. Mohon<br>
                            maaf lahir batin, selamat menunaikan
                        </div>
                    </td>
                </tr>
            </table>
        </body>

        </html>
<?php
        $html = ob_get_clean();

        require_once FCPATH . 'vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
        $mpdf->WriteHTML($html);

        $nama_file_pdf = 'slip_thr_' . strtolower(str_replace(' ', '_', $nama)) . '.pdf';
        $path_pdf = FCPATH . 'pdf/' . $nama_file_pdf;
        $mpdf->Output($path_pdf, 'F');

        $this->load->library('email');
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'rezky21ti@mahasiswa.pcr.ac.id',
            'smtp_pass' => 'rihg uelh xrup aive',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        );
        $this->email->initialize($config);
        $this->email->from('rezky21ti@mahasiswa.pcr.ac.id', 'PT Bakti Timah');
        $this->email->to('akbarmaulana.am826@gmail.com');
        $this->email->subject('Slip THR Periode ' . $periode);
        $this->email->message('<h3>Yth. ' . $nama . '<br>Berikut terlampir slip THR Anda.</h3>');
        $this->email->attach($path_pdf);

        if ($this->email->send()) {
            $response = [
                'status' => 'success',
                'message' => 'Slip THR berhasil dikirim ke email pegawai!'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal mengirim email! ' . $this->email->print_debugger(['headers'])
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
