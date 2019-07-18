<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	private $id = '00000000000000'; // app_id
	private $secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // app_secret
	private $version = 'v3.1'; // graph_version

	public function __construct()
    {
    	parent::__construct();
    	$this->load->model('auth_model');
    	$this->load->library('lib');
    	require_once __DIR__ . '/Facebook/autoload.php'; // Facebook SDK (5.6.3)
    }

    public function index()
    {
    	if ($this->session->userdata('logged_in') == FALSE) {
    		redirect(base_url('auth/login'));
		} else {
			redirect(base_url('members'));
		}
    }

	public function login()
	{
		if ($this->session->userdata('logged_in') == FALSE) {
			$fb = new Facebook\Facebook([
				'app_id' => $this->id,
				'app_secret' => $this->secret,
				'default_graph_version' => $this->version,
			]);
			$helper = $fb->getRedirectLoginHelper();
			$permissions = array('public_profile','email');
			$loginUrl = $helper->getLoginUrl(base_url('auth/callback'), $permissions);
			
			$data['url_login'] = htmlspecialchars($loginUrl);
			$this->load->view('login',$data);
		} else {
			redirect(base_url('members'));
		}
	}

	public function callback()
	{
		if ($this->session->userdata('logged_in') == FALSE) {
			$fb = new Facebook\Facebook([
				'app_id' => $this->id,
				'app_secret' => $this->secret,
				'default_graph_version' => $this->version,
			]);
			$helper = $fb->getRedirectLoginHelper();
			try {
				$accessToken = $helper->getAccessToken();
				$response = $fb->get('/me?fields=id,name,email', $accessToken);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				$this->session->set_flashdata('message', 'Graph returned an error: '.$e->getMessage());
				redirect(base_url('auth/login'));
				exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				$this->session->set_flashdata('message', 'Facebook SDK returned an error: '.$e->getMessage());
				redirect(base_url('auth/login'));
				exit;
			}
			if (!isset($accessToken)) {
				if ($helper->getError()) {
					header('HTTP/1.0 401 Unauthorized');
					$this->session->set_flashdata('message', '401 Unauthorized');
					redirect(base_url('auth/login'));
				} else {
					header('HTTP/1.0 400 Bad Request');
					$this->session->set_flashdata('message', '400 Bad Request');
					redirect(base_url('auth/login'));
				}
				exit;
			}
			$user = $response->getGraphUser();
			$check = $this->auth_model->check_user($user['id']);
			if ($check == 0) {
				$data = array(
					'token' 	=> (string) $accessToken, 
					'fbid'		=> $user['id'],
					'name'		=> $user['name'],
					'email'		=> $user['email'],
					'password'	=> sha1($this->lib->randomPassword(10))
				);
				$signup = $this->auth_model->user_signup($data);
				if ($signup == TRUE) {
					$sesi = array(
						'token' 	=> (string) $accessToken, 
						'fbid'		=> $user['id'],
						'name'		=> $user['name'],
						'email'		=> $user['email'],
						'logged_in'	=> TRUE
					);
					$this->session->set_userdata($sesi);
					redirect(base_url('members'));
				} else {
					$this->session->set_flashdata('message', 'Terjadi Kesalahan');
					redirect(base_url('auth/login'));
				}
			} else {
				$data['token'] = (string) $accessToken;
				$logged = $this->auth_model->user_token_update($user['id'], $data);
				if ($logged == TRUE) {
					$sesi = array(
						'token' 	=> (string) $accessToken, 
						'fbid'		=> $user['id'],
						'name'		=> $user['name'],
						'email'		=> $user['email'],
						'logged_in'	=> TRUE
					);
					$this->session->set_userdata($sesi);
					redirect(base_url('members'));
				} else {
					$this->session->set_flashdata('message', 'Terjadi Kesalahan');
					redirect(base_url('auth/login'));
				}
			}
    	} else {
    		redirect(base_url('members'));
    	}
	}

	public function action()
	{
		if ($this->session->userdata('logged_in') == FALSE) {
			$email = $this->input->post('email', TRUE);
			$password = $this->input->post('password');
			$action = $this->auth_model->action_login($email, $password);
			if ($action == TRUE) {
				$sesi = array(
					'token' 	=> $action[0]['token'], 
					'fbid'		=> $action[0]['fbid'],
					'name'		=> $action[0]['name'],
					'email'		=> $action[0]['email'],
					'logged_in'	=> TRUE
				);
				$this->session->set_userdata($sesi);
				redirect(base_url('members'));
			} else {
				$this->session->set_flashdata('message', 'Email atau Password Salah');
				redirect(base_url('auth/login'));
			}
		} else {
			redirect(base_url('members'));
		}
	}

	public function logout()
	{
		session_destroy();
        redirect(base_url('auth/login'));
	}
}
