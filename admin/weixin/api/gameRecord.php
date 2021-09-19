<?php

$userInfo = json_decode($_POST['userInfo'],true);
$score = $_POST['myScore'];
$myId = $_POST['myId'];

$conf = require('../database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

if($myId == ''){
	//第一次请求
	//分配一个myId
	$id = uniqid();

	$sql = "insert into user values('".$id."','".$userInfo['nickName']."','".$userInfo['avatarUrl']."','".$userInfo['gender']."','".$userInfo['city']."','".$score."')";

	// print_r($sql);
	// exit;
	$res=$conn->query($sql);

	$data = ['statu'=>$res,'id'=>$id];
	print_r(json_encode($data));
}else{
	// print_r($myId);exit;
	$sql = "select score from user where id='".$myId."'";
	$res=$conn->query($sql);
	$data = mysqli_fetch_assoc($res);

	$score = $data['score'] + $score;
	
	$sql = "update user set score='".$score."' where id='".$myId."'";
	$res=$conn->query($sql);

	$data = ['statu'=>$res];
	print_r(json_encode($data));
}


// insert into user values('5ead061017a56','IM','http://wx.qlogo.cn/mmopen/vi_32/t80hOPsqvAzEYvLcw5sQpYTicUQTIWwpVqcfDq7PmOZ3JxxI920vzJTd2BEdc0u1Xlss8xQ1RTGc5qLRibj6zHhw/132','1','1','null','20')