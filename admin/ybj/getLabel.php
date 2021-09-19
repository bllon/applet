<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type:text/html;charset=utf-8");
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

if(isset($_POST['session_id']) && $_POST['session_id'] != ""){
	$sql = 'select distinct(name) from jyybj_label where session_id="'.$_POST['session_id'].'"';
	$result = $conn->query($sql);

 	$list = [];
 	while($rs = mysqli_fetch_assoc($result)){
       $list[] = $rs;
   	}
}
echo json_encode($list);
?>