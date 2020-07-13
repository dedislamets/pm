<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pengguna extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
	    $this->load->model('admin');
	   
	}
	public function index()
	{		
		if($this->admin->logged_id()){
        	
            $data['title'] = 'Home';
            $data['main'] = 'pengguna/list';
			$data['js'] = 'script/pengguna';
			$data['modal'] = 'modal/pengguna';

			$data['apps'] = $this->admin->getmaster('app_tbl');

			$this->load->view('dashboard',$data,FALSE); 

        }else{

            redirect("login");

        }				  				
	}

	public function dataTable()
	{
	    $draw = intval($this->input->get("draw"));
	    $start = intval($this->input->get("start"));
	    $length = intval($this->input->get("length"));
	    $order = $this->input->get("order");
	    $search= $this->input->get("search");
	    $search = $search['value'];
	    $col = 10;
	    $dir = "";

	    if(!empty($order))
	    {
	        foreach($order as $o)
	        {
	            $col = $o['column'];
	            $dir= $o['dir'];
	        }
	    }

	    if($dir != "asc" && $dir != "desc")
	    {
	        $dir = "desc";
	    }

	    $valid_columns = array(
	        0=>'user_id',
	        1=>'apps',
	        2=>'nama_pengguna',
	        3=>'email',
	        4=>'last_update_date',
	        5=>'aktif',
	        6=>'eternal'
	    );
	    $valid_sort = array(
	        0=>'user_id',
	        1=>'apps',
	        2=>'nama_pengguna',
	        3=>'email',
	        4=>'last_update_date',
	        5=>'aktif',
	        6=>'eternal'
	    );
	    if(!isset($valid_sort[$col]))
	    {
	        $order = null;
	    }
	    else
	    {
	        $order = $valid_sort[$col];
	    }
	    if($order !=null)
	    {
	        $this->db->order_by($order, $dir);
	    }
	    
	    if(!empty($search))
	    {
	        $x=0;
	        foreach($valid_columns as $sterm)
	        {
	            if($x==0)
	            {
	                $this->db->like($sterm,$search);
	            }
	            else
	            {
	                $this->db->or_like($sterm,$search);
	            }
	            $x++;
	        }                 
	    }
	    $this->db->limit($length,$start);
	    $pengguna = $this->db->get("login");
	    // echo $this->db->last_query();exit();
	    $data = array();
	    foreach($pengguna->result() as $r)
	    {
	    	$status = $this->admin->getExpired($r->user_id,$r->apps);
	    	$btn_aktifkan = '<button type="button" class="btn btn-danger btn-sm" onclick="aktifkan(this)"  data-id="'.$r->user_id.'" data-apps="'.$r->apps.'" data-aktif="0" >
                          Matikan
                        </button>';
                $ex = 'expired';
	    	$ex='';
            if($status['day_diff']< 0 || $r->aktif == 0){
             	$btn_aktifkan = '<button type="button" class="btn btn-success btn-sm" onclick="aktifkan(this)"  data-id="'.$r->user_id.'" data-apps="'.$r->apps.'" data-aktif="1" >
                          Aktifkan
                        </button>';
                $ex = 'expired';
                if($r->aktif == 0)
                	$ex = 'non aktif';
            }
	      	$data[] = array( 
	                    $r->user_id,
	                    $r->apps,
	                    $r->nama_pengguna,
	                    $r->email,
	                    $r->last_update_date,
	                    ($r->eternal == 1 || $r->aktif == 0) ? '-' : $status['day_diff'] ,
	                    ($r->eternal == 1) ? 'Ya' : '-' ,
	                    $ex,
	                    '<button type="button" rel="tooltip" class="btn btn-sm " onclick="editmodal(this)"  data-id="'.$r->user_id.'" data-apps="'.$r->apps.'" >
                          Edit
                        </button> '. $btn_aktifkan .'
                        <button type="button" rel="tooltip" class="btn btn-warning btn-sm " onclick="logmodal(this)"  data-id="'.$r->user_id.'" data-apps="'.$r->apps.'" >
                          Log
                        </button> ',
	               );
	    }
	    $total_pengguna = $this->totalPengguna();

	    $output = array(
	        "draw" => $draw,
	        "recordsTotal" => $total_pengguna,
	        "recordsFiltered" => $total_pengguna,
	        "data" => $data
	    );
	    echo json_encode($output);
	    exit();
	}

	public function totalPengguna()
  	{
      $query = $this->db->select("COUNT(*) as num")->get("login");
      $result = $query->row();
      if(isset($result)) return $result->num;
      return 0;
  	}

	public function edit(){
        $id = $this->input->get('id');
        $apps = $this->input->get('apps'); 
        $arr_par = array('user_id' => $id, 'apps' => $apps);
        $data = $this->admin->getmaster('login',$arr_par);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function Save()
    {       
        
        $response = [];
        $response['error'] = TRUE; 
        $response['msg']= "Gagal menyimpan.. Terjadi kesalahan pada sistem";
        $recLogin = $this->session->userdata('user_id');
        $data = array(
            'email'  			=> $this->input->get('email'),
            'eternal'      		=> $this->input->get('eternal'),             
        );
        if(!empty($this->input->get('password'))){
        	$data['password'] = $this->input->get('password');
        	$data['last_update_date'] = date('Y-m-d');
        }

        $this->db->trans_begin();

        $this->db->set($data);
        $this->db->where('user_id', $this->input->get('user_id'));
        $this->db->where('apps', $this->input->get('apps'));
        $result  =  $this->db->update('login');  

        if(!$result){
            print("<pre>".print_r($this->db->error(),true)."</pre>");
        }else{
            $response['error']= FALSE;
            if(!empty($this->input->get('password'))){
	        	$data_log = array(
                    'user_id'           => $this->input->get('user_id'),
                    'last_password'     => $this->input->get('password'),  
                    'log_by'            => $recLogin, 
                    'remark'            => 'Ganti Password' ,
                    'apps_log'      	=> $this->input->get('apps')
                );
                $this->db->insert('log_tbl', $data_log);
	        }
        }
        

        $this->db->trans_complete();
                            
      $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
	public function aktifkan()
    {       
        
        $response = [];
        $response['error'] = TRUE; 
        $response['msg']= "Gagal menyimpan.. Terjadi kesalahan pada sistem";
        $recLogin = $this->session->userdata('user_id');
        $data = array(
            'aktif'  			=> $this->input->get('aktif'),
            'last_update_date'  => date('Y-m-d'),             
        );
        if($this->input->get('aktif') == '1')
        	$data['password'] =  $this->input->get('password');

        $this->db->trans_begin();

        $this->db->set($data);
        $this->db->where('user_id', $this->input->get('user_id'));
        $this->db->where('apps', $this->input->get('apps'));
        $result  =  $this->db->update('login');  

        if(!$result){
            print("<pre>".print_r($this->db->error(),true)."</pre>");
        }else{
            $response['error']= FALSE;
        	$data_log = array(
                'user_id'           => $this->input->get('user_id'),
                'log_by'            => $recLogin, 
                'remark'            => 'Mengaktifkan User' ,
                'apps_log'      	=> $this->input->get('apps')
            );
            if($this->input->get('aktif') == '0'){
            	$data_log['remark'] = 'Non aktifkan User' ;
            }else{
            	$data_log['last_password']     = $this->input->get('password');
            }
            $this->db->insert('log_tbl', $data_log);
        }
        

        $this->db->trans_complete();
                            
      $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
}
