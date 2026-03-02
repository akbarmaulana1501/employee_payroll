<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kompensasi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        // $this->load->model('M_tunjangan_hari_raya', 'thr');
        $this->load->model('M_kompensasi', 'k');
        $this->load->model('M_unit_level');
        $this->load->model('M_unit_usaha');
        $this->load->model('M_unit_kerja');
        $this->load->model('M_pegawai');
    }

    public function index()
    {
        $this->data['title'] = 'Master Kompensasi';
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
        $this->data['m'] = 'Kompensasi';
        $this->data['ml'] = 'Daftar Kompensasi';
        $this->data['unit_levels'] = $this->M_unit_level->get_all();
        $this->data['unit_usaha'] = $this->M_unit_usaha->get_all();
        $this->data['unit_kerja'] = $this->M_unit_kerja->get_all();
        $this->data['pegawai'] = $this->M_pegawai->get_all();

        $this->template->load('templates/master', 'admin/master/kompensasi/list', $this->data);
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

        $this->template->load('templates/master', 'admin/master/kompensasi/import_excel', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->k->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_kompensasi) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_kompensasi) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->nik;
            $row[] = $item->nama;
            // $row[] = $item->nm_unit_level;
            $row[] = 'Rp. ' . number_format($item->nominal, 0, ',', '.');

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->k->count_all(),
            "recordsFiltered" => $this->k->count_filtered(),
            "data" => $data,
        ];

        echo json_encode($output);
    }

    private function id_kOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_kompensasi, 4) AS UNSIGNED)) AS maxKode FROM kompensasi";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'K' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function simpan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
        }

        $json = file_get_contents('php://input');
        $decoded = json_decode($json, true);
        $excel_data = isset($decoded['data']) ? $decoded['data'] : [];

        if (empty($excel_data) || !is_array($excel_data)) {
            echo json_encode(['status' => 'error', 'message' => 'Data kosong atau tidak valid.']);
            return;
        }

        // Ambil max ID saat ini dari pegawai_perumahan
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_kompensasi, 3) AS UNSIGNED)) AS maxKode FROM kompensasi";
        $data = $ci->db->query($query)->row();
        $last_kode = $data->maxKode ?? 0;

        $counter = 1;
        $data_insert = [];
        $duplicate_data = [];

        foreach ($excel_data as $row) {
            $nik = trim($row['NIK']);
            $nama_pegawai = trim($row['Nama Pegawai']);
            $gaji_pokok = (int) filter_var($row['Nominal'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

            // Ambil ID pegawai
            $ref_pegawai = $this->db
                ->select('id_pegawai,nama,nik')
                ->where('nik', $nik)
                ->get('pegawai')
                ->row();

            if ($ref_pegawai) {
                // Cek apakah NIK sudah ada di tunjangan_penugasan
                $exists = $this->db
                    ->where('nik', $ref_pegawai->nik)
                    ->get('kompensasi')
                    ->num_rows();

                if ($exists > 0) {
                    // Simpan ke daftar duplikat
                    $duplicate_data[] = $ref_pegawai->nik . " - " . $ref_pegawai->nama;
                    continue;
                }

                $new_id_number = $last_kode + $counter;
                $new_id = 'K' . str_pad($new_id_number, 3, '0', STR_PAD_LEFT);
                $counter++;

                $data_insert[] = [
                    'id_kompensasi' => $new_id,
                    'nik' => $ref_pegawai->nik,
                    'id_pegawai'           => $ref_pegawai->id_pegawai,
                    'nominal'              => $gaji_pokok
                ];
            }
        }

        // Jika ada data duplikat, tampilkan pesan error
        if (!empty($duplicate_data)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data sudah ada untuk: ' . implode(', ', $duplicate_data)
            ]);
            return;
        }

        if (empty($data_insert)) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau tidak valid.']);
            return;
        }

        $saved = $this->db->insert_batch('kompensasi', $data_insert);

        if ($saved) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
        }
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_kompensasi' => $this->id_kOtomatis(),
            // 'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'nik' => $this->input->post('nik', true),
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->k->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->k->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_tunjangan', true);
        $data = [
            // 'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'nik' => $this->input->post('nik', true),
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->k->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_tunjangan'));
        $this->k->delete(decrypt_url($id));
        echo json_encode(["status" => TRUE]);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        // if ($this->input->post('id_unit_usaha') == '') {
        //     $data['inputerror'][] = 'id_unit_usaha';
        //     $data['error_string'][] = 'Unit Usaha wajib diisi';
        //     $data['status'] = FALSE;
        // }

        // if ($this->input->post('id_unit_level') == '') {
        //     $data['inputerror'][] = 'id_unit_level';
        //     $data['error_string'][] = 'Unit Level wajib diisi';
        //     $data['status'] = FALSE;
        // }

        if ($this->input->post('id_pegawai') == '') {
            $data['inputerror'][] = 'id_pegawai';
            $data['error_string'][] = 'Pegawai wajib dipilih';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nominal') == '') {
            $data['inputerror'][] = 'nominal';
            $data['error_string'][] = 'Nominal tidak boleh kosong';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function get_penempatan_by_pegawai()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $data = $this->db->get_where('pegawai', ['id_pegawai' => $id_pegawai])->row();

        if ($data) {
            echo json_encode([
                'nik' => $data->nik,
                // 'id_unit_level' => $data->id_unit_level
            ]);
        } else {
            echo json_encode(null);
        }
    }
}
