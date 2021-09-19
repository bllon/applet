<?php

$id = $_GET['id'];

$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

$sql="delete from topic where id=".$id;

$res=$conn->query($sql);

if($res){
	echo "<script>alert('删除成功!');location.href='topic.php';</script>";
	exit;
}else{
	echo "<script>alert('删除失败!');history.back();</script>";
	exit;
}