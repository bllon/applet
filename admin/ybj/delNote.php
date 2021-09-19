<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type:text/html;charset=utf-8");
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

$id = $_POST['id'];

//删除笔记
$sql = 'delete from jyybj_note where id='.$id.' and session_id="'.$_POST['session_id'].'"';
$res = $conn->query($sql);
if($res){
	echo 0;
}else{
	echo 1;
}

?>