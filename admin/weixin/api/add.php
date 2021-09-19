<?php

header("Content-type: text/html; charset=utf-8");
//1.实例化对象
$redis = new Redis();

//2.定义主机和端口
$host = '127.0.0.1';
$port = 6379;

//3.连接redis服务器
$redis->connect($host , $port);

//查询题目个数
$num = $redis->get('timuNum');

$redis2 = new Redis();
$redis2->connect('119.29.96.243' , 6379);
$redis2->auth('xubei`123');

$redis2->set('timuNum',$num);

$conf = require('../database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

if($num == false){
	echo '没有题目';
}else{
	echo '有'.$num.'道题目如下<br/>';
	for($i=1;$i<=$num;$i++){
		$res = $redis->get('timu'.$i);
		$redis2->set('timu'.$i,$res);

		$data = json_decode($res,true);

		$title = $data['title'];
		$options = json_encode($data['options']);
		$answer = $data['answer']+1;

		$title = mysqli_real_escape_string($conn,$title);
		$options = mysqli_real_escape_string($conn,$options);

		//查询最后一条记录id
		$sql = "select MAX(id) as id from topic";
		$res=$conn->query($sql);
		$data = $res->fetch_assoc();

		if($data['id'] == null){
			$id = 1;
		}else{
			$id = $data['id'] + 1;
		}

		$sql="insert into topic values(".$id.",'".$title."','".$options."',".$answer.")";

		// var_dump($sql);
		$res=$conn->query($sql);
		var_dump($res);
	}
}