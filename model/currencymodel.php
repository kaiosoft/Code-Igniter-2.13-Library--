<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CurrencyModel extends CI_Model{

	private $tbl = 'kaio_currency';

	public function _construct()
	{
		parent::_construct();
	}
	
	public  function getlist($start,$finish,$perusahaan_id){ 
		$this->db->select('a.*');
		$this->db->from($this->tbl.' AS a');
		
		if($perusahaan_id > 0){
			$this->db->where('company_id',$perusahaan_id);
		}
				
		$this->db->order_by('a.currency_name','asc');
		
		if($start > 0 OR $finish > 0){
			$this->db->limit($start,$finish);
		}
		
		return $this->db->get()->result();
	}
	
	public function jmlhdata($perusahaan_id){
		if($perusahaan_id > 0){
			$this->db->where('company_id',$perusahaan_id);
		}
				
		return $this->db->count_all_results($this->tbl);
	}
	
	public function hapus($id=0){
		$this->db->where_in('id',$id);
		$this->db->delete($this->tbl);
		return true;
	}
	
	public function get_by_id($id){ 
		$this->db->select('*');
		$this->db->where('id', $id);
		return $this->db->get($this->tbl)->result_array();
	}
	
	public function saveData($data)
	{
		if(empty($data['id'])){
			$this->db->insert($this->tbl,$data);
		} else {
			$this->db->where('id',$data['id']);
			$this->db->update($this->tbl,$data);
		}
	}
	
	public  function getlistrate($start,$finish,$perusahaan_id){ 
		$this->db->select('b.currency_name,a.*');
		$this->db->from('kaio_currency_rate AS a');
		$this->db->join('kaio_currency As b','b.id=a.currency_id');
		
		if($perusahaan_id > 0){
			$this->db->where('a.company_id',$perusahaan_id);
		}
				
		$this->db->order_by('b.currency_name','asc');
		
		if($start > 0 OR $finish > 0){
			$this->db->limit($start,$finish);
		}
		
		return $this->db->get()->result();
	}
	
	public function jmlhdata_rate($perusahaan_id){
		if($perusahaan_id > 0){
			$this->db->where('company_id',$perusahaan_id);
		}
				
		return $this->db->count_all_results('kaio_currency_rate');
	}
	
	/*public function get_default_rate($currency_id,$perusahaan_id){
		$this->db->select('rate');
		$this->db->where('currency_id', $currency_id);
		$this->db->where('id', $id);
		return $this->db->get('kaio_currency_rate')->result_array();	
	}*/
}

?>
