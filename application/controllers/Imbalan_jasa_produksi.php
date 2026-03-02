<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Imbalan_jasa_produksi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        // $this->load->model('M_tunjangan_hari_raya', 'thr');
        $this->load->model('M_imbalan_jasa_produksi', 'ijp');
        $this->load->model('M_unit_level');
        $this->load->model('M_unit_usaha');
        $this->load->model('M_unit_kerja');
        $this->load->model('M_pegawai');
    }

    public function index()
    {
        $this->data['title'] = 'Master Imbalan Jasa Produksi';
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
        $this->data['m'] = 'Imbalan Jasa Produksi';
        $this->data['ml'] = 'Daftar Imbalan Jasa Produksi';
        $this->data['unit_levels'] = $this->M_unit_level->get_all();
        $this->data['unit_usaha'] = $this->M_unit_usaha->get_all();
        $this->data['unit_kerja'] = $this->M_unit_kerja->get_all();
        $this->data['pegawai'] = $this->M_pegawai->get_all();

        $this->template->load('templates/master', 'admin/master/imbalan_jasa_produksi/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->ijp->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_tunjangan_imbalan) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_tunjangan_imbalan) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->nama;
            $row[] = $item->nm_unit_usaha;
            $row[] = $item->nm_unit_level;
            $row[] = 'Rp. ' . number_format($item->nominal, 0, ',', '.');

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->ijp->count_all(),
            "recordsFiltered" => $this->ijp->count_filtered(),
            "data" => $data,
        ];

        echo json_encode($output);
    }

    private function id_ijpOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_tunjangan_imbalan, 4) AS UNSIGNED)) AS maxKode FROM imbalan_jasa_produksi";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'IJP' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_tunjangan_imbalan' => $this->id_ijpOtomatis(),
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'id_unit_level' => $this->input->post('id_unit_level', true),
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->ijp->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->ijp->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_tunjangan', true);
        $data = [
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'id_unit_level' => $this->input->post('id_unit_level', true),
            'id_pegawai' => $this->input->post('id_pegawai', true),
            'nominal' => $this->input->post('nominal', true),
        ];
        $this->ijp->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_tunjangan'));
        $this->ijp->delete(decrypt_url($id));
        echo json_encode(["status" => TRUE]);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];

        if ($this->input->post('id_unit_usaha') == '') {
            $data['inputerror'][] = 'id_unit_usaha';
            $data['error_string'][] = 'Unit Usaha wajib diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('id_unit_level') == '') {
            $data['inputerror'][] = 'id_unit_level';
            $data['error_string'][] = 'Unit Level wajib diisi';
            $data['status'] = FALSE;
        }

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
        $data = $this->db->get_where('pegawai_penempatan', ['id_pegawai' => $id_pegawai])->row();

        if ($data) {
            echo json_encode([
                'id_unit_usaha' => $data->id_unit_usaha,
                'id_unit_level' => $data->id_unit_level
            ]);
        } else {
            echo json_encode(null);
        }
    }
}
