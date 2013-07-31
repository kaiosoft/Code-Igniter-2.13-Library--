<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends CI_Controller {
	private $c = 'invoice';

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
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access INVOICE page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('InvoiceModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/invoice/index/";
		$config['total_rows'] = $this->InvoiceModel->jmlhdata($perusahaan_id,$cabang_id);
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		$invoice = $this->InvoiceModel->getlist($config['per_page'],$page,$perusahaan_id,$cabang_id);
		
		// prepare data
		$data['jmlh_data'] = $this->InvoiceModel->jmlhdata($perusahaan_id,$cabang_id);
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['action'] = site_url().'/invoice/edit';
		$data['results'] = $invoice;
		$data['file'] = 'invoice/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Invoice";
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
		$this->load->model('InvoiceModel');
		$this->load->model('CustomerModel');
		$this->load->model('CurrencyModel');
		
		$perusahaan_id = $this->session->userdata('perusahaan_id');
		$cabang_id = $this->session->userdata('cabang_id');
		
		$data['results'] = null;
		$data['action'] = site_url().'/invoice/update';
		$data['listcustomer'] = $this->CustomerModel->getlist(0,0,$perusahaan_id,$cabang_id);
		$data['listcurrency'] = $this->CurrencyModel->getlist(0,0,$perusahaan_id);
		
		if($id){ 
			$data['results'] = $this->InvoiceModel->get_by_id($id); 
		} 
		
		$this->load->view('invoice/form_invoice',$data);
		
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
		
		redirect(site_url('invoice'));	
    }
	
    public function save(){ 
		// load model
		$this->load->model('InvoiceModel');
		
		$this->load->helper('waktu');
		
		$data['id'] = $this->input->post('id');
		$data['buyers_id'] = $this->input->post('buyers_id');
		$data['orders_id'] = $this->input->post('orders_id');
		$data['invoice_date'] = format_waktu($this->input->post('invoice_date'));
		$data['total'] = $this->input->post('total');
		$data['currency_id'] = $this->input->post('currency_id');
		$data['invoice_no'] = $this->input->post('invoice_no');
		$data['note'] = $this->input->post('note');
		$data['status'] = 'Unpaid';
		$data['company_id'] = $this->session->userdata('perusahaan_id');
		$data['cabang_id'] = $this->session->userdata('cabang_id');
		
		$this->InvoiceModel->saveData($data); 
		
		$text = '<div class="notification info nopic">Data Invoice telah berhasil di simpan. </div>';
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