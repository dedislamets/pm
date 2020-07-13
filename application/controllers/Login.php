<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin');
        $this->load->helper('url');
        $this->load->helper('string');
    }

    public function index()
    {
        if($this->admin->logged_id())
        {
            redirect("home");

        }else{

            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            $this->form_validation->set_message('required', '<div class="alert alert-danger" >
                <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

            if ($this->form_validation->run() == TRUE) {

                $username = $this->input->post("username", TRUE);
                $password = sha1($this->input->post('password'));

                $checking = $this->admin->check_login('pj_user', array('username' => $username), array('password' => $password));

                if ($checking != FALSE) {
                    foreach ($checking as $apps) {
                        $session_data = array(
                            'user_id'   => $apps->id_user,
                            'user_name' => $apps->username,
                            'nama' => $apps->nama,
                            'role' => $apps->id_akses,
                        );
                        //set session userdata
                        $this->session->set_userdata($session_data);

                        redirect('dashboard/');

                    }
                }else{

                    $data['error'] = '<div class="alert alert-danger">
                        <div class="header"><b><i class="fa fa-exclamation-circle"></i></b> Username atau Password salah!</div></div>';
                    $this->load->view('login', $data);
                }

            }else{
                $this->load->view('login');
            }

        }
    }

    public function update()
    {

        $this->form_validation->set_rules('last_password', 'last_password', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        $this->form_validation->set_rules('ulangi_password', 'ulangi_password', 'required');
        $this->form_validation->set_message('required', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

        if ($this->form_validation->run() == TRUE) {

            $app_tbl  = $this->admin->api_getmaster('app_tbl',"app_code='". $this->input->post('apps') . "'");
            $login_tbl  = $this->admin->api_getmaster('login',"user_id='". $this->input->post('user_id') . "'");

            if($this->input->post('last_password') !== $login_tbl[0]->password){
                $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
                    <div class="header"><b><i class="fa fa-exclamation-circle"></i> Password Terakhir</b> tidak cocok !!</div></div>');
                redirect($_SERVER['HTTP_REFERER']);
            }elseif ($this->input->post('password') !== $this->input->post('ulangi_password')) {
                $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
                    <div class="header"><b><i class="fa fa-exclamation-circle"></i> Password Baru</b> tidak sama dengan Ulangi Password Baru !!</div></div>');
                redirect($_SERVER['HTTP_REFERER']);
            }else{

                $new_password = $this->input->post('ulangi_password');
                $qry = '';
                // print("<pre>".print_r($this->input->post(),true)."</pre>");exit();
                if(strtolower($this->input->post('apps'))=='dm'){
                    if(!empty($app_tbl[0]->encrypt_type))
                        $new_password = sha1( $new_password); 
             
                    $data = array( $app_tbl[0]->field_password  =>  $new_password );

                    //update tabel aslinya
                    $dm = $this->load->database('dm', TRUE);
                    $dm->set($data);
                    $dm->where($app_tbl[0]->key_tbl , $this->input->post('user_id'));
                    $result  =  $dm->update($app_tbl[0]->tabel_user); 
                    $qry    =  $dm->last_query();
                
                    // print("<pre>".print_r($qry,true)."</pre>");exit();

                    if(!$result){
                         print("<pre>".print_r($dm->error(),true)."</pre>");
                    }else{
                        //update tabel login pm
                        $data = array( 
                            'password'         =>  $this->input->post('ulangi_password'),
                            'last_update_date' =>  date('Y-m-d')
                        );
                        $this->db->set($data);
                        $this->db->where('user_id' , $this->input->post('user_id'));
                        $this->db->update('login');

                        //update token
                        $data = array( 
                            'valid'         =>  1,
                        );
                        $this->db->set($data);
                        $this->db->where(array('user_id' => $this->input->post('user_id'), 'token_code' => $this->input->post('token')));
                        $this->db->update('token_tbl');

                        //update tabel log pm
                        $data = array( 
                            'user_id'       =>  $this->input->post('user_id'),
                            'log_by'        =>  empty($this->input->post('by')) ? $this->input->post('user_id') : $this->input->post('by'),
                            'last_password' =>  $this->input->post('ulangi_password'),
                            'remark'        => 'Perbarui Password',
                            'query'         => $qry,
                            'apps_log'      => $this->input->post('apps')
                        );
                        $this->db->insert('log_tbl',$data);

                        $this->session->set_flashdata('base_url', $app_tbl[0]->base_url);
                        redirect('lock/success');

                    }
                }
            }
            

        }else{

            $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> Password</b> tidak boleh kosong !!</div></div>');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function forgot()
    {

        $this->form_validation->set_rules('password', 'password', 'required');
        $this->form_validation->set_rules('ulangi_password', 'ulangi_password', 'required');
        $this->form_validation->set_message('required', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

        if ($this->form_validation->run() == TRUE) {

            $app_tbl  = $this->admin->api_getmaster('app_tbl',"app_code='". $this->input->post('apps') . "'");

            if ($this->input->post('password') !== $this->input->post('ulangi_password')) {
                $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
                    <div class="header"><b><i class="fa fa-exclamation-circle"></i> Password Baru</b> tidak sama dengan Ulangi Password Baru !!</div></div>');
                redirect($_SERVER['HTTP_REFERER']);
            }else{

                $new_password = $this->input->post('ulangi_password');
                $qry = '';
                // print("<pre>".print_r($this->input->post(),true)."</pre>");exit();
                if(strtolower($this->input->post('apps'))=='dm'){
                    if(!empty($app_tbl[0]->encrypt_type))
                        $new_password = sha1( $new_password); 
             
                    $data = array( $app_tbl[0]->field_password  =>  $new_password );

                    //update tabel aslinya
                    $dm = $this->load->database('dm', TRUE);
                    $dm->set($data);
                    $dm->where($app_tbl[0]->key_tbl , $this->input->post('user_id'));
                    $result  =  $dm->update($app_tbl[0]->tabel_user); 
                    $qry    =  $dm->last_query();

                    if(!$result){
                         print("<pre>".print_r($dm->error(),true)."</pre>");
                    }else{
                        //update tabel login pm
                        $data = array( 
                            'password'         =>  $this->input->post('ulangi_password'),
                            'last_update_date' =>  date('Y-m-d')
                        );
                        $this->db->set($data);
                        $this->db->where('user_id' , $this->input->post('user_id'));
                        $this->db->update('login');

                        //update token
                        $data = array( 
                            'valid'         =>  1,
                        );
                        $this->db->set($data);
                        $this->db->where(array('user_id' => $this->input->post('user_id'), 'token_code' => $this->input->post('token')));
                        $this->db->update('token_tbl');

                        //update tabel log pm
                        $data = array( 
                            'user_id'       =>  $this->input->post('user_id'),
                            'log_by'        =>  empty($this->input->post('by')) ? $this->input->post('user_id') : $this->input->post('by'),
                            'last_password' =>  $this->input->post('ulangi_password'),
                            'remark'        => 'Reset Akun',
                            'query'         => $qry,
                            'apps_log'      => $this->input->post('apps')
                        );
                        $this->db->insert('log_tbl',$data);

                        $this->session->set_flashdata('base_url', $app_tbl[0]->base_url);
                        redirect('lock/success');

                    }
                }
            }
            

        }else{

            $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> Password</b> tidak boleh kosong !!</div></div>');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function email()
    {

        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_message('required', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

        if ($this->form_validation->run() == TRUE) {

            $app_tbl  = $this->admin->api_getmaster('app_tbl',"app_code='". $this->input->post('apps') . "'");

            $data = array(
                'email'         =>  $this->input->post('email') . $this->input->post('long_email'),
            );
            $this->db->set($data);
            $this->db->where('user_id' , $this->input->post('user_id'));
            $this->db->update('login');

            $this->session->set_flashdata('base_url', $app_tbl[0]->base_url);
            redirect('lock/success');

        }else{

            $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> Email</b> tidak boleh kosong !!</div></div>');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function reset()
    {

        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_message('required', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> {field}</b> harus diisi</div></div>');

        if ($this->form_validation->run() == TRUE) {

            $data = array(
                'email'         =>  $this->input->post('email') . $this->input->post('long_email'),
                'apps'          => $this->input->post('apps')
            );

            $this->db->from('login');
            $this->db->where($data);
            $pengguna = $this->db->get();

            $arr = array();
            $message = $this->load->view('email/template_email','',true); 
            $note = $this->load->view('email/reset','',true); 

            $message=str_replace("#content#", $note, $message);
            $message=str_replace("#horizontal_line#", "<hr width=100% size=1 />", $message);

            $this->load->library('phpmailer_lib');
            $mail = $this->phpmailer_lib->load();

            if ( $pengguna->num_rows() > 0 ){
                foreach ($pengguna->result() as $apps) {
                    //Create token
                    $token = random_string('alnum', 8);
                    $data = array( 
                        'user_id'       =>  $apps->user_id,
                        'token_code'    =>  $token,
                        'tipe'          => 2
                    );
                    $this->db->insert('token_tbl',$data);

                    $app_tbl    = $this->admin->api_getmaster('app_tbl',"app_code='". $apps->apps ."'");

                    unset($arr["#link#"]);
                    $arr["nama_pengguna"] = $apps->nama_pengguna;
                    $arr["apps"]            = strtoupper($apps->apps);
                    $arr["username"]        = $apps->user_id;
                    $arr["email"]           = $apps->email;
                    $arr["by"]              = $apps->user_id;
                    $arr["base_url"]        = $app_tbl[0]->base_url;
                    $arr["token"]           = $token;
                    $arr['last_update_date'] = date("d M Y", strtotime($apps->last_update_date));
                    $arr["#link#"]          = "http://air.modena.co.id/pm/lock/forgot?" . http_build_query($arr);

                    print("<pre>".print_r($arr,true)."</pre>");

                    unset($arr["nama_pengguna"]);
                    unset($arr["apps"]);
                    unset($arr["username"]);
                    unset($arr["email"]);
                    unset($arr["by"]);
                    unset($arr["last_update_date"]);
                    unset($arr["token"]);

                    $arr["#nama_pengguna#"] = $apps->nama_pengguna;
                    $arr["#apps#"]          = strtoupper($apps->apps);
                    $template = str_replace( array_keys($arr), array_values($arr), $message );

                    $mail->addAddress($apps->email);
                    $mail->Subject = 'Permintaan reset akun '. strtoupper($apps->apps) .' anda .';
                    $mail->Body = $template;
                    

                    if(!$mail->send()){
                        echo 'Message could not be sent.';
                        echo 'Mailer Error: ' . $mail->ErrorInfo;
                    }else{
                        $this->session->set_flashdata('message_error', '<div class="alert alert-success" >
                        <div class="header"><b><i class="fa fa-exclamation-circle"></i> Email</b> berhasil dikirim!!</div></div>');
                        redirect($_SERVER['HTTP_REFERER']);
                    }   
                }
            }else{
                $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
                <div class="header"><b><i class="fa fa-exclamation-circle"></i> Email</b> tidak ditemukan !!</div></div>');
                redirect($_SERVER['HTTP_REFERER']);
            }

        }else{

            $this->session->set_flashdata('message_error', '<div class="alert alert-danger" >
            <div class="header"><b><i class="fa fa-exclamation-circle"></i> Email</b> tidak boleh kosong !!</div></div>');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function keluar()
    {
        $this->session->sess_destroy();
        redirect('login');
    }
}