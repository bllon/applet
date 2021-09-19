<?php
	session_start();
	if(!isset($_SESSION['admin-name'])){
		echo "<script>alert('请登录用户!');location.href='html/login.html';</script>";
		exit;
	}

	$id = $_GET['id'];
	//查询所有题目
	$conf = require('database.php');
	$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

	if(!$conn){
	  exit('连接服务器失败: '.$conn->error());
	}

	$sql="select * from topic where id =".$id;

	$res=$conn->query($sql);
	$data = mysqli_fetch_assoc($res);

	$options = json_decode($data['options']);
?>
<!DOCTYPE html>
<html lang="en">

	<head>
    	<meta charset="UTF-8" />
		<title>环保挑战赛-题目详情</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">		
		<style>
			body{
				background: #ecedf0;
			}
		</style>		
	</head>

	<body>
		<div class="container">
			<div class="row" style="margin-top:100px;">
				<div class="col-2"></div>
				<div class="col-8">
					<h3 style="text-align:center;"><small><a href="index.php">首页</a></small>&nbsp;&nbsp;&nbsp;环保挑战赛&nbsp;&nbsp;&nbsp;<small><?php echo $_SESSION['admin-name'];?></small></h3>
					<ul class="list-group">
						<?php
						    echo '<li class="list-group-item" style="text-align:center;">'.$data['title'].'</li>';
						    foreach ($options as $k => $v) {
						    	if($k+1 == $data['answer']){
						    		echo '<li class="list-group-item active" style="text-align:center;">'.$v.'</li>';
						    	}else{
						    		echo '<li class="list-group-item" style="text-align:center;">'.$v.'</li>';
						    	}
						    }
						    echo '<li class="list-group-item" style="text-align:center;">答案: '.$options[$data['answer']-1].'</li>';
						?>
					</ul>
				</div>
				<div class="col-2"></div>
			</div>
		</div>
	</body>	
</html>