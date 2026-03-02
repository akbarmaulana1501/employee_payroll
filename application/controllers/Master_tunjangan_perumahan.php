<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_tunjangan_perumahan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->model('M_tunjangan_perumahan', 'perumahan');
        $this->load->model('M_eselon', 'eselon');
    }

    public function index()
    {
        $this->data['title'] = 'Tunjangan Perumahan';
        $this->data['TombolCreate'] = '
            <h4 class="page-title">
                <button type="button" class="btn btn-sm btn-primary" onclick="add()"><i class="fe-plus-square"></i> Create</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="reload_table()"><i class="fe-refresh-cw"></i> Reload</button>
            </h4>';
        $this->data['create'] = 'Create';
        $this->data['edit'] = 'Update';
        $this->data['m'] = 'Tunjangan Perumahan';

        $this->data['list_eselon'] = $this->eselon->get_all();

        $this->template->load('templates/master', 'admin/master/perumahan/list', $this->data);
    }

    public function ajax_list()
    {
        $list = $this->perumahan->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = '
                <button class="btn btn-sm btn-primary" onclick="update(\'' . $item->id_tunjangan . '\')">Update</button>
                <button class="btn btn-sm btn-danger" onclick="deletedata(\'' . $item->id_tunjangan . '\')">Delete</button>';
            $row[] = $item->id_tunjangan;
            $row[] = $item->id_eselon;
            $row[] = 'Rp ' . number_format($item->tunjangan, 0, ',', '.');
            $data[] = $row;
        }

        echo json_encode([
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->perumahan->count_all(),
            "recordsFiltered" => $this->perumahan->count_filtered(),
            "data" => $data,
        ]);
    }

    private function generate_id()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_tunjangan, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('master_tunjangan_perumahan')->row();
        $max_id = $data->max_id + 1;
        return 'TPR' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }

    public function insert()
    {
        $this->_validate();
        $data = [
            'id_tunjangan' => $this->generate_id(),
            'id_eselon' => $this->input->post('id_eselon'),
            'tunjangan' => $this->input->post('tunjangan'),
        ];
        $this->perumahan->insert($data);
        echo json_encode(["status" => true]);
    }

    public function update()
    {
        $this->_validate();
        $id = $this->input->post('id_tunjangan');
        $data = [
            'id_eselon' => $this->input->post('id_eselon'),
            'tunjangan' => $this->input->post('tunjangan'),
        ];
        $this->perumahan->update($id, $data);
        echo json_encode(["status" => true]);
    }

    public function delete()
    {
        $id = $this->input->post('id_tunjangan');
        $this->perumahan->delete($id);
        echo json_encode(["status" => true]);
    }

    public function get_by_id($id)
    {
        echo json_encode($this->perumahan->get_by_id($id));
    }

    private function _validate()
    {
        $data = ['status' => TRUE, 'inputerror' => [], 'error_string' => []];

        if ($this->input->post('id_eselon') == '') {
            $data['inputerror'][] = 'id_eselon';
            $data['error_string'][] = 'Eselon wajib dipilih';
            $data['status'] = FALSE;
        }

        if ($this->input->post('tunjangan') == '') {
            $data['inputerror'][] = 'tunjangan';
            $data['error_string'][] = 'Jumlah tunjangan wajib diisi';
            $data['status'] = FALSE;
        }

        if (!$data['status']) {
            echo json_encode($data);
            exit;
        }
    }
}
