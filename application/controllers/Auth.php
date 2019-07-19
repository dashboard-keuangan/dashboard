<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
  public function __construct() {
		parent::__construct();
		$this->load->model('m_user', '', TRUE);
  }
  
	public function index() {
    if ($this->session->userdata('dash_keu_id')){
      redirect('pages','location');
    }
    else {
      $this->load->view('auth/login');
    }
  }

	public function register() {
		$this->load->view('auth/register');
  }
  
  public function recover() {
    $this->load->view('auth/recover');
  }

  public function logout() {
    $array_val = array('dash_keu_id','dash_keu_username', 'dash_keu_nama');
    $this->session->unset_userdata($array_val);
    redirect('auth', 'location');
  }

  public function act_login() {
    // cek $_POST
		if ($this->input->post()) {
			$username = $this->input->post('username');
			$pwd = sha1($this->input->post('password'));

			$data = $this->m_user->get_user($username, $pwd);
			if (!$data) {
				$this->session->set_flashdata('login-gagal', "GAGAL");
				redirect('auth', 'location');
			} else {
				$this->session->set_userdata('dash_keu_id', $data[0]['id']);
				$this->session->set_userdata('dash_keu_username', $data[0]['username']);
				$this->session->set_userdata('dash_keu_nama', $data[0]['nama']);

				redirect('/', 'location');
			}
		}
  }
  
  public function act_register() {
    if ($this->input->post()) {
      $data['username'] = $this->input->post('username');
      $data['nama'] = $this->input->post('nama');
      $data['password'] = sha1($this->input->post('password'));
      $ver['password-v'] = sha1($this->input->post('password-v'));
      if ($data['password']==$ver['password-v']){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'password', 'is_unique[user.username]');
        if ($this->form_validation->run()) {
          $this->m_user->add_user($data);
          $this->session->set_flashdata('register_ok', 'Success!');
          redirect('auth', 'location');
        } else {
          $this->session->set_flashdata('register_fail', 'Failed! Username exist!');
          redirect('auth/register', 'location');
        }
      } else {
        $this->session->set_flashdata('register_fail_p', 'Failed! Password does not match');
          redirect('auth/register', 'location');
      }
		}
  }

}
