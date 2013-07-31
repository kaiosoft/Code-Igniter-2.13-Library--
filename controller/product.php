<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {
	private $c = 'product';

	public function _construct(){
		parent::_construct();	
	}

	public function index($page=0)
	{
		if(!$this->session->userdata('logged_in')){
			redirect('auth/login');
		}
		
		$user_id = $this->session->userdata('user_id');
		
		// cek acl
		if(!$this->acl->cek_acl($this->c,'index',$user_id)){
			$text = '<div class="notification warning no-margin"> <span class="strong">Warning!</span> You dont have authorizate to access PRODUCT page. </div><p></p>';
			$this->session->set_flashdata('msg',$text);
			redirect('dashboard/index','refresh');
		}
		
		$this->load->library('pagination');
		
		// load model
		$this->load->model('ConfigModel');
		$this->load->model('ProductModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		// configuration paging
		$config['base_url'] = site_url()."/product/index/";
		$config['total_rows'] = $this->ProductModel->jmlhdata();
		$config['per_page'] = $cfg[0]['jmlh_item'];
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';
		$this->pagination->initialize($config); 
		
		$product = $this->ProductModel->getlist($config['per_page'],$page);
		
		// prepare data
		$data['jmlh_data'] = $this->ProductModel->jmlhdata();
		$data['jmlh_item'] = $cfg[0]['jmlh_item'];
		$data['action'] = site_url().'/product/edit';
		$data['results'] = $product;
		$data['file'] = 'product/list';
		$data['title'] = $cfg[0]['app_name'];
		$data['version'] = $cfg[0]['version'];
		$data['page'] = $page;
		
		// Write to $title
		$this->template->write('title', $data['title']);

		$subtitle = "Product";
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
		$this->load->model('ProductModel');
		$this->load->model('CatModel');
		
		$data['results'] = null;
		$data['action'] = site_url().'/product/update';
		$data['listcategory'] = $this->CatModel->getlist(0,0);

		if($id){ 
			$data['results'] = $this->ProductModel->get_by_id($id); 
		} 
		
		$this->load->view('product/form_product',$data);
		
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
		
		//redirect(site_url('product'));	
    }
	
    public function save(){ 
		// load model
		$this->load->model('ProductModel');
		
		$data['id'] = $this->input->post('id');
		$data['Product_Name'] = $this->input->post('Product_Name');
		$data['cat_id'] = $this->input->post('cat_id');
		$data['subcat_id'] = $this->input->post('subcat_id');
		$data['Material'] = $this->input->post('Material');
		$data['Model'] = $this->input->post('Model');
		$data['Note'] = $this->input->post('Note');
		
		// create Product_Code
		//$p_code = 
		print_r($data);
		// save data product
		//$this->ProductModel->saveDataProduct($data); 
		
		// save data image
		//$this->ImageModel->saveData($data);
		
		// get product_id
		//$product_id = $this->ProductModel->get_by_code($p_code);
		
		// save data product image
		//$this->ProductModel->saveDataProdImg($data);
		
		$text = '<div class="notification info nopic">Data Product telah berhasil di simpan. </div>';
		//$this->session->set_flashdata('msg',$text);
	}  
	
	/*
	*	Next version data tdk bisa di hapus jika sdng di gunakan di tabel lain
	*/
	private function hapus($id=0){
		// load model
		$this->load->model('ProductModel');
		
		$id = $this->input->post('Id');
		
		$this->ProductModel->hapus($id);

		$text = '<div class="notification info nopic">Data Category telah berhasil di hapus. </div>';
		$this->session->set_flashdata('msg',$text);
	}
	
    public function confirm(){
		$data['id'] = $this->uri->segment(3);
		$data['action'] = site_url().'/cat/update';
		$this->load->view('cat/confirm-delete',$data);
    }
	
	public function ajax($id){
		// load model
		$this->load->model('SubcatModel');
		
		$listsubcat = $this->SubcatModel->getlist(0,0,$id);
		
		if($id!=0){
			echo "<p class=\"inline\">";
			echo "<label>Sub Category <span class=\"required\">*</span></label>";
			echo "<label>";
			echo "<select name=\"subcat_id\">";
			echo "	<option value=\"\">Select Sub Category</option>";
			foreach($listsubcat as $res){	
			echo "	<option value=\"".$res->id."\">".$res->subcat_name."</option>";
			}
			echo "</select>";
			echo "</label>";
		} else {
			echo "<p class=\"inline\">";
			echo "<label>Sub Category <span class=\"required\">*</span></label>";
			echo "<label>";
			echo "<select name=\"subcat_id\">";
			echo "	<option value=\"\">Select Sub Category</option>";
			echo "</select>";
			echo "</label>";
		}
	}
	
	public function check_product(){	
		// load model
		$this->load->model('ProductModel');
		$this->load->model('CurrencyModel');
		$this->load->model('ConfigModel');
		
		$cfg = $this->ConfigModel->getlist();
		
		$perusahaan_id = $this->session->userdata('perusahaan_id');
		
		$data['kode'] = $this->input->post('kode');
		$data['buyers_id'] = $this->input->post('buyers_id');
		$data['orders_id'] = $this->input->post('orders_id');
		$data['product'] = $this->ProductModel->get_by_kode($data['kode'],$perusahaan_id);
		$data['listcurrecy'] = $this->CurrencyModel->getlist(0,0,$perusahaan_id);
		//$data['currency_rate'] = $cfg[0]['default_currency_rate'];
		
		if(count($data['product']) > 0){ 
			$data['listdim'] = $this->ProductModel->getlist_dim($data['product'][0]['id']);
		}
		
		$this->load->view('product/check_product',$data);
	}
	

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */