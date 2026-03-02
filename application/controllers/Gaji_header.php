<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gaji_header extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_gaji_header', 'gaji_header');
        $this->load->model('M_unit_kerja', 'unit_kerja');
        $this->load->model('M_pegawai_penempatan', 'pegawai_penempatan');
        $this->load->model('M_gaji_detail', 'gaji_detail');
    }

    public function index()
    {
        $this->data['title'] = 'Data Gaji Header';

        $this->data['unit_kerja'] = $this->unit_kerja->get_all();

        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="add()">
                    <i class="fe-plus-square"></i> Create
                </button>
                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="reload_table()">
                    <i class="fe-refresh-cw"></i> Reload
                </button>
            </h4>';

        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['delete'] = 'Delete';
        $this->data['m'] = 'Gaji Header';
        $this->data['ml'] = 'Daftar Gaji Header';

        $this->template->load('templates/master', 'admin/gaji/header/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->gaji_header->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
    <div class="action-buttons">
        <div class="action-buttons-row">
            <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_gaji_header) . '\')">
                <i class="fas fa-edit"></i> Update
            </a>
            <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_gaji_header) . '\')">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>
        <div class="action-buttons-row">
            <a href="javascript:void(0)" title="Lihat Detail" class="btn-xs btn-info waves-effect waves-light" onclick="viewDetail(\'' . encrypt_url($item->id_gaji_header) . '\')">
                <i class="fas fa-list"></i> Detail Gaji
            </a>
            <a href="javascript:void(0)" title="Lihat Potongan" class="btn-xs btn-warning waves-effect waves-light" onclick="viewPotonganDetail(\'' . encrypt_url($item->id_gaji_header) . '\')">
                <i class="fas fa-money-bill-wave"></i> Potongan
            </a>
        </div>
    </div>
';

            // <div class="action-buttons-row">
            // <a href="javascript:void(0)" title="Tambah Potongan" class="btn-xs btn-success waves-effect waves-light" onclick="addPotongan(\'' . encrypt_url($item->id_gaji_header) . '\')">
            //     <i class="fas fa-plus-circle"></i> Tambah Potongan Opsional
            // </a>
            // </div>

            $row[] = $item->nama;
            $row[] = $item->periode;
            $row[] = number_format($item->total_pendapatan_tetap, 2, ',', '.');
            $row[] = number_format($item->total_pendapatan_kotor, 2, ',', '.');
            $row[] = number_format($item->total_potongan, 2, ',', '.');
            $row[] = number_format($item->gaji_bersih, 2, ',', '.');
            $row[] = $item->status_konfirmasi;
            $row[] = $item->created_at;

            $data[] = $row;
        }
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->gaji_header->count_all(),
            "recordsFiltered" => $this->gaji_header->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }




    private function id_gaji_headerOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_gaji_header, 4) AS UNSIGNED)) AS maxKode FROM gaji_header";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'GH_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();

        $id_gaji_header = $this->id_gaji_headerOtomatis();
        $id_pegawai = $this->input->post('id_pegawai', true);
        $periode = $this->input->post('periode', true);
        $status_konfirmasi = $this->input->post('status_konfirmasi', true);

        $this->db->trans_begin();
        $data_header = [
            'id_gaji_header' => $id_gaji_header,
            'id_pegawai' => $id_pegawai,
            'periode' => $periode,
            'status_konfirmasi' => $status_konfirmasi,
        ];
        $this->gaji_header->insert($data_header);

        $generate_detail = $this->gaji_detail->generate_gaji_detail_otomatis($id_gaji_header, $id_pegawai);

        if (!$generate_detail) {
            $this->db->trans_rollback();
            echo json_encode([
                "status" => false,
                "error" => "Gagal generate detail gaji, pastikan data pegawai valid."
            ]);
            return;
        }

        $this->gaji_header->update_gaji_summary($id_gaji_header);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                "status" => false,
                "error" => "Gagal menyimpan data."
            ]);
        } else {
            $this->db->trans_commit();
            echo json_encode(["status" => TRUE]);
        }
    }


    public function get_by_id($id)
    {
        $data = $this->gaji_header->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_gaji_header', true);
        $data = [
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'periode' => $this->input->post('periode', true),
            'status_konfirmasi' => $this->input->post('status_konfirmasi', true),
        ];
        $this->gaji_header->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }
    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_gaji_header'));
        $this->gaji_header->delete(decrypt_url($id));
        echo json_encode(array("status" => TRUE));
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];
        if ($this->input->post('id_pegawai') == '') {
            $data['inputerror'][] = 'id_pegawai';
            $data['error_string'][] = 'Pegawai harus dipilih';
            $data['status'] = FALSE;
        }
        if ($this->input->post('periode') == '') {
            $data['inputerror'][] = 'periode';
            $data['error_string'][] = 'Periode harus diisi';
            $data['status'] = FALSE;
        }
        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function get_pegawai_by_unit_kerja()
    {
        $id_unit_kerja = $this->input->post('id_unit_kerja');
        $data = $this->pegawai_penempatan->get_pegawai_by_unit_kerja($id_unit_kerja);
        echo json_encode($data);
    }
}


    // public function get_unit_usaha_by_bisnis()
    // {
    //     $id_unit_bisnis = $this->input->post('id_unit_bisnis');
    //     $data = $this->pegawai_penempatan->get_unit_usaha_by_bisnis($id_unit_bisnis);
    //     echo json_encode($data);
    // }

    // public function get_unit_organisasi_by_usaha()
    // {
    //     $id_unit_usaha = $this->input->post('id_unit_usaha');
    //     $data = $this->pegawai_penempatan->get_unit_organisasi_by_usaha($id_unit_usaha);
    //     echo json_encode($data);
    // }

    // public function get_unit_kerja_by_organisasi()
    // {
    //     $id_unit_organisasi = $this->input->post('id_unit_organisasi');
    //     $data = $this->pegawai_penempatan->get_unit_kerja_by_organisasi($id_unit_organisasi);
    //     echo json_encode($data);
    // }