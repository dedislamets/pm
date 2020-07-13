<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Log extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
	    $this->load->model('admin');
	   
	}
	public function index()
	{		
		if($this->admin->logged_id()){
        	
            $data['title'] = 'Home';
            $data['main'] = 'log/list';
			$data['js'] = 'script/log';
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
	        0=>'login.user_id',
	        1=>'nama_pengguna',
	        2=>'log_date',
	        3=>'log_by',
	        4=>'remark',
	    );
	    $valid_sort = array(
	        0=>'user_id',
	        1=>'nama_pengguna',
	        2=>'log_date',
	        3=>'log_by',
	        4=>'remark',
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
	    $this->db->from('log_tbl');
	    // $this->db->join('login', 'login.user_id = log_tbl.user_id');
	    $this->db->order_by('log_date' , 'DESC');
	    if(!empty($this->input->get("user_id"))){
	    	$this->db->where(array('user_id' => $this->input->get("user_id"), 'apps_log' => $this->input->get("apps")));
	    }

	  //   $sql = "select * from (
			// select ROW_NUMBER() OVER(ORDER BY log_date DESC) AS CI_rownum,
			// 	A.apps,A.nama_pengguna,A.last_update_date,B.* 
			// from login A inner join log_tbl B on A.user_id=B.user_id
			// )temp
			// where CI_rownum between 11 and 20
			// order by log_date DESC";

	  //   $query = $this->db->query($sql);


	    $pengguna = $this->db->get();
	    
	    $data = array();
	    foreach($pengguna->result() as $r)
	    {
	      	$data[] = array(
	                    $r->user_id,
	                    $r->apps_log,
	                    $r->log_date,
	                    $r->log_by,
	                   	$r->remark,
	               );
	    }

	    $total_log = $this->totalLog(array('user_id' => $this->input->get("user_id"), 'apps_log' => $this->input->get("apps")));

	    $output = array(
	        "draw" => $draw,
	        "recordsTotal" => $total_log,
	        "recordsFiltered" => $total_log,
	        "data" => $data
	    );
	    echo json_encode($output);
	    exit();
	}

	public function totalLog($arr=[])
  	{
  		// print("<pre>".print_r($arr,true)."</pre>");exit();
  		$this->db->select("COUNT(*) as num");
      	if(!empty($arr['user_id'])){
	    	$this->db->where($arr);
	  	}
      	$query = $this->db->get("log_tbl");

      	$result = $query->row();
      if(isset($result)) return $result->num;
      return 0;
  	}
}
