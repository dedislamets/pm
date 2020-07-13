<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
	    $this->load->model('admin');
	   
	}
	public function index()
	{		
		if($this->admin->logged_id()){
        	
        	redirect('pengguna');

            $data['title'] = 'Home';
            $data['main'] = 'dashboard';
			$data['js'] = 'dashboard/js';
			$this->load->view('dashboard',$data,FALSE); 

        }else{

            redirect("login");

        }				  
						
	}

	public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
	
}
