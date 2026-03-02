<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai_kode extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_pegawai_kode', 'pegawai_kode');
        $this->load->model('M_gaji_pokok', 'gaji_pokok');
    }

    public function index()
    {
        $this->data['title'] = 'Pegawai Kode';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
                <button type="button" class="btn btn-sm btn-success waves-effect waves-light" onclick="import_excel()">
                    <i class="fe-file-text"></i> Excel
                </button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Pegawai Kode';
        $this->data['ml'] = 'Daftar Pegawai Kode';

        $this->data['list_pegawai'] = $this->db->get('pegawai')->result();
        $this->data['list_kode'] = $this->db->get('master_tunjangan_kode')->result();

        $this->template->load('templates/master', 'admin/master/pegawai_kode/list', $this->data);
    }

    public function import_excel()
    {
        $this->data['title'] = 'Import Excel';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
                <button type="button" class="btn btn-sm btn-success waves-effect waves-light" onclick="import_excel()">
                    <i class="fe-download"></i> Excel
                </button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Import Excel';
        // $this->data['ml'] = 'Daftar Gaji Pokok';
        // $this->data['unit_levels'] = $this->M_unit_level->get_all();
        // $this->data['unit_usaha'] = $this->M_unit_usaha->get_all();
        // $this->data['unit_kerja'] = $this->M_unit_kerja->get_all();
        // $this->data['pegawai'] = $this->M_pegawai->get_all();

        $this->template->load('templates/master', 'admin/master/pegawai_kode/import_excel', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->pegawai_kode->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" class="btn-xs btn-primary" onclick="update(\'' . encrypt_url($item->id_pegawai_kode) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" class="btn-xs btn-danger" onclick="deletedata(\'' . encrypt_url($item->id_pegawai_kode) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->id_pegawai_kode;
            $row[] = $item->nama_pegawai ?: $item->id_pegawai;
            $row[] = $item->nm_tunjangan ?: $item->id_master;
            $row[] = 'Rp ' . number_format($item->nominal ?? 0, 0, ',', '.');
            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->pegawai_kode->count_all(),
            "recordsFiltered" => $this->pegawai_kode->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    public function potongan_bpsjtk()
    {
        // $potongan = $this->potongan->get_potongan();
        $gaji_pokok_list = $this->gaji_pokok->get_gaji_pokok();
        // $bpjstk = $this->pegawai_kode->get_bpjstk();
        // $bpjs = $this->pegawai_kode->get_bpjs();

        $hasil_potongan = [];

        foreach ($gaji_pokok_list as $row) {
            $nominal = $row->nominal;

            // Hitung komponen potongan
            $jkm             = 0.0024 * $nominal;
            $jkk             = 0.003 * $nominal;
            $jht_perusahaan  = 0.037 * $nominal;
            $jht_karyawan    = 0.02 * $nominal;

            $jp_karyawan     = ($nominal >= 10547400) ? (0.01 * 10547400) : (0.01 * $nominal);
            $jp_perusahaan   = ($nominal >= 10547400) ? (0.02 * 10547400) : (0.02 * $nominal);

            $bpjstk_perusahaan = $jkm + $jkk + $jht_perusahaan + $jp_perusahaan;
            $bpjs_karyawan     = $jht_karyawan + $jp_karyawan;

            $hasil_potongan[] = [
                // 'gaji_pokok'         => $nominal,
                // 'jkm'                => $jkm,
                // 'jkk'                => $jkk,
                // 'jht_perusahaan'     => $jht_perusahaan,
                // 'jp_perusahaan'      => $jp_perusahaan,
                'bpjstk_perusahaan'  => $bpjstk_perusahaan,
                // 'jht_karyawan'       => $jht_karyawan,
                // 'jp_karyawan'        => $jp_karyawan,
                'bpjstk_karyawan'      => $bpjs_karyawan
            ];
        }

        // Cek hasil semua potongan
        echo "<pre>";
        print_r($hasil_potongan);
        echo "</pre>";
        die;
    }
    public function potongan_bpsj_kesehatan()
    {
        // $potongan = $this->potongan->get_potongan();
        $gaji_pokok_list = $this->gaji_pokok->get_gaji_pokok();
        // $bpjstk = $this->pegawai_kode->get_bpjstk();
        // $bpjs = $this->pegawai_kode->get_bpjs();

        $hasil_potongan = [];

        foreach ($gaji_pokok_list as $row) {
            $nominal = $row->nominal;

            // Hitung komponen potongan
            $jkm             = 0.04 * $nominal;
            $jkk             = 0.01 * $nominal;
            // $jht_perusahaan  = 0.037 * $nominal;
            // $jht_karyawan    = 0.02 * $nominal;

            // $jp_karyawan     = ($nominal >= 10547400) ? (0.01 * 10547400) : (0.01 * $nominal);
            // $jp_perusahaan   = ($nominal >= 10547400) ? (0.02 * 10547400) : (0.02 * $nominal);

            // $bpjstk_perusahaan = $jkm + $jkk + $jht_perusahaan + $jp_perusahaan;
            // $bpjs_karyawan     = $jht_karyawan + $jp_karyawan;

            $hasil_potongan[] = [
                // 'gaji_pokok'         => $nominal,
                // 'jkm'                => $jkm,
                // 'jkk'                => $jkk,
                // 'jht_perusahaan'     => $jht_perusahaan,
                // 'jp_perusahaan'      => $jp_perusahaan,
                'bpjskes_perusahaan'  => $jkm,
                // 'jht_karyawan'       => $jht_karyawan,
                // 'jp_karyawan'        => $jp_karyawan,
                'bpjskes_karyawan'      => $jkk
            ];
        }

        // Cek hasil semua potongan
        echo "<pre>";
        print_r($hasil_potongan);
        echo "</pre>";
        die;
    }

    public function get_potongan_by_kode()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $id_master = $this->input->post('id_master');

        $gaji_pokok = $this->gaji_pokok->get_gaji_pokok_by_pegawai($id_pegawai);
        $nominal = $gaji_pokok->nominal ?? 0;

        $bpjstk_perusahaan   = 0.0024 * $nominal + 0.003 * $nominal + 0.037 * $nominal + (($nominal >= 10547400) ? (0.02 * 10547400) : (0.02 * $nominal));
        $bpjstk_karyawan     = 0.02 * $nominal + (($nominal >= 10547400) ? (0.01 * 10547400) : (0.01 * $nominal));
        $bpjskes_perusahaan  = 0.04 * $nominal;
        $bpjskes_karyawan    = 0.01 * $nominal;

        switch ($id_master) {
            case 'TKD002':
                $potongan = $bpjstk_perusahaan;
                $is_automatic = true;
                break;
            case 'TKD007':
                $potongan = $bpjstk_karyawan;
                $is_automatic = true;
                break;
            case 'TKD003':
                $potongan = $bpjskes_perusahaan;
                $is_automatic = true;
                break;
            case 'TKD008':
                $potongan = $bpjskes_karyawan;
                $is_automatic = true;
                break;
            default:
                $potongan = 0;
                $is_automatic = false; // input manual
        }

        echo json_encode([
            'nominal' => round($potongan),
            'auto'    => $is_automatic
        ]);
    }



    public function simpan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
        }

        $json = file_get_contents('php://input');
        $decoded = json_decode($json, true);
        $excel_data = isset($decoded['data']) ? $decoded['data'] : [];

        // var_dump($excel_data);
        // die;

        if (empty($excel_data) || !is_array($excel_data)) {
            echo json_encode(['status' => 'error', 'message' => 'Data kosong atau tidak valid.']);
            return;
        }

        // Ambil max ID saat ini hanya sekali
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_pegawai_kode, 4) AS UNSIGNED)) AS maxKode FROM pegawai_kode";
        $data = $ci->db->query($query)->row();
        $last_kode = $data->maxKode ?? 0;

        $counter = 1;
        $data_insert = [];

        foreach ($excel_data as $row) {
            $nama_pegawai = trim($row['Pegawai']);
            $nama_insentif = trim($row['Kode Tunjangan']);
            $gaji_pokok = str_replace(['Rp.', '.', ',', ' '], '', $row['Nominal'] ?? '0');

            // Ambil ID pegawai dan tunjangan dari nama
            $ref_pegawai = $this->db->select('id_pegawai')->where('nama', $nama_pegawai)->get('pegawai')->row();
            $ref_master = $this->db->select('id_master')->where('nm_tunjangan', $nama_insentif)->get('master_tunjangan_kode')->row();

            if ($ref_pegawai && $ref_master) {
                $new_id_number = $last_kode + $counter;
                $new_id = 'GP_' . str_pad($new_id_number, 3, '0', STR_PAD_LEFT);
                $counter++;

                $data_insert[] = [
                    'id_pegawai_kode' => $new_id,
                    'id_master' => $ref_master->id_master,
                    'id_pegawai' => $ref_pegawai->id_pegawai,
                    'nominal' => $gaji_pokok
                ];
            }
        }

        if (empty($data_insert)) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau tidak valid.']);
            return;
        }

        $saved = $this->db->insert_batch('pegawai_kode', $data_insert);

        if ($saved) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
        }
    }

    public function insert()
    {
        $this->_validate();

        $id_pegawai = $this->input->post('id_pegawai', true);
        $id_master = $this->input->post('id_master', true);

        if ($this->pegawai_kode->is_duplicate($id_pegawai, $id_master)) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['id_pegawai', 'id_master'],
                'error_string' => ['Kombinasi Pegawai & Kode sudah ada', 'Kombinasi Pegawai & Kode sudah ada']
            ]);
            return;
        }

        $data = [
            'id_pegawai_kode' => $this->generate_id(),
            'id_pegawai' => $id_pegawai,
            'id_master' => $id_master,
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->pegawai_kode->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_pegawai_kode', true);
        $id_pegawai = $this->input->post('id_pegawai', true);
        $id_master = $this->input->post('id_master', true);

        if ($this->pegawai_kode->is_duplicate($id_pegawai, $id_master, $id)) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['id_pegawai', 'id_master'],
                'error_string' => ['Kombinasi Pegawai & Kode sudah ada', 'Kombinasi Pegawai & Kode sudah ada']
            ]);
            return;
        }

        $data = [
            'id_pegawai' => $id_pegawai,
            'id_master' => $id_master,
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->pegawai_kode->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = decrypt_url($this->input->post('id_pegawai_kode'));
        $this->pegawai_kode->delete($id);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->pegawai_kode->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    private function generate_id()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_pegawai_kode, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('pegawai_kode')->row();
        $max_id = $data->max_id + 1;
        return 'PKD' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('id_pegawai') == '') {
            $data['inputerror'][] = 'id_pegawai';
            $data['error_string'][] = 'Pegawai wajib dipilih';
            $data['status'] = FALSE;
        }
        if ($this->input->post('id_master') == '') {
            $data['inputerror'][] = 'id_master';
            $data['error_string'][] = 'Kode wajib dipilih';
            $data['status'] = FALSE;
        }
        if ($this->input->post('nominal') == '' || !is_numeric(str_replace('.', '', $this->input->post('nominal')))) {
            $data['inputerror'][] = 'nominal';
            $data['error_string'][] = 'Nominal wajib diisi dan berupa angka';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
