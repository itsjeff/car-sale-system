<?php
class User_model extends CI_Model 
{
	private $required = array(
		'email'    => 'required',
		'role_id'  => 'required',
		'password' => 'required'
		);


	public function __construct() {
		$this->load->database();
	}
	
	public function users($where = array()) {
		$this->db->join('user_profile', 'user_profile.user_id = users.user_id');
		$this->db->join('user_roles', 'user_roles.role_id = users.role_id');
		
		if (count($where) > 0) {
			$this->db->where($where); 
			
			$query = $this->db->get('users');
			
			return $query->row();
		}
		else {
			$query = $this->db->get('users');
			
			return $query->result();
		}		
	}

	public function count($where = array()) {
		// Where if array count more than 0
		if (count($where) > 0) {
			$this->db->where($where); 
		}

		// Get
		$query = $this->db->get('users');

		return $query->num_rows();
	}
	
	public function insert($form_data = array()) {
		// create user
		$this->db->insert('users', $form_data);
		$new_id = $this->db->insert_id();

		// create profile
		$this->db->insert('user_profile', array('user_id' => $new_id));
	}
	

	public function update_profile($where = array(), $form_data = array()) {
		$this->db->where($where);
		$this->db->update('user_profile', $form_data);
	}
	

	public function delete($user_id = 0) {
		
	}
	

	/*
	 * roles
	 */
	public function user_roles($is_array = false) 
	{
		$query = $this->db->get('user_roles');

		$dropdown = array();

		if ($is_array == true) {
			$dropdown['0" disabled selected style="display: none;'] = 'Select role';
			
			foreach ($query->result() as $row) {
				$dropdown[$row->role_id] = $row->role_name;
			}
			
			return $dropdown;
		} 
		else {
			return $query->result();	
		}
	}
	

	/*
	 * logged user details
	 */
	public function logged() {
		$session_data = $this->session->userdata('user_id');
		
		$where = array('users.user_id' => $session_data);
		
		if ($session_data > 0) {
			$this->db->join('user_profile', 'user_profile.user_id = users.user_id', 'left');
			$this->db->where($where); 
			
			$query = $this->db->get('users');
			
			return $query->row();
		} else {
			return false;
		}
	}


	/*
	 * User must be specifiedrole to view content
	 */
	public function is_role($role = 0)
	 {
		$role_id = $this->logged()->role_id;

		if ($role_id <= $role) {
			return true;
		}

		return false;

	}
}