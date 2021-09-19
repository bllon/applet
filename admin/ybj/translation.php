<?php
//调用百度AI接口,音频文件转文字
$content = $_POST['content'];//接受前台传递音频

$client_id = 'GiOljanxOv8X4KcZLafbXvas';//client_id：API Key
$client_secret = '19fnDjoSfBvzL3Y8wL2D2hHuYhn79Xon';//client_secret：Secret Key

function request_post($url = '', $param = '') {
    if (empty($url) || empty($param)) {
        return false;
    }
    
    $postUrl = $url;
    $curlPost = $param;
    $curl = curl_init();//初始化curl
    curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    // 关闭SSL验证
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($curl);//运行curl
    curl_close($curl);

    return $data;
}

$url = 'https://aip.baidubce.com/oauth/2.0/token';
$post_data['grant_type'] = 'client_credentials';
$post_data['client_id'] = $client_id;
$post_data['client_secret'] = $client_secret;
$o = "";
foreach ( $post_data as $k => $v ) 
{
	$o.= "$k=" . urlencode( $v ). "&" ;
}
$post_data = substr($o,0,-1);

$res = request_post($url, $post_data);

$res = json_decode($res,true);

$access_token = $res['access_token'];


$url = 'https://aip.baidubce.com/rpc/2.0/aasr/v1/create?access_token='.$access_token;
$post_data = [];
$post_data['speech_url'] = $content;
$post_data['format'] = 'mp3';
$post_data['pid'] = 80001;
$post_data['rate'] = 16000;

$post_data = json_encode($post_data);
$res = request_post($url, $post_data);

$res = json_decode($res,true);

// var_dump($res);

$task_id = $res['task_id'];


$url = 'https://aip.baidubce.com/rpc/2.0/aasr/v1/query?access_token='.$access_token;
$post_data = [];
$post_data['task_ids'][] = $task_id;

$post_data = json_encode($post_data);
$res = request_post($url, $post_data);

$res = json_decode($res,true);

// var_dump($res);

while(isset($res['tasks_info'][0]['task_status']) && $res['tasks_info'][0]['task_status'] == 'Running'){
    $url = 'https://aip.baidubce.com/rpc/2.0/aasr/v1/query?access_token='.$access_token;
    $post_data = [];
    $post_data['task_ids'][] = $task_id;

    $post_data = json_encode($post_data);
    $res = request_post($url, $post_data);

    $res = json_decode($res,true);

    // var_dump($res);

    sleep(2);
}

echo $res['tasks_info'][0]['task_result']['result'][0];