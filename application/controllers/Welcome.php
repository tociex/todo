<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
   var $api = "";

	function __construct(){
		parent::__construct();
		$this->api = "https://todo.api.devcode.gethired.id";
		$this->load->library('session');
		$this->load->library('curl');
		$this->load->helper('form');
		$this->load->helper('url');
	}

	function index(){
         	$data['title'] = "Todolist";
		try {
			$this->curl->ssl(false);
		    $apiResponse = $this->curl->simple_get($this->api.'/activity-groups');
		    $this->curl->ssl(true);
    
	    if ($apiResponse === false) {
	        throw new Exception("API request failed: " . $this->curl->error_string);
	    }

		    $data['todos'] = json_decode($apiResponse);
		    

		    $this->load->view('welcome_message', $data);
		} catch (Exception $e) {
		     
		    echo "Error: " . $e->getMessage();
		}
	}


	public function insert() {
	    if(isset($_POST['submit'])){
	        $data = array(
	            'title' => $this->input->post('todo'),
	            'activity_group_id' => 1,
	            'is_active' => true,
	        );

	        $this->curl->ssl(false);
	        $insert = $this->curl->simple_post($this->api.'/todo-item', $data, array(CURLOPT_BUFFERSIZE => 10));

	        $this->curl->ssl(true);

	        if ($insert === false) {
	            // Penanganan kesalahan cURL
	            echo 'CURL Error: ' . curl_error($this->curl);
	            echo 'CURL Error Code: ' . curl_errno($this->curl);
	        } else {
	            // Penanganan respons dari API
	            echo 'Response from API: ' . $insert;

	            // Parse respons JSON menjadi objek PHP (jika respons adalah JSON valid)
	            $response = json_decode($insert);

	            if ($response === null) {
	                echo 'Error parsing JSON response from API';
	            } else {
	                // Handle the response from the API
	                echo 'ID from API response: ' . $response->id;
	                // ... (Lanjutkan penanganan respons dari API sesuai kebutuhan Anda)
	            }
	        }

	        $this->find_todo($response->id);
	    }
	}

 


	public function edit() {
		if (!$this->input->is_ajax_request()) {
			exit('not allowed');
			return false;
		}
		$id = $this->input->post('id');
		$todo = $this->find_todo($id);
		echo json_encode($todo);
	}


	public function done() {
		if (!$this->input->is_ajax_request()) {
			exit('not allowed');
			return false;
		}
		$id = $this->input->post('id');

	 
			$this->curl->ssl(false);
			$delete = $this->curl->simple_delete($this->api.'/todo-items', array('id'=> $id), array(CURLOPT_BUFFERSIZE => 10));
			$this->curl->ssl(true);

	}

	public function find_todo($id) {


		


		$this->curl->ssl(false); // Matikan verifikasi SSL
		$todo = $this->curl->simple_get($this->api.'/todo-items' , array('id'=> $id), array(CURLOPT_BUFFERSIZE => 10));
		$this->curl->ssl(true); // Aktifkan verifikasi SSL kembali

		$decodedTodo = json_decode($todo);

		



	}



 
          

	 
}
