<?php 

/**
 *	Copyright (C) Kaio Piranti Lunak
 *	Developer: Fatah Iskandar Akbar
 *  Email : info@kaiogroup.com
 *	Date: Juni 2013
**/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CustomerModel extends CI_Model{

	private $tbl = 'kaio_buyers';

	public function _construct()
	{
		parent::_construct();
	}
	
	public  function getlist($start,$finish,$perusahaan_id,$cabang_id){ 
		$this->db->select('b.name AS country, a.*');
		$this->db->from($this->tbl.' AS a');
		$this->db->where('company_id',$perusahaan_id);
		
		if($cabang_id > 0){
			$this->db->where('cabang_id',$cabang_id);
		}
		
		$this->db->join('kaio_country AS b','b.id=a.country_id');
		$this->db->order_by('a.company','asc');
		
		if($start > 0 OR $finish > 0){
			$this->db->limit($start,$finish);
		}
		
		return $this->db->get()->result();
	}
	
	public function jmlhdata($perusahaan_id,$cabang_id){
		$this->db->where('company_id',$perusahaan_id);
		
		if($cabang_id > 0){
			$this->db->where('cabang_id',$cabang_id);
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
	
	public  function getlistmember(){ 
		$this->db->select('*');
		$this->db->from($this->tbl);
		$this->db->where('has_child',1);
		$this->db->order_by('company','asc');
		return $this->db->get()->result();
	}
	
	public function have_member($id){ 
		$this->db->where('MasterBuyers',$id);
		return $this->db->count_all_results($this->tbl);
	}
	
}

?>
