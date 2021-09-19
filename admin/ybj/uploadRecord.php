<?php
	ini_set('date.timezone','Asia/Shanghai');
	$conf = require('database.php');
	$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

	if(!$conn){
	  exit('连接服务器失败: '.$conn->error());
	}
	$data = date('Y/m/d',time());

	if(isset($_POST['flag']) && boolval($_POST['flag'])){
		//保存音频文件
		$path = dirname(__FILE__).'/record/'.$_POST['session_id'].'/'.$data.'/';

		if(!is_dir($path)){
			mkdir($path,0777,true);
		}
		
		$http_path = 'http://'.$conf['hostName'].'/ybj/record/'.$_POST['session_id'].'/'.$data.'/'.$_FILES['file']['name'];
		$file_path = dirname(__FILE__).'/record/'.$_POST['session_id'].'/'.$data.'/'.$_FILES['file']['name'];

		$res = move_uploaded_file($_FILES['file']['tmp_name'],$file_path);
	}else{
		$http_path = $_POST['content'];
	}	

	//保存笔记信息
	$session_id = $_POST['session_id'];
	$title = $_POST['title'];
	$class_name = $_POST['class_name'];

	$content = $http_path;

	$type = 1;//语音类型
	$sort = $_POST['sort'] == 'true' ? true:false;

	$label = trim($_POST['label']);

	if(isset($_POST['id']) && $_POST['id']!=''){
		//修改
		//添加标签
		$labelArr = explode(',', $label);
		foreach($labelArr as $v){
			if(trim($v) != ''){
				$sql = 'insert into jyybj_label (session_id,name) values("'.$session_id.'","'.$v.'")';
				$s = $conn->query($sql);
			}	
		}
		$update_time = time();
		if($sort){
			//置顶
			$sql = 'select max(sort) as id from jyybj_note where session_id="'.$session_id.'"';
			$s = $conn->query($sql);
			if($s){
				$row = mysqli_fetch_assoc($s);
				$sort = $row['id'] + 1;
			}else{
				$sort = 0;
			}
			//执行修改
			$sql = 'update jyybj_note set title="'.$title.'",content="'.$content.'",type='.$type.',class_name="'.$class_name.'",sort='.$sort.',label="'.$label.'",update_time='.$update_time.' where id='.$_POST['id'];
		}else{
			//不置顶
			//执行修改
			$sql = 'update jyybj_note set title="'.$title.'",content="'.$content.'",type='.$type.',class_name="'.$class_name.'",label="'.$label.'",update_time='.$update_time.' where id='.$_POST['id'];		
		}

	}else{
		//新增
		//添加标签
		$labelArr = explode(',', $label);
		foreach($labelArr as $v){
			$v = trim($v);
			if($v != ''){
				$sql = 'insert into jyybj_label (session_id,name) values("'.$session_id.'","'.$v.'")';
				$s = $conn->query($sql);
			}
		}
		$create_time = time();
		//置顶
		$sql = 'select max(sort) as id from jyybj_note where session_id="'.$session_id.'"';
		$s = $conn->query($sql);
		if($s){
			$row = mysqli_fetch_assoc($s);
			$sort = $row['id'] + 1;
		}else{
			$sort = 0;
		}
		$sql = 'insert into jyybj_note (session_id, title, content, type, class_name, sort, label, create_time, update_time) values("'.$session_id.'","'.$title.'","'.$content.'",'.$type.',"'.$class_name.'",'.$sort.',"'.$label.'",'.$create_time.','.$create_time.')';

	}
	$res = $conn->query($sql);

	if($res){
		//添加成功
		$data = [
			'src'=> $http_path,
			'code'=>0
		];
	}else{
		$data = [
			'src'=> $http_path,
			'code'=>1
		];
	}

	echo json_encode($data);