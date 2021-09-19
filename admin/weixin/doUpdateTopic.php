<?php

$id = $_POST['id'];

$title = trim($_POST['title']);
$option1 = trim($_POST['option1']);
$option2 = trim($_POST['option2']);
$option3 = trim($_POST['option3']);
$option4 = trim($_POST['option4']);
$answer = trim($_POST['answer']);

if($title == ''){
	echo "<script>alert('请填写题目!');history.back();</script>";
	exit;
}

if($option1 == ''){
	echo "<script>alert('请填写选项!');history.back();</script>";
	exit;
}

if($answer == ''){
	echo "<script>alert('请填写答案!');history.back();</script>";
	exit;
}

$options = [];
$options[] = $option1;
$options[] = $option2;
$options[] = $option3;

if($option4 != ''){
	$options[] = $option4;
}

$options = json_encode($options);

$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}
$title = mysqli_real_escape_string($conn,$title);
$options = mysqli_real_escape_string($conn,$options);


//修改
$sql="update topic set title='".$title."',options='".$options."',answer=".$answer." where id=".$id;

$res=$conn->query($sql);

if($res){
	echo "<script>alert('修改成功!');location.href='topic.php';</script>";
	exit;
}else{
	echo "<script>alert('修改失败!');history.back();</script>";
	exit;
}