<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Piutang extends CI_Controller {
	private $c = 'piutang';

	public function _construct(){
		parent::_construct();	
	}

	public function index($page=0)
	{
		if(!$this->session->userdata('logged_in')){
			redirect('auth/login');
		}
		
		$user_id = $this->session->userdata('user_id');
		$perusahaan_id = $this->session->userdata('perusahaan_id');
		$cabang_id = $this->session->userdata('cabang_id');
		
		// cek acl
		if(!$this->acl->cek_acl($this->c,'index',$user_id)){
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access PIUTANG page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('PiutangModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/piutang/index/";
		$config['total_rows'] = $this->PiutangModel->jmlhdata($perusahaan_id);
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		$piutang = $this->PiutangModel->getlist($config['per_page'],$page,$perusahaan_id,$cabang_id);
		
		// prepare data
		$data['jmlh_data'] = $this->PiutangModel->jmlhdata($perusahaan_id);
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['action'] = site_url().'/piutang/edit';
		$data['results'] = $piutang;
		$data['file'] = 'finance/piutang/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Piutang";
		// Write to $subtitle
		$this->template->write('subtitle', $subtitle);
		
		// Write to Header
		$this->template->write_view('header', 'templates/default/header.php'); 
		  
		// Write to Content
		$this->template->write_view('content', 'templates/default/content.php', $data);
					
		// Write to Footer
		$this->template->write_view('footer', 'templates/default/footer.php'); 
		  
		// Render the template
		$this->template->render();
	}
	
	public function edit($id=0){
		
		// load model
		$this->load->model('CabangModel');
		
		$data['results'] = null;
		$data['action'] = site_url().'/cabang/update';

		if($id){ 
			$data['results'] = $this->CabangModel->getdetail($id); 
		} 
		
		$this->load->view('cabang/form_cabang',$data);
		
	}
	
    public function update(){
		if($this->input->post('button')=='Save'){
			if($this->input->post('id')){
				$data['updatetype'] = "edit";
			} else {
				$data['updatetype'] = "new";
			}
			$this->save();
		} else {
			$this->hapus();
		}
		
		redirect(site_url('cabang'));	
    }
	
    public function save(){ 
		// load model
		$this->load->model('CabangModel');
		
		$data['id'] = $this->input->post('id');
		$data['cabang_name'] = $this->input->post('cabang_name');
		$data['phone'] = $this->input->post('phone');
		$data['fax'] = $this->input->post('fax');
		$data['address'] = $this->input->post('address');
		$data['company_id'] = $this->session->userdata('perusahaan_id');
		
		$this->CabangModel->saveData($data); 
		
		$text = '<div class="notification info nopic">Data Cabang telah berhasil di simpan. </div>';
		$this->session->set_flashdata('msg',$text);
	}  
	
	/*
	*	Next version data tdk bisa di hapus jika sdng di gunakan di tabel lain
	*/
	private function hapus($id=0){
		// load model
		$this->load->model('CabangModel');
		
		$id = $this->input->post('id');
		
		$this->CabangModel->hapus($id);

		$text = '<div class="notification info nopic">Data Cabang telah berhasil di hapus. </div>';
		$this->session->set_flashdata('msg',$text);
	}
	
    public function confirm(){
		$data['id'] = $this->uri->segment(3);
		$data['action'] = site_url().'/cabang/update';
		$this->load->view('cabang/confirm-delete',$data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */