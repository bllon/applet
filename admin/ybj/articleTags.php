<?php
//调用百度AI接口,获取文章标签
/*文章标签接口
接口描述
文本标签服务对文章的标题和内容进行深度分析，输出能够反映文章关键信息的主题、话题、实体等多维度标签以及对应的置信度，该技术在个性化推荐、文章聚合、内容检索等场景具有广泛的应用价值。
*/

$title = $_POST['title'];
$content = $_POST['content'];

$client_id = 'gmchGAe162wdcDTNp3Iw3XTB';//client_id：API Key
$client_secret = 'cAPnPevNfCwB5zDULotjDB7LcjzOsoDs';//client_secret：Secret Key

function request_post($url = '', $param = '',$header=0) {
    if (empty($url) || empty($param)) {
        return false;
    }
    
    $postUrl = $url;
    $curlPost = $param;
    $curl = curl_init();//初始化curl
    curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页    
    curl_setopt($curl, CURLOPT_HEADER, $header);//设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    // 关闭SSL验证
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($curl);//运行curl
    var_dump(curl_error($curl));
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

var_dump($access_token);

$url = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword?charset=UTF-8&access_token='.$access_token;
$post_data = [];
$post_data['title'] = $title;
$post_data['content'] = $content;

$post_data = json_encode($post_data);

$res = request_post($url, $post_data);

$res = json_decode($res,true);

var_dump($res);