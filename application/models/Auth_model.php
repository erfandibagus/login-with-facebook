<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

	function user_signup($data)
	{
		return $this->db->insert('users', $data);
	}
	
	function user_token_update($fbid, $data)
	{
		return $this->db->where('fbid', $fbid)
						->update('users', $data);
	}

	function check_user($fbid)
	{
		return $this->db->where('fbid', $fbid)
						->get('users')
						->num_rows();
	}

	function action_login($email, $password)
	{
		return $this->db->where(array('email' => $email, 'password' => sha1($password)))
						->get('users')
						->result_array();
	}
}