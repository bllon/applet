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
		<title>环保挑战赛-添加新题</title>
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
					<h3 style="text-align:center;"><small><a href="index.php">首页</a></small>&nbsp;&nbsp;&nbsp;环保挑战赛-添加新题&nbsp;&nbsp;&nbsp;<small><?php echo $_SESSION['admin-name'];?></small></h3>
					<form action="doAddTopic.php" method="post">
					  <div class="form-group">
					    <label>题目</label>
					    <input type="text" class="form-control" name="title">
					  </div>
					  <div class="form-group">
					    <label>选项1</label>
					    <input type="text" class="form-control" name="option1">
					  </div>
					  <div class="form-group">
					    <label>选项2</label>
					    <input type="text" class="form-control" name="option2">
					  </div>
					  <div class="form-group">
					    <label>选项3</label>
					    <input type="text" class="form-control" name="option3">
					  </div>
					  <div class="form-group">
					    <label>选项4</label>
					    <input type="text" class="form-control" name="option4">
					  </div>
					  <div class="form-group">
					    <label>答案</label>
					    <input type="text" class="form-control" name="answer">
					  </div>
					  <button type="submit" class="btn btn-default">提交</button>
					</form>
				</div>
				<div class="col-2"></div>
			</div>
		</div>
	</body>	
</html>