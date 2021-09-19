<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type:text/html;charset=utf-8");
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

if(isset($_POST['session_id']) && $_POST['session_id'] != ""){
	$sql = 'select * from jyybj_user where session_id="'.$_POST['session_id'].'"';
	$result = $conn->query($sql);
	$rs = mysqli_fetch_assoc($result);
	$username = $rs['nickName'];


	if(isset($_POST['name']) && $_POST['name']!=''){
		//查询分类笔记

		if(isset($_POST['keyword']) && $_POST['keyword']!=''){
			//查询分类加关键字
			$sql ='select * from jyybj_note where session_id="'.$_POST['session_id'].'" and class_name="'.$_POST['name'].'" and title like "%'.$_POST['keyword'].'%" order by sort desc' ;
		}else{
			$sql ='select * from jyybj_note where session_id="'.$_POST['session_id'].'" and class_name="'.$_POST['name'].'" order by sort desc' ;
		}
		
	}else if(isset($_POST['label']) && $_POST['label']!=''){
		//查询标签笔记

		if(isset($_POST['keyword']) && $_POST['keyword']!=''){
			//查询关键字加标签
			$sql ='select * from jyybj_note where session_id="'.$_POST['session_id'].'" and title like "%'.$_POST['keyword'].'%" and label like "%'.$_POST['label'].'%" order by sort desc' ;
		}else{
			$sql ='select * from jyybj_note where session_id="'.$_POST['session_id'].'" and label like "%'.$_POST['label'].'%" order by sort desc' ;
		}
	}else{
		//查询所有笔记
		if(isset($_POST['keyword']) && $_POST['keyword']!=''){
			$sql = 'select * from jyybj_note where session_id="'.$_POST['session_id'].'" and title like "%'.$_POST['keyword'].'%" order by sort desc' ;
		}else{
			$sql = 'select * from jyybj_note where session_id="'.$_POST['session_id'].'" order by sort desc' ;
		}
		
	}


 	$result = $conn->query($sql);
 	$list = [];
 	while($rs = mysqli_fetch_assoc($result)){ 	   
 	   $rs['create_time'] = date('Y-m-d H:i:s',$rs['create_time']);
 	   $rs['update_time'] = date('Y-m-d H:i:s',$rs['update_time']);
 	   $rs['nickName'] = $username;
 	   if($rs['type'] == 0){
 	   		$rs['type'] = '文本';
 	   		$rs['content'] = htmlspecialchars_decode($rs['content']);
 	   		$rs['content'] = strip_tags($rs['content']);
 	   }else if($rs['type'] == 1){
 	   		$rs['type'] = '语音';
 	   }
       $list[] = $rs;
   	}
}
echo json_encode($list);
?>