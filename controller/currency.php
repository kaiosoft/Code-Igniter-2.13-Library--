<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Currency extends CI_Controller {
	private $c = 'currency';

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
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access CURRENCY page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index','refresh');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('CurrencyModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/customer/index/";
		$config['total_rows'] = $this->CurrencyModel->jmlhdata($perusahaan_id,$cabang_id);
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		// jika user dr kntr pusat maka set cabang id menjadi nol
		if($cabang_id ==$cfg[0]['id_pusat']){
			$cabang_id = 0;
		}
		
		$currency = $this->CurrencyModel->getlist($config['per_page'],$page,$perusahaan_id,$cabang_id);
		
		// prepare data
		$data['jmlh_data'] = $this->CurrencyModel->jmlhdata($perusahaan_id,$cabang_id);
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['action'] = site_url().'/currency/edit';
		$data['results'] = $currency;
		$data['file'] = 'currency/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Currency";
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
		$this->load->model('CurrencyModel');
		
		$data['results'] = null;
		$data['action'] = site_url().'/currency/update';

		if($id){ 
			$data['results'] = $this->CurrencyModel->get_by_id($id); 
		} 
		
		$this->load->view('currency/form_currency',$data);
		
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
		
		redirect(site_url('currency'));	
    }
   
   public function save(){ 
		// load model
		$this->load->model('CurrencyModel');
		
		$data['id'] = $this->input->post('id');
		$data['currency_name'] = $this->input->post('currency_name');
		$data['desc'] = $this->input->post('desc');
		$data['company_id'] = $this->session->userdata('perusahaan_id');
		
		$this->CurrencyModel->saveData($data); 
		
		$text = '<div class="notification info nopic">Data Currency telah berhasil di simpan. </div>';
		$this->session->set_flashdata('msg',$text);
	}  
   
	/*
	*	Next version data tdk bisa di hapus jika sdng di gunakan di tabel lain
	*/
	private function hapus($id=0){
		// load model
		$this->load->model('CustomerModel');
		
		$id = $this->input->post('id');
		
		// cek apakah customer mempunyai member ?
		if($this->CurrencyModel->have_member($id)==0){
			$this->CurrencyModel->hapus($id);
		} else {
			// krn mempunyai member maka tdk dpt di hapus
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> Customer tidak dapat di hapus karena masih memilik member. </div><p></p';
			$this->session->set_flashdata('msg',$text);
		}
	}
   
    public function search(){
		// load model
		$this->load->model('CustomerModel');
		$this->load->model('CountryModel');
		
		$data['action'] = site_url().'/customer/index';
		$data['listmember'] = $this->CustomerModel->getlistmember();
		$data['listcountry'] = $this->CountryModel->getlist(0,0);
		
		$this->load->view('customer/search', $data);
    }
	
    public function confirm(){
		$data['id'] = $this->uri->segment(3);
		$data['action'] = site_url().'/customer/update';
		$this->load->view('customer/confirm-delete',$data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */