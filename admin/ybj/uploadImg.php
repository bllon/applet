<?php
	ini_set('date.timezone','Asia/Shanghai');
	header("Content-Type:text/html;charset=utf-8");
	$conf = require('database.php');
	$data = date('Y/m/d',time());



	$path = dirname(__FILE__).'/img/'.$_POST['session_id'].'/'.$data.'/';

	if(!is_dir($path)){
		mkdir($path,0777,true);
	}
	
	$http_path = 'http://'.$conf['hostName'].'/ybj/img/'.$_POST['session_id'].'/'.$data.'/'.$_FILES['file']['name'];
	$file_path = dirname(__FILE__).'/img/'.$_POST['session_id'].'/'.$data.'/'.$_FILES['file']['name'];

	$res = move_uploaded_file($_FILES['file']['tmp_name'],$file_path);

	$data = [
		'src'=> $http_path
	];
	echo json_encode($data);