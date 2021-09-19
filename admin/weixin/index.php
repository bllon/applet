<?php
	session_start();
	if(!isset($_SESSION['admin-name'])){
		echo "<script>alert('请登录用户!');location.href='html/login.html';</script>";
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">

	<head>
    	<meta charset="UTF-8" />
		<title>环保挑战赛-首页</title>
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
					<h3 style="text-align:center;">环保挑战赛&nbsp;&nbsp;&nbsp;<small><?php echo $_SESSION['admin-name'];?></small></h3>
					<ul class="list-group">
					  <li class="list-group-item"><a href="topic.php">查看题库</a></li>
					  <li class="list-group-item"><a href="addTopic.php">添加新题</a></li>
					  <li class="list-group-item"><a href="gameRanking.php">游戏记录</a></li>
					</ul>
				</div>
				<div class="col-2"></div>
			</div>
		</div>	
	</body>	
</html>