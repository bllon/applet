<?php
	session_start();
	if(!isset($_SESSION['admin-name'])){
		echo "<script>alert('请登录用户!');location.href='html/login.html';</script>";
		exit;
	}

	$id = $_GET['id'];

	$conf = require('database.php');
	$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

	if(!$conn){
	  exit('连接服务器失败: '.$conn->error());
	}

	$sql="select * from topic where id=".$id;

	$res=$conn->query($sql);
	$data = mysqli_fetch_assoc($res);

	$options = json_decode($data['options']);
?>
<!DOCTYPE html>
<html lang="en">

	<head>
    	<meta charset="UTF-8" />
		<title>环保挑战赛-修改题目</title>
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
					<h3 style="text-align:center;"><small><a href="index.php">首页</a></small>&nbsp;&nbsp;&nbsp;环保挑战赛（修改题目）&nbsp;&nbsp;&nbsp;<small><?php echo $_SESSION['admin-name'];?></small></h3>
					<form action="doUpdateTopic.php" method="post">
					  <div class="form-group">
					    <label>题目</label>
					    <input type="text" class="form-control" name="title" <?php echo "value='".$data['title']."'";?>>
					  </div>
					  <?php
					  	foreach ($options as $k => $v) {
					  		echo '<div class="form-group"><label>选项'.($k+1).'</label><input type="text" class="form-control" name="option'.($k+1).'" value="'.$v.'"></div>';
					  	}
					  	if($k == 2){
					  		$k++;
					  		echo '<div class="form-group"><label>选项'.($k+1).'</label><input type="text" class="form-control" name="option'.($k+1).'"></div>';
					  	}
					  ?>
					  <div class="form-group">
					    <label>答案</label>
					    <input type="text" class="form-control" name="answer" <?php echo "value='".$data['answer']."'";?>>
					  </div>
					  <input type="hidden" name="id" <?php echo "value='".$data['id']."'";?>>
					  <button type="submit" class="btn btn-default">提交</button>
					</form>
				</div>
				<div class="col-2"></div>
			</div>
		</div>
	</body>	
</html>