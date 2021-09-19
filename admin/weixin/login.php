<?php
session_start();
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}
$sql='select *from admin';
$res=$conn->query($sql);
$data = $res->fetch_assoc();

if($data['username'] == $_POST['username'] && $data['password'] == $_POST['pwd']){
	$_SESSION['admin-name'] = $data['username'];
	header('location:index.php');
}else{
	echo "<script>alert('账号密码错误!');history.back();</script>";
}
$conn->close();