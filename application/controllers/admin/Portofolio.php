<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Portofolio extends CI_Controller {


	// load data
	public function __construct()
	{
		parent::__construct();
		$this->load->model('portofolio_model');
		// Tambahkan proteksi halaman
		$url_pengalihan = str_replace('index.php/', '', current_url());
		$pengalihan 	= $this->session->set_userdata('pengalihan', $url_pengalihan);
		// Ambil check login dari simple_login
		$this->simple_login->check_login($pengalihan);
	}

	public function index()
	{
		$portofolio			= $this->portofolio_model->listing();
		// $total 	= $this->user_model->total();

		$data = array(
			'title'			=> 'Data Portofolio (' . count($portofolio) . ')',
			'portofolio'	=> $portofolio,
			'isi'			=> 'admin/portofolio/list'
		);
		$this->load->view('admin/layout/wrapper', $data, FALSE);
	}

	// Tambah
	public function tambah()
	{
		$portofolio	= $this->portofolio_model->listing();

		// Validasi
		$v = $this->form_validation;


		$v->set_rules(
			'judul_portofolio',
			'isi portofolio',
			'required',
			array('required'		=> 'judul portofolio harus diisi')
		);

		$v->set_rules(
			'link',
			'link ',
			'required',
			array('required'		=> 'link harus diisi')
		);

		$v->set_rules(
			'category',
			'category portofolio',
			'required',
			array('required'		=> 'category portofolio harus diisi')
		);

		$v->set_rules(
			'client',
			'client',
			'required',
			array('required'		=> 'client harus diisi')
		);

		$v->set_rules(
			'keterangan',
			'keterangan',
			'required',
			array('required'		=> 'keterangan harus diisi')
		);

		if ($v->run()) {
			$config['upload_path'] 		= './assets/upload/portofolio/';
			$config['allowed_types'] 	= 'gif|jpg|png|svg';
			$config['max_size']			= '12000'; // KB	
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('gambar')) {
				// End validasi

				$data = array(
					'title'			=> 'Tambah Data Portofolio',
					'portofolio'	=> $portofolio,
					'error'			=> $this->upload->display_errors(),
					'isi'			=> 'admin/portofolio/tambah'
				);
				$this->load->view('admin/layout/wrapper', $data);
				// Masuk database
			} else {
				$upload_data				= array('uploads' => $this->upload->data());
				// Image Editor
				$config['image_library']	= 'gd2';
				$config['source_image'] 	= './assets/upload/' . $upload_data['uploads']['file_name'];
				$config['new_image'] 		= './assets/upload/portofolio/';
				$config['create_thumb'] 	= TRUE;
				$config['quality'] 			= "100%";
				$config['maintain_ratio'] 	= TRUE;
				$config['width'] 			= 360; // Pixel
				$config['height'] 			= 200; // Pixel
				$config['x_axis'] 			= 0;
				$config['y_axis'] 			= 0;
				$config['thumb_marker'] 	= '';
				$this->load->library('image_lib', $config);
				$this->image_lib->resize();

				// Proses ke database
				$i = $this->input;
				$data = array(

					'judul_portofolio'		=> $i->post('judul_portofolio'),
					'link'					=> $i->post('link'),
					'category'				=> $i->post('category'),
					'client'				=> $i->post('client'),
					'keterangan'			=> $i->post('keterangan'),
					'gambar'				=> $upload_data['uploads']['file_name'],

				);

				$this->portofolio_model->tambah($data);
				$this->session->set_flashdata('sukses', 'Berita telah ditambah');
				redirect(base_url('admin/portofolio'));
			}
		}
		// End masuk database
		$data = array(

			'title'			=> 'Tambah portofolio ',
			'portofolio'	=> $portofolio,
			'isi'			=> 'admin/portofolio/tambah'

		);
		$this->load->view('admin/layout/wrapper', $data);
	}


	// Edit galeri
	public function edit($id_portofolio)
	{
		$portofolio 	= $this->portofolio_model->detail($id_portofolio);

		// Validasi
		$valid = $this->form_validation;

		$valid->set_rules(
			'judul_portofolio',
			'isi portofolio',
			'required',
			array('required'		=> 'judul portofolio harus diisi')
		);

		$valid->set_rules(
			'link',
			'link ',
			'required',
			array('required'		=> 'link harus diisi')
		);

		$valid->set_rules(
			'category',
			'category portofolio',
			'required',
			array('required'		=> 'category portofolio harus diisi')
		);

		$valid->set_rules(
			'client',
			'client',
			'required',
			array('required'		=> 'client harus diisi')
		);

		$valid->set_rules(
			'keterangan',
			'keterangan',
			'required',
			array('required'		=> 'keterangan harus diisi')
		);

		if ($valid->run()) {

			if (!empty($_FILES['gambar']['name'])) {

				$config['upload_path']   = './assets/upload/portofolio/';
				$config['allowed_types'] = 'gif|jpg|png|svg|jpeg';
				$config['max_size']      = '12000'; // KB  
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('gambar')) {
					// End validasi

					$data = array(
						
						'title'			=> 'Edit portofolio ',
						'portofolio'	=> $portofolio,
						'isi'			=> 'admin/portofolio/edit',
						'error'    		=> $this->upload->display_errors(),
		
					);
					$this->load->view('admin/layout/wrapper', $data, FALSE);
					// Masuk database
				} else {
					$upload_data        		= array('uploads' => $this->upload->data());
					// Image Editor
					$config['image_library']  	= 'gd2';
					$config['source_image']   	= './assets/upload/' . $upload_data['uploads']['file_name'];
					$config['new_image']     	= './assets/upload/portofolio/';
					$config['create_thumb']   	= TRUE;
					$config['quality']       	= "100%";
					$config['maintain_ratio']   = TRUE;
					$config['width']       		= 360; // Pixel
					$config['height']       	= 360; // Pixel
					$config['x_axis']       	= 0;
					$config['y_axis']       	= 0;
					$config['thumb_marker']   	= '';
					$this->load->library('image_lib', $config);
					$this->image_lib->resize();

					// Proses hapus gambar
					// if ($galeri->gambar != "") {
					// 	unlink('./assets/upload/' . $galeri->gambar);
					// 	unlink('./assets/upload/galeri/' . $galeri->gambar);

					// }
					// End hapus gambar

					$i 		= $this->input;

					$data = array(

						'id_portofolio'			=> $id_portofolio,
						'judul_portofolio'		=> $i->post('judul_portofolio'),
						'link'					=> $i->post('link'),
						'category'				=> $i->post('category'),
						'client'				=> $i->post('client'),
						'keterangan'			=> $i->post('keterangan'),
						'gambar'				=> $upload_data['uploads']['file_name'],
					);
					$this->portofolio_model->edit($data);
					$this->session->set_flashdata('sukses', 'Data telah diedit');
					redirect(base_url('admin/portofolio'), 'refresh');
				}
			} else {
				$i 		= $this->input;

				$data = array(

					'title'			=> 'Edit portofolio ',
					'portofolio'	=> $portofolio,
					'isi'			=> 'admin/portofolio/edit',
					// 'error'    			=> $this->upload->display_errors(),
					// 'gambar'			=> $upload_data['uploads']['file_name'],
				);
				$this->portofolio_model->edit($data);
				$this->session->set_flashdata('sukses', 'Data telah diedit');
				redirect(base_url('admin/portofolio'), 'refresh');
			}
		}
		// End masuk database
		$data = array(

			'title'			=> 'Edit portofolio ',
			'portofolio'	=> $portofolio,
			'isi'			=> 'admin/portofolio/edit',
		);
		$this->load->view('admin/layout/wrapper', $data, FALSE);
	}


	// Api Delete
	public function delete($id_portofolio)
	{

		$data = array('id_portofolio'	=> $id_portofolio);
		$this->portofolio_model->delete($data);
		$this->session->set_flashdata('sukses', 'Data Galeri telah didelete');
		redirect(base_url('admin/portofolio'));
	}

}

/* End of file Portofolio.php */
