<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type:text/html;charset=utf-8");
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

$id = $_POST['id'];

//查询笔记详情
$sql = 'select * from jyybj_note where id='.$id;
$res = $conn->query($sql);
$rs = mysqli_fetch_assoc($res);

if(isset($_POST['is_share']) && boolval($_POST['is_share'])){
	$session_id = $_POST['session_id'];
}else{
	$session_id = $rs['session_id'];
}

//查询用户名
$sql = 'select * from jyybj_user where session_id="'.$session_id.'"';
$result = $conn->query($sql);
$row = mysqli_fetch_assoc($result);
$username = $row['nickName'];

$rs['content'] = htmlspecialchars_decode($rs['content']);
$rs['create_time'] = date('Y-m-d H:i:s',$rs['create_time']);
$rs['update_time'] = date('Y-m-d H:i:s',$rs['update_time']);
$rs['nickName'] = $username;
if($rs['type'] == 0){
	$rs['type'] = '文本';
}

echo json_encode($rs);

?>