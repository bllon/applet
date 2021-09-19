<?php

$conf = require('../database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

$sql="select id from topic";

$res=$conn->query($sql);
$data = mysqli_fetch_all($res,MYSQLI_ASSOC);

// 查询可用id
$ids = [];
foreach ($data as $k => $v) {
	$ids[] = (int)$v['id'];
}

$num = count($ids);
//打乱ids
shuffle($ids);

//随机取n个
$n = $num >= 10 ? 10 : $num;

$ids = array_slice($ids, 0,$n);

$ids = implode($ids, ',');
// var_dump($ids);

$sql="select * from topic where id in (".$ids.")";

$res=$conn->query($sql);
$data = mysqli_fetch_all($res,MYSQLI_ASSOC);

foreach ($data as $k => $v) {
	$data[$k]['options'] = json_decode($data[$k]['options']);
	$data[$k]['answer']--;
}

print_r(json_encode($data));