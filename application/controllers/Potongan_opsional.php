<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Potongan_opsional extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_potongan_opsional', 'potongan_opsional');
    }

    public function index()
    {
        $this->data['title'] = 'Data Potongan Opsional';
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
        $this->data['m'] = 'Potongan Opsional';
        $this->data['ml'] = 'Daftar Potongan Opsional';

        $this->template->load('templates/master', 'admin/potongan_opsional/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->potongan_opsional->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\''.encrypt_url($item->id_potongan_opsional).'\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\''.encrypt_url($item->id_potongan_opsional).'\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->id_gaji_header;
            $row[] = $item->nama_potongan;
            $row[] = number_format($item->jumlah, 2, ',', '.');
            $data[] = $row;
        }
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->potongan_opsional->count_all(),
            "recordsFiltered" => $this->potongan_opsional->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    private function id_potongan_opsionalOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_potongan_opsional, 4) AS UNSIGNED)) AS maxKode FROM potongan_opsional";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'PO_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_potongan_opsional' => $this->id_potongan_opsionalOtomatis(),
            'id_gaji_header' => $this->input->post('id_gaji_header', true),
            'nama_potongan' => $this->input->post('nama_potongan', true),
            'jumlah' => $this->input->post('jumlah', true),
        ];
        $this->potongan_opsional->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->potongan_opsional->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_potongan_opsional', true);
        $data = [
            'id_gaji_header' => $this->input->post('id_gaji_header', true),
            'nama_potongan' => $this->input->post('nama_potongan', true),
            'jumlah' => $this->input->post('jumlah', true),
        ];
        $this->potongan_opsional->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = decrypt_url($this->input->post('id', true));
        $this->potongan_opsional->delete($id);
        echo json_encode(["status" => TRUE]);
    }

    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];
        if ($this->input->post('id_gaji_header') == '') {
            $data['inputerror'][] = 'id_gaji_header';
            $data['error_string'][] = 'ID Gaji Header harus diisi';
            $data['status'] = FALSE;
        }
        if ($this->input->post('nama_potongan') == '') {
            $data['inputerror'][] = 'nama_potongan';
            $data['error_string'][] = 'Nama Potongan harus diisi';
            $data['status'] = FALSE;
        }
        if ($this->input->post('jumlah') == '') {
            $data['inputerror'][] = 'jumlah';
            $data['error_string'][] = 'Jumlah harus diisi';
            $data['status'] = FALSE;
        }
        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
