<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_potongan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_potongan', 'potongan');
    }

    public function index()
    {
        $this->data['title'] = 'Master Potongan';
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
        $this->data['m'] = 'Master Potongan';
        $this->data['ml'] = 'Daftar Potongan';

        $this->template->load('templates/master', 'admin/master/potongan/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->potongan->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <a href="javascript:void(0)" title="Update" class="btn-xs btn-primary waves-effect waves-light" onclick="update(\'' . encrypt_url($item->id_potongan) . '\')">
                    <i class="fas fa-edit"></i> Update
                </a> 
                <a href="javascript:void(0)" title="Delete" class="btn-xs btn-danger waves-effect waves-light" onclick="deletedata(\'' . encrypt_url($item->id_potongan) . '\')">
                    <i class="fas fa-trash"></i> Delete
                </a>';
            $row[] = $item->nama_potongan;
            $row[] = ucfirst($item->tipe_potongan);
            $row[] = ($item->tipe_potongan == 'persen') ? number_format($item->persentase, 2, ',', '.') . ' %' : number_format($item->persentase, 2, ',', '.');
            $row[] = $item->maksimal !== null ? number_format($item->maksimal, 2, ',', '.') : '-';
            $data[] = $row;
        }
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->potongan->count_all(),
            "recordsFiltered" => $this->potongan->count_filtered(),
            "data" => $data,
        ];
        echo json_encode($output);
    }

    private function id_potonganOtomatis()
    {
        $ci = get_instance();
        $query = "SELECT MAX(CAST(SUBSTR(id_potongan, 4) AS UNSIGNED)) AS maxKode FROM master_potongan";
        $data = $ci->db->query($query)->row();
        $kode = $data->maxKode;
        $kodeBaru = $kode + 1;
        return 'PT_' . str_pad($kodeBaru, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_potongan' => $this->id_potonganOtomatis(),
            'nama_potongan' => $this->input->post('nama_potongan', true),
            'tipe_potongan' => $this->input->post('tipe_potongan', true),
            'persentase' => $this->input->post('persentase', true),
            'maksimal' => $this->input->post('maksimal', true) ?: null,
        ];
        $this->potongan->insert($data);
        echo json_encode(["status" => TRUE]);
    }

    public function get_by_id($id)
    {
        $data = $this->potongan->get_by_id(decrypt_url($id));
        echo json_encode($data);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_potongan', true);
        $data = [
            'nama_potongan' => $this->input->post('nama_potongan', true),
            'tipe_potongan' => $this->input->post('tipe_potongan', true),
            'persentase' => $this->input->post('persentase', true),
            'maksimal' => $this->input->post('maksimal', true) ?: null,
        ];
        $this->potongan->update($id, $data);
        echo json_encode(["status" => TRUE]);
    }

    public function delete()
    {
        $id = str_replace("'", "", $this->input->post('id_potongan'));
        $this->potongan->delete(decrypt_url($id));
        echo json_encode(array("status" => TRUE));
    }


    private function _validate()
    {
        $data = ["error_string" => [], "inputerror" => [], "status" => TRUE];
        if ($this->input->post('nama_potongan') == '') {
            $data['inputerror'][] = 'nama_potongan';
            $data['error_string'][] = 'Nama potongan tidak boleh kosong';
            $data['status'] = FALSE;
        }
        if ($this->input->post('tipe_potongan') == '') {
            $data['inputerror'][] = 'tipe_potongan';
            $data['error_string'][] = 'Tipe potongan harus dipilih';
            $data['status'] = FALSE;
        }
        // if ($this->input->post('persentase') == '') {
        //     $data['inputerror'][] = 'persentase';
        //     $data['error_string'][] = 'Persentase tidak boleh kosong';
        //     $data['status'] = FALSE;
        // }
        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
