<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_tunjangan_fungsional extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_tunjangan_fungsional', 'fungsional');
        $this->load->model('M_tunjangan', 'tunjangan');
        $this->load->model('M_unit_usaha', 'unit_usaha');
        $this->load->model('M_eselon', 'eselon');
    }

    public function index()
    {
        $this->data['title'] = 'Tunjangan Fungsional';
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
        $this->data['m'] = 'Tunjangan Fungsional';
        $this->data['ml'] = 'Daftar Tunjangan Fungsional';

        $this->data['list_tunjangan'] = $this->tunjangan->get_all();
        $this->data['list_eselon'] = $this->eselon->get_all();
        $this->data['list_unit_usaha'] = $this->unit_usaha->get_all();

        $this->template->load('templates/master', 'admin/master/fungsional/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->fungsional->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary" onclick="update(\'' . encrypt_url($item->id_tunjangan) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger" onclick="deletedata(\'' . encrypt_url($item->id_tunjangan) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->id_tunjangan;
            $row[] = $item->nama_eselon ?? '-';
            $row[] = $item->jenis_eselon ?? '-';
            $row[] = $item->nama_unit_usaha ?? '-';
            $row[] = 'Rp ' . number_format($item->tunjangan, 0, ',', '.');
            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->fungsional->count_all(),
            "recordsFiltered" => $this->fungsional->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    private function id_tunjanganOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_tunjangan, 3) AS UNSIGNED)) AS maxKode FROM master_tunjangan_fungsional";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'TF' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();

        if ($this->fungsional->is_duplicate(
            $this->input->post('id_eselon'),
            $this->input->post('id_unit_usaha'),
            $this->input->post('id_tunjangan')
        )) {
            echo json_encode([
                'status' => false,
                'inputerror' => ['id_eselon', 'id_unit_usaha'],
                'error_string' => ['Kombinasi sudah ada', 'Kombinasi sudah ada']
            ]);
            return;
        }

        $data = [
            'id_tunjangan' => $this->id_tunjanganOtomatis(),
            'id_eselon' => $this->input->post('id_eselon', true),
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'tunjangan' => $this->input->post('tunjangan', true),
        ];
        $this->fungsional->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->fungsional->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_tunjangan', true);
        $data = [
            'id_eselon' => $this->input->post('id_eselon', true),
            'id_unit_usaha' => $this->input->post('id_unit_usaha', true),
            'tunjangan' => $this->input->post('tunjangan', true),
        ];
        $this->fungsional->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_tunjangan'));
        $this->fungsional->delete(decrypt_url($id));
        echo json_encode(["status" => TRUE]);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];


        if ($this->input->post('id_eselon') == '') {
            $data['inputerror'][] = 'id_eselon';
            $data['error_string'][] = 'Eselon harus dipilih';
            $data['status'] = FALSE;
        }
        if ($this->input->post('id_unit_usaha') == '') {
            $data['inputerror'][] = 'id_unit_usaha';
            $data['error_string'][] = 'Unit Usaha tidak boleh kosong';
            $data['status'] = FALSE;
        }
        if ($this->input->post('tunjangan') == '') {
            $data['inputerror'][] = 'tunjangan';
            $data['error_string'][] = 'Jumlah tunjangan tidak boleh kosong';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
