<?php 

class API_UploadIMG
{	
	private static $path = "upload";
	private static $token = "p5ZVysTqePcliKwMZxL5M1FXBpraVY";

	public function index(){
		header("Content-Type: application/json");
		$method = $_SERVER['REQUEST_METHOD'];

		if($method != "POST") return;

		$headers = apache_request_headers(); 
		if(!isset($headers['Authorization'])) return;
		$token = $headers['Authorization'];

		
		if($token != "Bearer ".self::$token){
			echo "Permission denied";
			return;
		}

		if (isset($_FILES['fileUpload']) && isset($_POST['name']) && $_POST['pathImage']) {

			$upload = $this->upload($_POST['name'],$_POST['pathImage'],$_FILES['fileUpload']);

		} else {
			echo json_encode(['status'=>0,'msg'=>'Error request'],http_response_code(400));
			return;
		}

		if($upload['success'] == 1){
			echo json_encode(['status'=>1,'msg'=>'Uploaded successfully! - '.$upload['data']],http_response_code(200));
		} else {
			echo json_encode(['status'=>0,'msg'=>"error upload file - ".$upload],http_response_code(400));
		}
	}

	private function upload($image_name,$target_dir,$file){
		if (isset($image_name) && isset($target_dir) && isset($file)) {

			$error = '';
			$tmp_name = $file['tmp_name'];
			$target_file = $target_dir.$image_name;

			if($_FILES['fileUpload']['size'] > 5242880){
	  		 $error ="Only upload files under 5M";
	  		}

	   		$file_type = pathinfo($_FILES['fileUpload']['name'], PATHINFO_EXTENSION);
	   
	   		$file_type_allow = array('png','jpg','jpeg');

		    if(!in_array(strtolower($file_type),$file_type_allow)){
		      $error = "Only upload image!";
		    }
	 
		    if(empty($error)){
		    	if (!file_exists(self::$path.$target_dir)){
		            mkdir(self::$path.$target_dir, 0777, true);
		        }
				move_uploaded_file($tmp_name,self::$path.$target_file);	
				return ['success'=>1,'data'=>self::$path.$target_file];
			} else {
				return $error;
			}			
		} else {
			return "errRequest";
		}
	}
}

$upload = new API_UploadIMG;
return $upload->index();
