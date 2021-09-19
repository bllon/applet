<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type:text/html;charset=utf-8");
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

$class_name = $_POST['class_name'];

//删除分类
$sql = 'delete from jyybj_class where class_name="'.$class_name.'" and session_id="'.$_POST['session_id'].'"';
$res = $conn->query($sql);

//删除该分类下的所有笔记
$sql = 'delete from jyybj_note where class_name="'.$class_name.'" and session_id="'.$_POST['session_id'].'"';
$res = $conn->query($sql);

if($res){
	echo 0;
}else{
	echo 1;
}

?>