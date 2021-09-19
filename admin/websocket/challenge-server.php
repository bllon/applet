<?php 

class WebSocketServer
{
	private $sockets;//所有socket连接池包括服务端socket
	private $users;//所有连接用户
	private $server;//服务端socket
	private $groups;//所有分组
	private $wait;//等待队列
	private $redis;


	public function __construct($ip,$port){
		$this->server = socket_create(AF_INET,SOCK_STREAM,0);
		$this->sockets = array($this->server);
		$this->users = array();
		$this->groups = array();
		$this->wait = array();

		//1.实例化对象
		$this->redis = new Redis();

		//2.定义主机和端口
		$Redisport = 6379;

		//3.连接redis服务器
		$this->redis->connect('127.0.0.1' , $Redisport);
		// $this->redis->auth('');

		socket_bind($this->server,$ip,$port);
		socket_listen($this->server,3);		//并发连接3个
		echo "[*]Listening:".$ip.":".$port."\n";
	}

	//启动服务端
	public function run(){
		$write = null;
		$except = null;
		while(true){
			$active_sockets = $this->sockets;	//当前所有socket端
			socket_select($active_sockets,$write,$except,null);
			//前三个参数传入的是数组的引用，会依次从传入的数组中选择出可读的，可写的，异常的socket，我们只需要选择出可读的socket
			//最后一个参数tv_sec很重要
			//第一，若将null以形参传入，即不传入时间结构，就是将select置于阻塞状态，一定等到监视文件描述符集合（socket数组）中某个文件描述符
			//第二，若将时间值设置为0秒0毫秒，就变成一个纯粹的非阻塞函数，不管文件描述符是否有变化，都立刻返回继续执行，文件无变化返回0，有变化返回一个正值
			//第三，timeout的值大于0，这就是等待的超时时间，即select在timeout时间内阻塞，超时时间之内有事件到来就返回，否则在超时后不管怎样一定返回，返回值同上述

			//循环可读的socket
			foreach($active_sockets as $socket){

				if($socket == $this->server){
					//服务端socket可读说明有新用户连接
					$user = socket_accept($this->server);	//接收到的客户端socket
					$key = uniqid();	//分配一个键给客户端socket
					$this->sockets[] = $user;
					$this->users[$key] = array(
						'socket'=>$user,
						'handshake'=>false //是否完成websocket握手
					);
				}else{
					//用户socket可读
					$buffer = '';
					$bytes = socket_recv($socket, $buffer, 1024, 0);//读取数据	用户发送的数据用$buffer保存
					$key = $this->find_user_by_socket($socket);	//通过socket在users数组中找出user
					if ($bytes == 0){
						//没有数据 关闭连接
						$this->disconnect($socket);
					}else{
						//没有握手就先握手
						if(!$this->users[$key]['handshake']){
							$this->handshake($key,$buffer);
						}else{
							//握手后
							//解析消息websocket协议有自己的消息格式
							//解码 编码过程固定的
							$msg = $this->msg_decode($buffer);
							$data = json_decode($msg,true);

							if(is_null($data)){
								//有人离开
								//1.判断是否在wait队列中
								//2.判断是否在分组中
								//3.清除socket套接字
								$fla = true;//默认在分组中
								foreach($this->wait as $k=>$v){
									if($v['socket'] == $socket){
										//在wait队列中，直接清除
										unset($this->wait[$k]);
										$fla = false;
										break;
									}
								}

								if($fla){
									//在分组中
									// 找到分组
									foreach ($this->groups as $k=>$v) {
										if(isset($v['one']) && $v['one']['socket'] == $socket){

											if(isset($v['two'])){
												//给two发送信息
												$msg = array(
													'type'=>'f',
													'msg'=>'对手已离开，重新匹配',
												);
												$this->push_msg_for_user($v['two']['socket'],$msg);

											}
											//删除分组,重新匹配
											unset($this->groups[$k]);

											// array_push($this->wait,$v['two']);
										}

										if(isset($v['two']) && $v['two']['socket'] == $socket){

											if(isset($v['one'])){
												//给one发送信息
												$msg = array(
													'type'=>'f',
													'msg'=>'对手已离开，重新匹配',
												);
												$this->push_msg_for_user($v['one']['socket'],$msg);
											}
											//删除分组,重新匹配
											unset($this->groups[$k]);

											// array_push($this->wait,$v['one']);
										}
									}

								}
								$this->disconnect($socket);//解除连接
							}else{

								//消息类型i,a,g,r,f


								//首次连接
								if($data['type'] == 'i'){

									//用户是否存在sessionId
									if(!isset($data['myId']) || $data['myId'] == ''){
										$myId = uniqid();
									}else{
										$myId = $data['myId'];
									}

									if(empty($this->wait)){
										//进入等待队列
										
										$data['userInfo']['id'] = $myId;
										$one = array(
											'userInfo'=>$data['userInfo'],
											'socket'=>$socket
										);
										array_push($this->wait,$one);

										$msg = array(
											'type'=>'i',
											'msg'=>'ok',
											'myId'=>$myId
										);
										$this->push_msg_for_user($socket,$msg);
									}else{
										//匹配成功
										$data['userInfo']['id'] = $myId;
										$other = array_shift($this->wait);

										//分配组
										$groupId = uniqid();//组id
										$this->groups[$groupId] = array(
											'one'=>array(
												'userInfo'=>$data['userInfo'],
												'socket'=>$socket,
											),
											'two'=>$other
										);

										$msg = array(
											'type'=>'i',
											'msg'=>'ok',
											'myId'=>$myId
										);
										$this->push_msg_for_user($socket,$msg);

										$num = $this->redis->get('timuNum');

										if($num == false){
											//没有题库
											$timu = array();
										}else{
											//从1-num随机取5个
											$numbers = range (1,$num);
											shuffle($numbers);
											array_slice($numbers,0,5);

											$timu = [];
											for($i=0;$i<5;$i++){
												$row = $this->redis->get('timu'.$numbers[$i]);

												$timu[] = json_decode($row);
											} 
										}

										$data = array(
											'type'=>'a',
											'message'=>'匹配成功',
											'groupId'=>$groupId,
											'users'=>array(
												'one'=>$data['userInfo'],
												'two'=>$other['userInfo']
											),//一次性推送5道题
											'timu'=>$timu
											
										);
										$this->push_msg_for_group($groupId,$data);
									}
								}

								//更新分数
								if($data['type'] == 'g'){
									$groupId = $data['groupId'];

									//向他人发送消息
									$this->push_msg_for_groupUser($groupId,$socket,$data);
								}

								//保存游戏记录
								if($data['type'] == 'r'){
									$res = $this->redis->get('gameData');
									if($res == false){
										$gameData = [];
										$gameData[$data['myId']] = [];
										$gameData[$data['myId']][] = $data;

										$this->redis->set('gameData',json_encode($gameData));
									}else{
										$gameData = json_decode($res,true);
										if(isset($gameData[$data['myId']])){
											$gameData[$data['myId']][] = $data;
											$this->redis->set('gameData',json_encode($gameData));
										}else{
											$gameData[$data['myId']] = [];
											$gameData[$data['myId']][] = $data;

											$this->redis->set('gameData',json_encode($gameData));
										}

									}

								}

								//单人学习浏览
								if($data['type'] == 'x'){
									$num = $this->redis->get('timuNum');

									if($num == false){
										//没有题库
										$timu = array();
									}else{
										//从1-num随机取5个
										$numbers = range (1,$num);
										shuffle($numbers);
										array_slice($numbers,0,10);

										$timu = [];
										for($i=0;$i<10;$i++){
											$row = $this->redis->get('timu'.$numbers[$i]);

											$timu[] = json_decode($row);
										} 
									}

									$data = array(
										'type'=>'x',
										'timu'=>$timu
										
									);
									$this->push_msg_for_user($socket,$data);


								}

								//好友对战
								if($data['type'] == 'y'){

									//用户是否存在sessionId
									if(!isset($data['myId']) || $data['myId'] == ''){
										$myId = uniqid();
									}else{
										$myId = $data['myId'];
									}

									//匹配成功
									if(isset($data['userInfo'])){
										$data['userInfo']['id'] = $myId;
									}

									if(!isset($data['groupId']) || $data['groupId'] == ''){
										//创建分组，邀请者
										//分配组
										$groupId = uniqid();//组id
										$this->groups[$groupId] = array(
											'one'=>array(
												'userInfo'=>$data['userInfo'],
												'socket'=>$socket,
											)
										);

										$msg = array(
											'type'=>'i',
											'msg'=>'ok',
											'myId'=>$myId,
											'groupId'=>$groupId
										);
										$this->push_msg_for_user($socket,$msg);

									}else{
										//进入分组，被邀请进来
										$this->groups[$data['groupId']]['two'] = array(
											'userInfo'=>$data['userInfo'],
											'socket'=>$socket,
										);

										$msg = array(
											'type'=>'i',
											'msg'=>'ok',
											'myId'=>$myId,
											'groupId'=>$data['groupId']
										);
										$this->push_msg_for_user($socket,$msg);

										$num = $this->redis->get('timuNum');

										if($num == false){
											//没有题库
											$timu = array();
										}else{
											//从1-num随机取5个
											$numbers = range (1,$num);
											shuffle($numbers);
											array_slice($numbers,0,5);

											$timu = [];
											for($i=0;$i<5;$i++){
												$row = $this->redis->get('timu'.$numbers[$i]);

												$timu[] = json_decode($row);
											} 
										}

										$data = array(
											'type'=>'a',
											'message'=>'匹配成功',
											'groupId'=>$data['groupId'],
											'users'=>array(
												'one'=>$this->groups[$data['groupId']]['one']['userInfo'],
												'two'=>$data['userInfo']
											),//一次性推送5道题
											'timu'=>$timu
											
										);
										$this->push_msg_for_group($data['groupId'],$data);
									}
								}

							}

							

							//编码后发送回去
							// $res_msg = $this->msg_encode($msg);
							// socket_write($socket,$res_msg,strlen($res_msg));
						}
					}
				}
			}
		}
	}


	//解除连接
	private function disconnect($socket){
		$key = $this->find_user_by_socket($socket);	//用户的键值
		unset($this->users[$key]);
		foreach($this->sockets as $k=>$v){
			if($v == $socket){
				unset($this->sockets[$k]);	//销毁用户socket资源
			}
		}	
		socket_shutdown($socket);	//关闭socket资源
		socket_close($socket);
	}

	//通过socket在users数组中找出user
	private function find_user_by_socket($socket){
		foreach($this->users as $key=>$user){
			if($user['socket'] == $socket){
				return $key;
			}
		}
		return -1;
	}

	//握手
	private function handshake($k,$buffer){
		//用户的键值		用户发送过来的数据
		//截取Sec-WebSocket-Key的值并加密
		$buf = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:')+18);	//找到Sec-WebSocket-Key:后面的字符串
		$key = trim(substr($buf, 0,strpos($buf, "\r\n")));	//除去换行后的字符，只剩key的值
		$new_key = base64_encode(sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11",true));	//拼接得到new-key

		//按照协议组合信息进行返回
		$message = "HTTP/1.1 101 Switching Protocols\r\n";
		$message .= "Upgrade: websocket\r\n";
		$message .= "Sec-WebSocket-Version: 13\r\n";
        $message .= "Connection: Upgrade\r\n";
        $message .= "Sec-WebSocket-Accept: " . $new_key . "\r\n\r\n";
        socket_write($this->users[$k]['socket'], $message,strlen($message));	//服务端回应消息，进行握手

        //对已经握手的client做标志
        $this->users[$k]['handshake'] = true;
        return true;
	}

	//编码 把消息打包成websocket协议支持的格式
	private function msg_encode($buffer){
		$len = strlen($buffer);
		if($len <= 125){
			return "\x81".chr($len).$buffer;
		}else if($len <= 65535){
			return "\x81".chr(126).pack("n",$len).$buffer;
		}else{
			return "\x81".chr(127).pack("xxxxN",$len).$buffer;
		}
	}

	//解码 解析websocket数据帧
	private function msg_decode($buffer){
		$len = null;
		$masks = null;
		$data = null;
		$decoded = null;
		$len = ord($buffer[1]) & 127;
		if($len === 126){
			$masks = substr($buffer, 4,4);
			$data = substr($buffer, 8);
		}else if($len === 127){
			$masks = substr($buffer, 10,4);
			$data = substr($buffer, 14);
		}else{
			$masks = substr($buffer, 2,4);
			$data = substr($buffer, 6);
		}

		for($index=0;$index<strlen($data);$index++){
			$decoded .= $data[$index] ^ $masks[$index%4];
		}
		return $decoded;
	}

	//向socket用户发消息
	private function push_msg_for_user($socket,$msg){
		$msg = $this->msg_encode(json_encode($msg));
		socket_write($socket, $msg, strlen($msg));
	}

	//向组内的特定用户发送消息
	private function push_msg_for_groupUser($groupId,$socket,$msg){

		$msg = $this->msg_encode(json_encode($msg));
		foreach($this->groups[$groupId] as $user){
			if($socket != $user['socket']){
				socket_write($user['socket'], $msg, strlen($msg));
			}
		}	
	}

	//同一个组里的用户广播消息		向groupId的组发送消息
	private function push_msg_for_group($groupId, $msg){
		//发送消息给组内所有人
		$msg = $this->msg_encode(json_encode($msg));
		foreach($this->groups[$groupId] as $user){
			socket_write($user['socket'], $msg, strlen($msg));
		}	
	}

	//全局广播消息 消息为数组格式		向所有客户端发送消息
	private function push_msg_for_all($msg, $c_socket=null){
		$msg = $this->msg_encode(json_encode($msg));
		//除了服务器和自己都发送
		foreach($this->sockets as $socket){
			if($socket != $this->server){
				socket_write($socket, $msg, strlen($msg));
			}
		}
	}

}


$ws = new WebSocketServer('0.0.0.0',9999);
$ws->run();
?>

