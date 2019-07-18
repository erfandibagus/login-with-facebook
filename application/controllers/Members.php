<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Members extends CI_Controller {

	public function index()
	{
		if ($this->session->userdata('logged_in') == FALSE) {
			redirect(base_url('facebook'));
		} else {
			$this->load->view('members');
		}
	}
}