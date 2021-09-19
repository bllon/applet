<?php
	session_start();
	if(!isset($_SESSION['admin-name'])){
		echo "<script>alert('请登录用户!');location.href='html/login.html';</script>";
		exit;
	}
	
	$conf = require('database.php');
	$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

	if(!$conn){
	  exit('连接服务器失败: '.$conn->error());
	}

	$sql="select * from user order by score desc";

	$res=$conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

	<head>
    	<meta charset="UTF-8" />
		<title>环保挑战赛-排行榜</title>
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
					<h3 style="text-align:center;"><small><a href="index.php">首页</a></small>&nbsp;&nbsp;&nbsp;环保挑战赛（排行榜）&nbsp;&nbsp;&nbsp;<small><?php echo $_SESSION['admin-name'];?></small></h3>
					<ul class="list-group">
						<table class="table table-striped">
							<tr>
								<td>微信名</td>
								<td>头像</td>
								<td>性别</td>
								<td>城市</td>
								<td>分数</td>
							</tr>
						<?php
						while($row = mysqli_fetch_assoc($res))
						{
							$row['gender'] = $row['gender'] == 1 ? '男':'女';
							$row['city'] = $row['city'] == 'null' ? '未知':$row['city'];
						    echo '<tr><td>'.$row['nickName'].'</td><td><img style="width:50px;height:50px;" src="'.$row['avatarUrl'].'" /></td><td>'.$row['gender'].'</td><td>'.$row['city'].'</td><td>'.$row['score'].'</td></tr>';
						}
						?>
						</table>
					</ul>
				</div>
				<div class="col-2"></div>
			</div>
		</div>
	</body>	
</html>