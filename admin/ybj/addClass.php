<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type:text/html;charset=utf-8");
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

$session_id = $_POST['session_id'];
$class_name = $_POST['class_name'];

//查询分类是否存在
$sql = 'select count(*) as num from jyybj_class where class_name="'.$class_name.'" and session_id="'.$_POST['session_id'].'"';
$res = $conn->query($sql);
$rs = mysqli_fetch_assoc($res);

if($rs['num'] > 0){
	echo 2;//已存在该分类
	exit;
}

//新增
$sql = 'insert into jyybj_class (session_id, class_name) values("'.$session_id.'","'.$class_name.'")';

$res = $conn->query($sql);
if($res){
	//添加成功
	echo 0;
	exit;
}else{
	//添加失败
	echo 1;
	exit;
}

?>