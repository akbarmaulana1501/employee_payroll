<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pegawai_penggajian extends CI_Model
{
    var $table = 'pegawai_penggajian';
    var $table2 = 'pegawai_penggajian2';
    var $table_insentif = 'pegawai_penggajian_insentif';
    var $table_kode = 'pegawai_penggajian_kode';
    var $table_potongan = 'pegawai_potongan2';
    var $column_order = ['id_pegawai_penggajian', 'pegawai.nama', 'fungsional.tunjangan', 'kesra.tunjangan'];
    var $column_search = ['pegawai_penggajian.id_pegawai_penggajian', 'pegawai.nama'];
    var $order = ['id_pegawai_penggajian' => 'asc'];

    // private function _get_datatables_query($status_konfirmasi = null)
    // {
    //     $this->db->select(
    //         'pegawai_penggajian.*,
    //     pegawai.nama,
    //     gaji.nominal as gaji_pokok,
    //     fungsional.tunjangan as fungsional,
    //     kesra.tunjangan as kesra,
    //     komunikasi.tunjangan as komunikasi,
    //     daerah.tunjangan as daerah,
    //     radiasi.tunjangan as radiasi,
    //     perumahan.tunjangan as perumahan,
    //     pegawai_penggajian.status_konfirmasi,
    //     (
    //         SELECT COALESCE(SUM(ppk.nominal), 0)
    //         FROM pegawai_penggajian_kode ppk
    //         WHERE ppk.id_pegawai_penggajian = pegawai_penggajian.id_pegawai_penggajian
    //     ) as jml_tunjangan_kode,
    //     (
    //         SELECT COALESCE(SUM(ppi.nominal), 0)
    //         FROM pegawai_penggajian_insentif ppi
    //         WHERE ppi.id_pegawai_penggajian = pegawai_penggajian.id_pegawai_penggajian
    //     ) as jml_tunjangan_insentif'
    //     );

    //     $this->db->from($this->table); // 'pegawai_penggajian'
    //     $this->db->join('pegawai', 'pegawai.id_pegawai = pegawai_penggajian.id_pegawai', 'left');
    //     $this->db->join('master_gaji_pokok gaji', 'gaji.id_gaji_pokok = pegawai_penggajian.id_gaji_pokok', 'left');
    //     $this->db->join('master_tunjangan_fungsional fungsional', 'fungsional.id_tunjangan = pegawai_penggajian.id_tunjangan_fungsional', 'left');
    //     $this->db->join('master_tunjangan_kesra kesra', 'kesra.id_tunjangan = pegawai_penggajian.id_tunjangan_kesra', 'left');
    //     $this->db->join('master_tunjangan_komunikasi komunikasi', 'komunikasi.id_tunjangan = pegawai_penggajian.id_tunjangan_komunikasi', 'left');
    //     $this->db->join('master_tunjangan_daerah daerah', 'daerah.id_tunjangan = pegawai_penggajian.id_tunjangan_daerah', 'left');
    //     $this->db->join('master_tunjangan_radiasi radiasi', 'radiasi.id_tunjangan = pegawai_penggajian.id_tunjangan_radiasi', 'left');
    //     $this->db->join('master_tunjangan_perumahan perumahan', 'perumahan.id_tunjangan = pegawai_penggajian.id_tunjangan_perumahan', 'left');

    //     $tahun = $this->input->post('tahun');
    //     $bulan = $this->input->post('bulan');
    //     if ($tahun) {
    //         $this->db->where('pegawai_penggajian.tahun', $tahun);
    //     }
    //     if ($bulan) {
    //         $this->db->where('pegawai_penggajian.bulan', $bulan);
    //     }

    //     if ($status_konfirmasi !== null) {
    //         $this->db->where('pegawai_penggajian.status_konfirmasi', $status_konfirmasi);
    //     }

    //     $search = $_POST['search']['value'] ?? null;
    //     $i = 0;
    //     foreach ($this->column_search as $item) {
    //         if ($search) {
    //             if ($i === 0) {
    //                 $this->db->group_start();
    //                 $this->db->like($item, $search);
    //             } else {
    //                 $this->db->or_like($item, $search);
    //             }
    //             if (count($this->column_search) - 1 == $i)
    //                 $this->db->group_end();
    //         }
    //         $i++;
    //     }

    //     if (isset($_POST['order'])) {
    //         $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
    //     } else {
    //         $this->db->order_by(key($this->order), $this->order[key($this->order)]);
    //     }
    // }

    private function _get_datatables_query($status_konfirmasi = null, $tahun = null, $bulan = null, $start_month = null, $end_month = null)
    {
        $this->db->select('
        pegawai_penggajian.*,
        pegawai.nama,
        gaji.nominal as gaji_pokok,
        fungsional.tunjangan as fungsional,
        kesra.tunjangan as kesra,
        komunikasi.tunjangan as komunikasi,
        daerah.tunjangan as daerah,
        radiasi.tunjangan as radiasi,
        perumahan.tunjangan as perumahan,
        penugasan.nominal as nominal_penugasan,
        kasir.nominal as nominal_kasir,
        transport.nominal as nominal_transport,
        kompensasi.nominal as nominal_kompensasi,
        mthr.nominal as nominal_thr,
        ijp.nominal as nominal_bonus,
        pegawai_penggajian.status_konfirmasi,
        (SELECT COALESCE(SUM(ppk.nominal),0) FROM pegawai_penggajian_kode ppk WHERE ppk.id_pegawai_penggajian = pegawai_penggajian.id_pegawai_penggajian) as jml_tunjangan_kode,
        (SELECT COALESCE(SUM(ppi.nominal),0) FROM pegawai_penggajian_insentif ppi WHERE ppi.id_pegawai_penggajian = pegawai_penggajian.id_pegawai_penggajian) as jml_tunjangan_insentif
        ');

        $this->db->from($this->table);
        $this->db->join('pegawai', 'pegawai.id_pegawai = pegawai_penggajian.id_pegawai', 'left');
        $this->db->join('master_gaji_pokok gaji', 'gaji.id_gaji_pokok = pegawai_penggajian.id_gaji_pokok', 'left');
        $this->db->join('master_tunjangan_fungsional fungsional', 'fungsional.id_tunjangan = pegawai_penggajian.id_tunjangan_fungsional', 'left');
        $this->db->join('master_tunjangan_kesra kesra', 'kesra.id_tunjangan = pegawai_penggajian.id_tunjangan_kesra', 'left');
        $this->db->join('master_tunjangan_komunikasi komunikasi', 'komunikasi.id_tunjangan = pegawai_penggajian.id_tunjangan_komunikasi', 'left');
        $this->db->join('master_tunjangan_daerah daerah', 'daerah.id_tunjangan = pegawai_penggajian.id_tunjangan_daerah', 'left');
        $this->db->join('master_tunjangan_radiasi radiasi', 'radiasi.id_tunjangan = pegawai_penggajian.id_tunjangan_radiasi', 'left');
        $this->db->join('master_tunjangan_perumahan perumahan', 'perumahan.id_tunjangan = pegawai_penggajian.id_tunjangan_perumahan', 'left');
        $this->db->join('tunjangan_penugasan penugasan', 'penugasan.id_tunjangan_penugasan = pegawai_penggajian.id_tunjangan_penugasan', 'left');
        $this->db->join('tunjangan_kasir kasir', 'kasir.id_tunjangan_kasir = pegawai_penggajian.id_tunjangan_kasir', 'left');
        $this->db->join('tunjangan_transport transport', 'transport.id_tunjangan_transport = pegawai_penggajian.id_tunjangan_transport', 'left');
        $this->db->join('kompensasi kompensasi', 'kompensasi.id_kompensasi = pegawai_penggajian.id_kompensasi', 'left');
        $this->db->join('imbalan_jasa_produksi ijp', 'ijp.id_pegawai = pegawai_penggajian.id_pegawai', 'left');
        $this->db->join('master_tunjangan_hari_raya mthr', 'mthr.id_pegawai = pegawai_penggajian.id_pegawai', 'left');

        // Filter range bulan-tahun
        if (!empty($start_month) && !empty($end_month)) {
            $start_val = (int) date('Ym', strtotime($start_month . '-01'));
            $end_val   = (int) date('Ym', strtotime($end_month . '-01'));

            $this->db->where("
            CAST(CONCAT(pegawai_penggajian.tahun, LPAD(pegawai_penggajian.bulan, 2, '0')) AS UNSIGNED) >= {$start_val}
            AND CAST(CONCAT(pegawai_penggajian.tahun, LPAD(pegawai_penggajian.bulan, 2, '0')) AS UNSIGNED) <= {$end_val}
        ");
        } else {
            if ($tahun) {
                $this->db->where('pegawai_penggajian.tahun', $tahun);
            }
            if ($bulan) {
                $this->db->where('pegawai_penggajian.bulan', $bulan);
            }
        }

        if ($status_konfirmasi !== null) {
            $this->db->where('pegawai_penggajian.status_konfirmasi', $status_konfirmasi);
        }

        // Search
        $search = $_POST['search']['value'] ?? null;
        $i = 0;
        foreach ($this->column_search as $item) {
            if ($search) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search);
                } else {
                    $this->db->or_like($item, $search);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        // Order
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        } else {
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }
    }



    // public function get_datatables($status_konfirmasi = 0)
    // {
    //     $this->_get_datatables_query($status_konfirmasi);
    //     $length = isset($_POST['length']) ? $_POST['length'] : -1;
    //     $start  = isset($_POST['start']) ? $_POST['start'] : 0;
    //     if ($length != -1) {
    //         $this->db->limit($length, $start);
    //     }
    //     return $this->db->get()->result();
    // }

    public function get_datatables($status_konfirmasi = 0, $tahun = null, $bulan = null, $start_date = null, $end_date = null)
    {
        $this->_get_datatables_query($status_konfirmasi, $tahun, $bulan, $start_date, $end_date);

        $length = isset($_POST['length']) ? $_POST['length'] : -1;
        $start  = isset($_POST['start']) ? $_POST['start'] : 0;
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        return $this->db->get()->result();
    }

    public function get_data_penggajian2()
    {
        $this->db->select('*');
        $this->db->from('pegawai_penggajian2');
        return $this->db->get()->result();
    }

    // public function generate_all_penggajian($ids)
    // {
    //     $this->db->select('
    //     pp.id_pegawai,
    //     mgp.nominal,
    //     mtf.tunjangan AS tunjangan_fungsional,
    //     mtk.tunjangan AS tunjangan_kesra,
    //     mtkm.tunjangan AS tunjangan_komunikasi,
    //     mtd.tunjangan AS tunjangan_daerah,
    //     mtr.tunjangan AS tunjangan_radiasi,
    //     mtp.tunjangan AS tunjangan_perumahan,
    //     tp.nominal AS tunjangan_penugasan,
    //     tk.nominal AS tunjangan_kasir,
    //     tt.nominal AS tunjangan_transport,
    //     k.nominal AS kompensasi
    // ');
    //     $this->db->from('pegawai_penggajian pp');
    //     $this->db->join('pegawai p', 'p.id_pegawai = pp.id_pegawai', 'left');
    //     $this->db->join('master_gaji_pokok mgp', 'mgp.id_gaji_pokok = pp.id_gaji_pokok', 'left');
    //     $this->db->join('master_tunjangan_fungsional mtf', 'mtf.id_tunjangan = pp.id_tunjangan_fungsional', 'left');
    //     $this->db->join('master_tunjangan_kesra mtk', 'mtk.id_tunjangan = pp.id_tunjangan_kesra', 'left');
    //     $this->db->join('master_tunjangan_komunikasi mtkm', 'mtkm.id_tunjangan = pp.id_tunjangan_komunikasi', 'left');
    //     $this->db->join('master_tunjangan_daerah mtd', 'mtd.id_tunjangan = pp.id_tunjangan_daerah', 'left');
    //     $this->db->join('master_tunjangan_radiasi mtr', 'mtr.id_tunjangan = pp.id_tunjangan_radiasi', 'left');
    //     $this->db->join('master_tunjangan_perumahan mtp', 'mtp.id_tunjangan = pp.id_tunjangan_perumahan', 'left');
    //     $this->db->join('tunjangan_penugasan tp', 'tp.id_tunjangan_penugasan = pp.id_tunjangan_penugasan', 'left');
    //     $this->db->join('tunjangan_kasir tk', 'tk.id_tunjangan_kasir = pp.id_tunjangan_kasir', 'left');
    //     $this->db->join('tunjangan_transport tt', 'tt.id_tunjangan_transport = pp.id_tunjangan_transport', 'left');
    //     $this->db->join('kompensasi k', 'k.id_kompensasi = pp.id_kompensasi', 'left');

    //     $this->db->where_in('p.id_pegawai', $ids);

    //     return $this->db->get()->result_array();
    // }

    public function generate_gaji($id_pegawai)
    {
        $this->db->select('*');
        $this->db->from('pegawai');

        if (is_array($id_pegawai)) {
            $this->db->where_in('id_pegawai', $id_pegawai);
        } else {
            $this->db->where('id_pegawai', $id_pegawai);
        }

        return $this->db->get()->result();
    }





    // public function generate_all_penggajian($id_pegawai)
    // {
    //     // Pastikan input berupa array
    //     if (!is_array($id_pegawai)) {
    //         $id_pegawai = [$id_pegawai];
    //     }

    //     $results = [];

    //     foreach ($id_pegawai as $id) {
    //         // 1) pastikan punya gaji pokok
    //         $gaji_pokok_row = $this->db->get_where('master_gaji_pokok', [
    //             'id_pegawai' => $id
    //         ])->row();

    //         if (!$gaji_pokok_row || (float)$gaji_pokok_row->nominal <= 0) {
    //             $results[] = [
    //                 'id_pegawai' => $id,
    //                 'status'     => false,
    //                 'message'    => 'Pegawai belum punya gaji pokok'
    //             ];
    //             continue;
    //         }

    //         // 2) tentukan bulan/tahun
    //         $bulan = date('m');
    //         $tahun = date('Y');

    //         // 3) cek duplikasi
    //         $cek = $this->db->get_where('pegawai_penggajian', [
    //             'id_pegawai' => $id,
    //             'bulan'      => $bulan,
    //             'tahun'      => $tahun
    //         ])->row();
    //         if ($cek) {
    //             $results[] = [
    //                 'id_pegawai' => $id,
    //                 'status'     => false,
    //                 'message'    => 'Data sudah ada untuk bulan & tahun ini'
    //             ];
    //             continue;
    //         }

    //         // 4) ambil master tunjangan
    //         $fungsional = $this->db->get_where('master_tunjangan_fungsional', ['id_tunjangan' => $id])->row();
    //         $kesra      = $this->db->get_where('master_tunjangan_kesra', ['id_tunjangan' => $id])->row();
    //         $komunikasi = $this->db->get_where('master_tunjangan_komunikasi', ['id_tunjangan' => $id])->row();
    //         $daerah     = $this->db->get_where('master_tunjangan_daerah', ['id_tunjangan' => $id])->row();
    //         $radiasi    = $this->db->get_where('master_tunjangan_radiasi', ['id_tunjangan' => $id])->row();
    //         $perumahan  = $this->db->get_where('master_tunjangan_perumahan', ['id_tunjangan' => $id])->row();
    //         $penugasan  = $this->db->get_where('tunjangan_penugasan',     ['id_tunjangan_penugasan' => $id])->row();
    //         $kasir      = $this->db->get_where('tunjangan_kasir',         ['id_tunjangan_kasir' => $id])->row();
    //         $transport  = $this->db->get_where('tunjangan_transport',     ['id_tunjangan_transport' => $id])->row();
    //         $kompensasi = $this->db->get_where('kompensasi',              ['id_kompensasi' => $id])->row();

    //         $id_penggajian = $this->generate_id();

    //         // 5) transaksi
    //         $this->db->trans_start();

    //         // 6) insert header
    //         $data = [
    //             'id_pegawai_penggajian' => $id_penggajian,
    //             'id_pegawai'            => $id,
    //             'bulan'                 => $bulan,
    //             'tahun'                 => $tahun,
    //             'gaji_pokok'            => (float)$gaji_pokok_row->nominal,
    //             'tunjangan_fungsional'  => (float)($fungsional->tunjangan ?? 0),
    //             'tunjangan_kesra'       => (float)($kesra->tunjangan ?? 0),
    //             'tunjangan_komunikasi'  => (float)($komunikasi->tunjangan ?? 0),
    //             'tunjangan_daerah'      => (float)($daerah->tunjangan ?? 0),
    //             'tunjangan_radiasi'     => (float)($radiasi->tunjangan ?? 0),
    //             'tunjangan_perumahan'   => (float)($perumahan->tunjangan ?? 0),
    //             'tunjangan_penugasan'   => (float)($penugasan->tunjangan ?? 0),
    //             'tunjangan_kasir'       => (float)($kasir->tunjangan ?? 0),
    //             'tunjangan_transport'   => (float)($transport->tunjangan ?? 0),
    //             'tunjangan_kompensasi'  => (float)($kompensasi->tunjangan ?? 0),
    //             'status_konfirmasi'     => 0,
    //         ];
    //         $this->db->insert('pegawai_penggajian2', $data);

    //         // 7) detail insentif
    //         $insentifList = $this->db->get_where('master_tunjangan_insentif', ['id_tunjangan' => $id])->result();
    //         if ($insentifList) {
    //             $batch = [];
    //             foreach ($insentifList as $row) {
    //                 $batch[] = [
    //                     'id_pegawai_penggajian' => $id_penggajian,
    //                     'id_tunjangan_insentif'  => $row->id_tunjangan_insentif,
    //                     'nominal'                => (float)$row->nominal,
    //                 ];
    //             }
    //             if ($batch) {
    //                 $this->db->insert_batch('pegawai_penggajian_insentif', $batch);
    //             }
    //         }

    //         // 8) detail kode
    //         $kodeRows = $this->db->get_where('pegawai_kode', ['id_pegawai' => $id])->result();
    //         if ($kodeRows) {
    //             $kodeBatch = [];
    //             foreach ($kodeRows as $r) {
    //                 $kodeBatch[] = [
    //                     'id_pegawai_penggajian' => $id_penggajian,
    //                     'id_tunjangan_kode'     => $r->id_master,
    //                     'nominal'               => (float)$r->nominal,
    //                 ];
    //             }
    //             if ($kodeBatch) {
    //                 $this->db->insert_batch('pegawai_penggajian_kode', $kodeBatch);
    //             }
    //         }

    //         // 9) potongan
    //         $potonganBatch = [];
    //         $potonganList = $this->db->get('master_potongan')->result();
    //         $zis = $this->db->get_where('master_zis', ['id_pegawai' => $id])->row();
    //         $iuran_sertifikat = $this->db->get_where('master_iuran_sertifikat', ['id_pegawai' => $id])->row();

    //         foreach ($potonganList as $pot) {
    //             $nilai = 0;
    //             if (strtolower($pot->tipe_potongan) === 'fixed') {
    //                 $nilai = (float)$pot->persentase;
    //             } elseif (strtolower($pot->tipe_potongan) === 'persen') {
    //                 $nilai = ((float)$pot->persentase / 100) * (float)$gaji_pokok_row->nominal;
    //             }
    //             if ($pot->maksimal !== null && $nilai > (float)$pot->maksimal) {
    //                 $nilai = (float)$pot->maksimal;
    //             }
    //             $potonganBatch[] = [
    //                 'id_pegawai_penggajian' => $id_penggajian,
    //                 'id_potongan'           => $pot->id_potongan,
    //                 'nominal'               => $nilai,
    //             ];
    //         }

    //         if ($zis && (float)$zis->nominal > 0) {
    //             $id_pot_zis = $this->db->get_where('master_potongan', ['nama_potongan' => 'ZIS'])->row('id_potongan');
    //             if ($id_pot_zis) {
    //                 $potonganBatch[] = [
    //                     'id_pegawai_penggajian' => $id_penggajian,
    //                     'id_potongan'           => $id_pot_zis,
    //                     'nominal'               => (float)$zis->nominal,
    //                 ];
    //             }
    //         }

    //         if ($iuran_sertifikat && (float)$iuran_sertifikat->nominal > 0) {
    //             $id_pot_sertif = $this->db->get_where('master_potongan', ['nama_potongan' => 'Iuran Sertifikat'])->row('id_potongan');
    //             if ($id_pot_sertif) {
    //                 $potonganBatch[] = [
    //                     'id_pegawai_penggajian' => $id_penggajian,
    //                     'id_potongan'           => $id_pot_sertif,
    //                     'nominal'               => (float)$iuran_sertifikat->nominal,
    //                 ];
    //             }
    //         }

    //         if ($potonganBatch) {
    //             $this->db->insert_batch('master_potongan', $potonganBatch);
    //         }

    //         // 10) commit
    //         $this->db->trans_complete();
    //         if ($this->db->trans_status() === false) {
    //             $results[] = [
    //                 'id_pegawai' => $id,
    //                 'status'     => false,
    //                 'message'    => 'Gagal menyimpan (rollback)'
    //             ];
    //             continue;
    //         }

    //         $results[] = [
    //             'id_pegawai'    => $id,
    //             'status'        => true,
    //             'message'       => 'Penggajian berhasil digenerate',
    //             'id_penggajian' => $id_penggajian
    //         ];
    //     }

    //     return $results;
    // }

    // Ambil daftar penggajian yang sudah terkonfirmasi
    public function get_datatables_terkonfirmasi($tahun = null, $bulan = null, $start_date = null, $end_date = null)
    {
        $this->db->select('pt.id_pegawai_penggajian, pt.nama_pegawai, pt.bulan, pt.tahun');
        $this->db->from('pendapatan_terkonfirmasi pt');

        if (!empty($tahun)) {
            $this->db->where('pt.tahun', $tahun);
        }

        if (!empty($bulan)) {
            $this->db->where('pt.bulan', $bulan);
        }

        // Filter berdasarkan range bulan/tahun
        if (!empty($start_date) && !empty($end_date)) {
            // Pastikan format input YYYY-MM
            $this->db->where("DATE_FORMAT(CONCAT(pt.tahun,'-',LPAD(pt.bulan,2,'0'),'-01'), '%Y-%m') >=", $start_date);
            $this->db->where("DATE_FORMAT(CONCAT(pt.tahun,'-',LPAD(pt.bulan,2,'0'),'-01'), '%Y-%m') <=", $end_date);
        }

        $this->db->group_by('pt.id_pegawai_penggajian, pt.bulan, pt.tahun, pt.nama_pegawai');
        return $this->db->get()->result();
    }






    // Ambil pendapatan detail per pegawai
    public function get_pendapatan_terkonfirmasi($id_pegawai_penggajian)
    {
        return $this->db->where('id_pegawai_penggajian', $id_pegawai_penggajian)
            ->get('pendapatan_terkonfirmasi')
            ->result();
    }

    // Ambil potongan detail per pegawai
    public function get_potongan_terkonfirmasi($id_pegawai_penggajian)
    {
        return $this->db->where('id_pegawai_penggajian', $id_pegawai_penggajian)
            ->get('potongan_terkonfirmasi')
            ->result();
    }

    // Model
    public function get_all_terkonfirmasi()
    {
        $this->db->select('pp.bulan, pp.tahun, pp.id_pegawai_penggajian, pt.nama_pegawai, 
                       pt.nama_pendapatan, pt.nominal as nominal_pendapatan, 
                       pot.nama_potongan, pot.nominal as nominal_potongan,
                       tp.total_pendapatan');
        $this->db->from('pegawai_penggajian pp');
        $this->db->join('pendapatan_terkonfirmasi pt', 'pp.id_pegawai_penggajian = pt.id_pegawai_penggajian', 'center');
        $this->db->join('potongan_terkonfirmasi pot', 'pp.id_pegawai_penggajian = pot.id_pegawai_penggajian', 'center');
        $this->db->join(
            '(SELECT id_pegawai_penggajian, SUM(nominal) as total_pendapatan FROM pendapatan_terkonfirmasi GROUP BY id_pegawai_penggajian) tp',
            'pp.id_pegawai_penggajian = tp.id_pegawai_penggajian',
            'left'
        );
        $this->db->order_by('pp.id_pegawai_penggajian', 'asc');

        return $this->db->get()->result();
    }

    public function get_all_pegawai_terkonfirmasi()
    {
        $this->db->select('p.nama,pp.*');
        $this->db->from('pegawai_penggajian pp,pegawai p');
        $this->db->where('pp.id_pegawai = p.id_pegawai');
        return $this->db->get()->result();
    }

    private function generate_id()
    {
        $this->db->select("MAX(CAST(SUBSTR(id_pegawai_penggajian, 4) AS UNSIGNED)) AS max_id");
        $data = $this->db->get('pegawai_penggajian')->row();
        $max_id = $data->max_id + 1;
        return 'PGJ' . str_pad($max_id, 3, '0', STR_PAD_LEFT);
    }


    public function thr()
    {
        $this->db->select('mthr.*');
        $this->db->from('master_tunjangan_hari_raya mthr,pegawai_penggajian pp');
        $this->db->where('mthr.id_pegawai = pp.id_pegawai');
        $this->db->where('status_konfirmasi', 1);
        $this->db->get()->result();
    }

    public function bonus()
    {
        $this->db->select('ijp.*');
        $this->db->from('imbalan_jasa_produksi ijp,pegawai_penggajian pp');
        $this->db->where('ijp.id_pegawai = pp.id_pegawai');
        $this->db->where('status_konfirmasi', 1);
        $this->db->get()->result();
    }


    public function count_all($status_konfirmasi = 0)
    {
        $this->db->from($this->table);
        $this->db->where('status_konfirmasi', $status_konfirmasi);
        return $this->db->count_all_results();
    }

    public function count_filtered($status_konfirmasi = 0)
    {
        $this->_get_datatables_query($status_konfirmasi);
        return $this->db->get()->num_rows();
    }


    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('pegawai_penggajian.id_pegawai_penggajian', $id);

        $this->db->join('pegawai', 'pegawai.id_pegawai = pegawai_penggajian.id_pegawai', 'left');
        $this->db->join('master_gaji_pokok gaji', 'gaji.id_gaji_pokok = pegawai_penggajian.id_gaji_pokok', 'left');
        $this->db->join('master_tunjangan_fungsional fungsional', 'fungsional.id_tunjangan = pegawai_penggajian.id_tunjangan_fungsional', 'left');
        $this->db->join('master_tunjangan_kesra kesra', 'kesra.id_tunjangan = pegawai_penggajian.id_tunjangan_kesra', 'left');
        $this->db->join('master_tunjangan_komunikasi komunikasi', 'komunikasi.id_tunjangan = pegawai_penggajian.id_tunjangan_komunikasi', 'left');
        $this->db->join('master_tunjangan_daerah daerah', 'daerah.id_tunjangan = pegawai_penggajian.id_tunjangan_daerah', 'left');
        $this->db->join('master_tunjangan_radiasi radiasi', 'radiasi.id_tunjangan = pegawai_penggajian.id_tunjangan_radiasi', 'left');
        $this->db->join('master_tunjangan_perumahan perumahan', 'perumahan.id_tunjangan = pegawai_penggajian.id_tunjangan_perumahan', 'left');

        return $this->db->get()->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, ['id_pegawai_penggajian' => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id_pegawai_penggajian' => $id]);
    }

    public function insert_insentif_batch($data)
    {
        $insertData = [];
        $this->db->select("MAX(CAST(SUBSTRING(id_pegawai_penggajian_insentif, 4) AS UNSIGNED)) AS max_id");
        $row = $this->db->get('pegawai_penggajian_insentif')->row();
        $start = $row->max_id ? $row->max_id + 1 : 1;

        foreach ($data as $i => $item) {
            $new_id = 'PPI' . str_pad($start + $i, 3, '0', STR_PAD_LEFT);
            $insertData[] = [
                'id_pegawai_penggajian_insentif' => $new_id,
                'id_pegawai_penggajian'          => $item['id_pegawai_penggajian'],
                'id_tunjangan_insentif'          => $item['id_tunjangan_insentif'],
                'nominal'                        => $item['nominal'] ?? 0
            ];
        }
        return $this->db->insert_batch('pegawai_penggajian_insentif', $insertData);
    }


    public function insert_insentif_batch2($data)
    {
        if (empty($data)) {
            return false;
        }

        // Jika $data multidimensi berarti batch
        if (isset($data[0]) && is_array($data[0])) {
            return $this->db->insert_batch($this->table_insentif, $data);
        } else {
            return $this->db->insert($this->table_insentif, $data);
        }
    }


    public function insert_kode_batch($data)
    {
        $insertData = [];
        $this->db->select("MAX(CAST(SUBSTRING(id_pegawai_penggajian_kode, 4) AS UNSIGNED)) AS max_id");
        $row = $this->db->get('pegawai_penggajian_kode')->row();
        $start = $row->max_id ? $row->max_id + 1 : 1;

        foreach ($data as $i => $item) {
            $new_id = 'PPK' . str_pad($start + $i, 3, '0', STR_PAD_LEFT);
            $insertData[] = [
                'id_pegawai_penggajian_kode' => $new_id,
                'id_pegawai_penggajian'      => $item['id_pegawai_penggajian'],
                'id_tunjangan_kode'          => $item['id_tunjangan_kode'],
                'nominal'                    => $item['nominal'] ?? 0
            ];
        }
        return $this->db->insert_batch('pegawai_penggajian_kode', $insertData);
    }

    public function insert_kode_batch2($data)
    {
        if (empty($data)) {
            return false;
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $this->db->insert_batch($this->table_kode, $data);
        } else {
            return $this->db->insert($this->table_kode, $data);
        }
    }


    public function insert_potongan_batch($data)
    {
        $insertData = [];

        // Buat ID unik: misal "PTG" + nomor urut (opsional, atau pakai uniqid())
        $this->db->select("MAX(CAST(SUBSTRING(id_pegawai_potongan, 4) AS UNSIGNED)) AS max_id");
        $row = $this->db->get('pegawai_potongan')->row();
        $start = $row->max_id ? $row->max_id + 1 : 1;

        foreach ($data as $i => $item) {
            $new_id = 'PTG' . str_pad($start + $i, 3, '0', STR_PAD_LEFT);
            $insertData[] = [
                'id_pegawai_potongan'     => $new_id,
                'id_pegawai_penggajian'   => $item['id_pegawai_penggajian'],
                'id_potongan'             => $item['id_potongan'],
                'nominal'                 => $item['nominal']
            ];
        }

        return $this->db->insert_batch('pegawai_potongan', $insertData);
    }

    public function insert_potongan_batch2($data)
    {
        if (empty($data)) {
            return false;
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $this->db->insert_batch($this->table_potongan, $data);
        } else {
            return $this->db->insert($this->table_potongan, $data);
        }
    }

    public function insert_penggajian($data)
    {
        return $this->db->insert($this->table2, $data);
    }

    public function delete_potongan_by_penggajian($id_pegawai_penggajian)
    {
        return $this->db->delete('pegawai_potongan', ['id_pegawai_penggajian' => $id_pegawai_penggajian]);
    }


    public function delete_insentif_by_penggajian($id)
    {
        return $this->db->delete('pegawai_penggajian_insentif', ['id_pegawai_penggajian' => $id]);
    }

    public function delete_kode_by_penggajian($id)
    {
        return $this->db->delete('pegawai_penggajian_kode', ['id_pegawai_penggajian' => $id]);
    }

    public function get_insentif_by_penggajian($id)
    {
        return $this->db->get_where('pegawai_penggajian_insentif', ['id_pegawai_penggajian' => $id])->result();
    }

    public function get_kode_by_penggajian($id)
    {
        return $this->db->get_where('pegawai_penggajian_kode', ['id_pegawai_penggajian' => $id])->result();
    }

    public function get_potongan_by_penggajian($id_pegawai_penggajian)
    {
        return $this->db->select('pegawai_potongan2.*, master_potongan.nama_potongan')
            ->from('pegawai_potongan2')
            ->join('master_potongan', 'master_potongan.id_potongan = pegawai_potongan2.id_potongan', 'left')
            ->where('pegawai_potongan2.id_pegawai_penggajian', $id_pegawai_penggajian)
            ->get()->result();
    }


    public function get_all()
    {
        $this->_get_datatables_query();
        return $this->db->get()->result();
    }

    public function get_data_penggajian()
    {
        $this->db->select('id_pegawai');
        $this->db->from('pegawai');
        $this->db->limit(10);
        return $this->db->get()->result();
    }


    public function get_slip_gaji_by_penggajian($id_pegawai_penggajian)
    {
        // Ambil data utama pegawai + gaji
        $this->db->select('pegawai.*, pegawai_penggajian.*, pegawai.email as email_pegawai');
        $this->db->from('pegawai_penggajian');
        $this->db->join('pegawai', 'pegawai.id_pegawai = pegawai_penggajian.id_pegawai');
        $this->db->where('pegawai_penggajian.id_pegawai_penggajian', $id_pegawai_penggajian);
        $main = $this->db->get()->row_array();

        if (!$main) return false;

        // Ambil data penempatan terbaru (atau penempatan aktif)
        $this->db->select('pegawai_penempatan.*, unit_level.nm_unit_level, unit_organisasi.nm_unit_organisasi');
        $this->db->from('pegawai_penempatan');
        $this->db->join('unit_level', 'unit_level.id_unit_level = pegawai_penempatan.id_unit_level', 'left');
        $this->db->join('unit_organisasi', 'unit_organisasi.id_unit_organisasi = pegawai_penempatan.id_unit_organisasi', 'left');
        $this->db->where('pegawai_penempatan.id_pegawai', $main['id_pegawai']);
        $penempatan = $this->db->get()->row_array();

        // Tambahkan ke array utama
        $main['level'] = $penempatan['nm_unit_level'] ?? '-';
        $main['unit'] = $penempatan['nm_unit_organisasi'] ?? '-';

        // --- SISA KODE KAMU ---
        // Ambil nominal tunjangan sesuai struktur kamu!
        $main['gaji_pokok'] = $this->db->get_where('master_gaji_pokok', ['id_gaji_pokok' => $main['id_gaji_pokok']])->row('nominal') ?? 0;

        $main['tunjangan_fungsional'] = 0;
        if (!empty($main['id_tunjangan_fungsional'])) {
            $main['tunjangan_fungsional'] = $this->db
                ->get_where('master_tunjangan_fungsional', ['id_tunjangan' => $main['id_tunjangan_fungsional']])
                ->row('tunjangan') ?? 0;
        }

        $main['tunjangan_kesra'] = 0;
        if (!empty($main['id_tunjangan_kesra'])) {
            $main['tunjangan_kesra'] = $this->db
                ->get_where('master_tunjangan_kesra', ['id_tunjangan' => $main['id_tunjangan_kesra']])
                ->row('tunjangan') ?? 0;
        }

        $main['tunjangan_komunikasi'] = 0;
        if (!empty($main['id_tunjangan_komunikasi'])) {
            $main['tunjangan_komunikasi'] = $this->db
                ->get_where('master_tunjangan_komunikasi', ['id_tunjangan' => $main['id_tunjangan_komunikasi']])
                ->row('tunjangan') ?? 0;
        }

        // Tunjangan Daerah
        $main['tunjangan_daerah'] = 0;
        if (!empty($main['id_tunjangan_daerah'])) {
            $q = $this->db->get_where('master_tunjangan_daerah', ['id_tunjangan' => $main['id_tunjangan_daerah']])->row();
            $main['tunjangan_daerah'] = $q ? $q->tunjangan : 0;
        }
        // Tunjangan Radiasi
        $main['tunjangan_radiasi'] = 0;
        if (!empty($main['id_tunjangan_radiasi'])) {
            $q = $this->db->get_where('master_tunjangan_radiasi', ['id_tunjangan' => $main['id_tunjangan_radiasi']])->row();
            $main['tunjangan_radiasi'] = $q ? $q->tunjangan : 0;
        }
        // Tunjangan Perumahan
        $main['tunjangan_perumahan'] = 0;
        if (!empty($main['id_tunjangan_perumahan'])) {
            $q = $this->db->get_where('master_tunjangan_perumahan', ['id_tunjangan' => $main['id_tunjangan_perumahan']])->row();
            $main['tunjangan_perumahan'] = $q ? $q->tunjangan : 0;
        }

        $main['tunjangan_penugasan'] = 0;
        if (!empty($main['id_tunjangan_penugasan'])) {
            $q = $this->db->get_where('tunjangan_penugasan', ['id_tunjangan_penugasan' => $main['id_tunjangan_penugasan']])->row();
            $main['tunjangan_penugasan'] = $q ? $q->nominal : 0;
        }

        $main['tunjangan_kasir'] = 0;
        if (!empty($main['id_tunjangan_kasir'])) {
            $q = $this->db->get_where('tunjangan_kasir', ['id_tunjangan_kasir' => $main['id_tunjangan_kasir']])->row();
            $main['tunjangan_kasir'] = $q ? $q->nominal : 0;
        }

        $main['tunjangan_transport'] = 0;
        if (!empty($main['id_tunjangan_transport'])) {
            $q = $this->db->get_where('tunjangan_transport', ['id_tunjangan_transport' => $main['id_tunjangan_transport']])->row();
            $main['tunjangan_transport'] = $q ? $q->nominal : 0;
        }

        $main['kompensasi'] = 0;
        if (!empty($main['id_kompensasi'])) {
            $q = $this->db->get_where('kompensasi', ['id_kompensasi' => $main['id_kompensasi']])->row();
            $main['kompensasi'] = $q ? $q->nominal : 0;
        }

        // Insentif & kode lain
        $main['insentif'] = $this->db->select_sum('nominal')->get_where('pegawai_penggajian_insentif', ['id_pegawai_penggajian' => $id_pegawai_penggajian])->row('nominal') ?? 0;
        $main['kode_lain'] = $this->db->select_sum('nominal')->get_where('pegawai_penggajian_kode', ['id_pegawai_penggajian' => $id_pegawai_penggajian])->row('nominal') ?? 0;

        // Hitung total pendapatan
        $main['total_pendapatan'] =
            floatval($main['gaji_pokok']) +
            floatval($main['tunjangan_fungsional']) +
            floatval($main['tunjangan_kesra']) +
            floatval($main['tunjangan_komunikasi']) +
            floatval($main['tunjangan_daerah']) +
            floatval($main['tunjangan_radiasi']) +
            floatval($main['tunjangan_perumahan']) +
            floatval($main['tunjangan_penugasan']) +
            floatval($main['tunjangan_kasir']) +
            floatval($main['tunjangan_transport']) +
            floatval($main['kompensasi']) +
            floatval($main['insentif']) +
            floatval($main['kode_lain']);

        // Potongan
        $potongan = $this->db->get_where('pegawai_potongan', ['id_pegawai_penggajian' => $id_pegawai_penggajian])->result();
        $main['total_potongan'] = 0;
        $main['potongan_rincian'] = [];
        foreach ($potongan as $ptg) {
            // join ke master nama potongan
            $nama_potongan = $this->db->get_where('master_potongan', ['id_potongan' => $ptg->id_potongan])->row('nama_potongan') ?? $ptg->id_potongan;
            $main['potongan_rincian'][$nama_potongan] = $ptg->nominal;
            $main['total_potongan'] += $ptg->nominal;
        }

        $main['pendapatan_bersih'] = $main['total_pendapatan'] - $main['total_potongan'];
        // Format bulan
        $bulan = [
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
            12 => 'Desember'
        ];
        $main['bulan'] = $bulan[$main['bulan']] ?? $main['bulan'];
        $main['periode'] = ($main['bulan'] ?? '-') . ' ' . ($main['tahun'] ?? '-');

        $main['no_rekening'] = $main['no_rek'] ?? '-';
        $main['nama_bank'] = '-';
        if (!empty($main['id_bank'])) {
            $bank = $this->db->get_where('bank_list', ['id_bank' => $main['id_bank']])->row();
            $main['nama_bank'] = $bank ? $bank->nm_bank : '-';
        }

        return $main;
    }

    public function get_potongan_kode_by_nama($id_pegawai, $nama_tunjangan)
    {
        $id_master = $this->db->get_where('master_tunjangan_kode', [
            'nm_tunjangan' => $nama_tunjangan
        ])->row('id_master');
        if (!$id_master) return 0;

        $nominal = $this->db->get_where('pegawai_kode', [
            'id_pegawai' => $id_pegawai,
            'id_master'  => $id_master
        ])->row('nominal');
        return $nominal ? (float)$nominal : 0;
    }

    public function get_all_potongan_kode($id_pegawai, $daftar_nama_tunjangan)
    {
        $result = [];
        foreach ($daftar_nama_tunjangan as $nama) {
            $result[$nama] = $this->get_potongan_kode_by_nama($id_pegawai, $nama);
        }
        return $result;
    }
}
