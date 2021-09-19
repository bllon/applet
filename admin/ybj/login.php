<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type:text/html;charset=utf-8");
$conf = require('database.php');
$conn = new mysqli($conf['host'],$conf['username'],$conf['password'],$conf['db_name']);

mysqli_set_charset($conn,'utf8');

if(!$conn){
  exit('连接服务器失败: '.$conn->error());
}

// 获取openid
function getOpenid($code){ // $code为小程序提供
    $appid = 'wx5d54d0acc564a6ee'; // 小程序APPID
    $secret = 'c130b60dc74a60ed73ee8d5b5a48882b'; // 小程序secret
    $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';    
        
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。    
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    
    return json_decode($res, true); // 这里是获取到的信息
}    


$res = getOpenid($_POST['code']);

if(isset($res['openid']) && $res['openid'] !=''){
  $session_id = $res['openid'];

  if($_POST['session_id'] == ''){
    //新用户

    //判断之前是否授权过
    $sql = 'select * from jyybj_user where session_id="'.$session_id.'"';
    $res = $conn->query($sql);
    $row = mysqli_fetch_assoc($res);
    if(!empty($row)){
      $code = 0;
      $msg = '授权成功';
      $data = $session_id;
    }else{
      //给新用户添加默认目录
      $sql = 'insert into jyybj_class (session_id, class_name) values("'.$session_id.'","默认")';
      $res = $conn->query($sql);

      //给用户添加默认笔记
      $create_time = time();
      $sql = 'insert into jyybj_note(session_id, title, content, type, class_name, sort, label, create_time, update_time) VALUES ("'.$session_id.'", "简易云笔记介绍", "&lt;p class=\'ql-indent-4\'&gt;&lt;strong&gt;建议云笔记介绍&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;1.方便使用&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;简易云笔记适用于学生，工作人士等，支持多种存&lt;/p&gt;&lt;p&gt;储格式及样式&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;2.简洁清晰&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;建议云笔记界面清晰，无广告，操作人性化&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;3.分类及标签&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;简易云笔记支持创建分类和标签，可以帮助用户准&lt;/p&gt;&lt;p&gt;确定位笔记位置，方便笔记搜索&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;hr&gt;&lt;p&gt;&lt;strong&gt;4.支持图片上传&lt;/strong&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;简易云笔记支持富文本编辑，包括图片上传，例如&lt;/p&gt;&lt;p&gt;&lt;img src=\'http://'.$conf['hostName'].'/ybj/img/moren.jpg\' data-custom=\'id=abcd&amp;amp;role=god\'&gt;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;p class=\'ql-indent-1\'&gt;&lt;br&gt;&lt;/p&gt;", 0, "默认", 1, "", '.$create_time.', '.$create_time.')';
      $res = $conn->query($sql);
      
      $sql = 'insert into jyybj_user (session_id,nickName,gender,city,country,language,province,avatarUrl) values("'.$session_id.'","'.$_POST['nickName'].'",'.$_POST['gender'].',"'.$_POST['city'].'","'.$_POST['country'].'","'.$_POST['language'].'","'.$_POST['province'].'","'.$_POST['avatarUrl'].'")';

      $res = $conn->query($sql);

      if($res){
        $code = 0;
        $data = $session_id;
        $msg = '授权成功';
      }else{
        $code = 1;
        $msg = '授权失败';
        $data = '';
      }
    }

  }else{
    $sql = 'select * from jyybj_user where session_id="'.$session_id.'"';
    $res = $conn->query($sql);
    $row = mysqli_fetch_assoc($res);
    if(!empty($row)){
      $code = 0;
      $msg = '授权成功';
      $data = $session_id;
    }else{
      $code = 1;
      $msg = '授权失败';
      $data = '';
    }
  }
}else{
  $code = 1;
  $msg = '授权失败';
  $data = '';
}




//把上面的数组以 json 格式返回
$json = json_encode(array(
  "code"=>$code,
  "message"=>$msg,
  "data"=>$data
),JSON_UNESCAPED_UNICODE);  
echo $json;
 
?>