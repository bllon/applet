<?php

$conf = require('../database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

$sql="select * from user order by score desc";

$res=$conn->query($sql);

$data = mysqli_fetch_all($res,MYSQLI_ASSOC);
print_r(json_encode($data));