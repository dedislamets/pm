<?php

    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
    header('Content-Type: application/json');

// Default
// $users = [
//     ['id' => 0, 'name' => 'John', 'email' => 'john@example.com'],
//     ['id' => 1, 'name' => 'Jim', 'email' => 'jim@example.com'],
// ];

// $id = $this->get( 'id' );

// if ( $id === null )
// {
//     if ( $users )
//     {
//         $this->response( $users, 200 );
//     }
//     else
//     {
//         $this->response( [
//             'status' => false,
//             'message' => 'No users were found'
//         ], 404 );
//     }
// }
// else
// {
//     if ( array_key_exists( $id, $users ) )
//     {
//         $this->response( $users[$id], 200 );
//     }
//     else
//     {
//         $this->response( [
//             'status' => false,
//             'message' => 'No such user found'
//         ], 404 );
//     }
// }
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Api extends RestController  {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin');
    }

    public function login_get()
    {
        $data = array(
            'user_id'           => $this->input->get('username'),
            'apps'              => $this->input->get('apps'),
            'nama_pengguna'     => $this->input->get('nama_pengguna'),
            'email'             => $this->input->get('email'),
            'password'          => $this->input->get('password'),
            'last_update_date'  => date('Y-m-d'),                           
        );

        $checking = $this->admin->check_login('login', array('user_id' => $data['user_id']), array('apps' => $data['apps']));
        if (!empty($checking)) {
            $range    = $this->admin->api_getmaster('setup');
            $range    = $range[0]->range_date;
            $last_log = $this->admin->api_getmaster('log_tbl',"user_id='". $data['user_id'] ."' and apps_log='". $data['apps'] ."' ",'top 1 *','log_date desc');
            $last_log = $last_log[0]->log_date;
            $expired_date = date('Y-m-d', strtotime( "$last_log + $range day" ));
            // $expired_date = "2020-06-08";

            $app_tbl    = $this->admin->api_getmaster('app_tbl',"app_code='". $data['apps'] ."'");

            // print("<pre>".print_r($last_log,true)."</pre>");

            foreach ($checking as $apps) {
                $response = array(
                    'user_id'       => $apps->user_id,
                    'apps'          => $apps->apps,
                    'nama_pengguna' => $apps->nama_pengguna,
                    'email'         => $apps->email,
                    'aktif'         => $apps->aktif,
                    'eternal'       => $apps->eternal,
                    'last_update_date' => $last_log,
                    'base_url'      => $app_tbl[0]->base_url,
                    'expired'       => $apps->eternal == 1 ? '-' : $expired_date,
                    'day_diff'      => date_diff(date_create(date('Y-m-d')), date_create($expired_date))->format("%R%a"))
                ;

                $this->response($response, 200 );
                exit();
            }
        }else{
            $result  = $this->db->insert('login', $data);
            $lastid = $this->db->insert_id();

            if($lastid !=null){
                $data_log = array(
                    'user_id'           => $this->input->get('username'),
                    'last_password'     => $this->input->get('password'),  
                    'log_by'            => $this->input->get('by'), 
                    'remark'            => 'Insert Data Pertama' ,
                    'apps_log'          => $this->input->get('apps')
                );
                $this->db->insert('log_tbl', $data_log);
            }
            
            $response['error']= FALSE;
            $response['id']= $lastid;
            $this->response($response, 200 );
            exit();
        }
        
        
    }
}