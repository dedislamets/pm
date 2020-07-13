<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Model
{
    function logged_id()
    {
        return $this->session->userdata('user_id');
    }

    function check_login($table, $field1, $field2)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($field1);
        $this->db->where($field2);
        $this->db->limit(1);
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($query->num_rows() == 0) {
            return FALSE;
        } else {
            return $query->result();
        }
    }
    function api_getmaster($tabel, $where='', $variable='*', $orderby=''){
        $sql = "SELECT ". $variable ." FROM ". $tabel;
        if($where !=''){
            $sql.= " WHERE ". $where ;
        }

        if($orderby !=''){
            $sql.= " ORDER BY ". $orderby ;
        }

        $query = $this->db->query($sql);
        return $query->result();    
    }

    function getmaster($tabel,$where='',$order=''){
        $this->db->from($tabel);
        if($where !=""){
            $this->db->where($where);
        }
        if($order !=""){
            $this->db->order_by($order);
        }
        $query = $this->db->get();
        return $query->result();    
    }

    function getExpired($user_id, $apps){
        $arr_par = array('user_id' => $user_id, 'apps' => $apps);
        $checking = $this->getmaster('login', $arr_par);
        $response = [];
        if (!empty($checking)) {
            $range    = $this->api_getmaster('setup');
            $range    = $range[0]->range_date;
            $last_log = $this->api_getmaster('log_tbl',"user_id='". $user_id ."'",'top 1 *','log_date desc');
            $last_log = $last_log[0]->log_date;
            $expired_date = date('Y-m-d', strtotime( "$last_log + $range day" ));  

            // print("<pre>".print_r($response,true)."</pre>");exit();

            foreach ($checking as $apps) {
                $response = array(
                    'last_update_date' => $last_log,
                    'expired'       => $apps->eternal == 1 ? '-' : $expired_date,
                    'day_diff'      => date_diff(date_create(date('Y-m-d')), date_create($expired_date))->format("%R%a"))
                ;
            }
        }

        return $response;
    }
    
    public function get($username){
        $this->db->where('username', $username); 
        $result = $this->db->get('pj_user')->row(); 

        return $result;
    }

}