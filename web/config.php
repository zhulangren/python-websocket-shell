<?php
header("Content-Type: text/html;charset=utf-8");
require_once('log4php/Logger.php');  

session_start();
$account="guest@gmail.com";
$bison_key="2da2d990f2abad8-f0f6d6e46556d7-9ad";
$filename = "./config.json";
$write_config_fd=0;
$logger = Logger::getRootLogger();
Logger::configure(dirname(__FILE__).'/log4php.properties');
if(isset($_POST['token']) && isset($_POST['time']) && isset($_POST['zhulangren'] ))
{
	$mytime=$_POST['time'];
	$res= [];
	if(abs($mytime-time()) >=300)
	{
		$res["flag"]=-1;
	}
	$mytoken="${bison_key}".$mytime. $_POST['zhulangren'] ;
	$mytoken=md5($mytoken);
	if($mytoken !=$_POST['token'])
	{
		$res["flag"]=-2;
	}else
	{
		$res['flag']=0;
		$res['data']=json_decode(read_config_json());
		$res['data']->servershell=get_server_shell($res['data']->shell);
	}
	print(json_encode($res,JSON_UNESCAPED_UNICODE));
	die(0);
}








if(isset($_SESSION['account']))
{
  $account=$_SESSION['account'];
}

$json_string = read_config_json();
$config_data=json_decode($json_string);

$fun_list=$config_data->shell;
$accounts=$config_data->account;
$address=$config_data->address;



//修改账号权限或者删除账号
if( isset($_POST['editaccount']) && isset($_POST['accountp']) && isset($_POST['powerp']) && isset($_POST['btnp']))
{
	$accountp=$_POST['accountp'];
	$powerp=$_POST['powerp'];
	$haschange=false;
	begin_write_config();

	if($_POST['btnp']=='ae')
	{//修改权限
		if(property_exists($accounts,$accountp) && $accounts->$accountp->power !=$powerp && (int)$powerp >=0)
		{
			$accounts->$accountp->power=$powerp;
			$haschange=true;
		}
	}else
	{
		if(property_exists($accounts,$accountp))
		{
			unset( $accounts->$accountp);
			$haschange=true;
		}
	}
	end_write_config();
}


//修改或删除脚本路径
//addshell:"addshell",namep:bname,powerp:bpower,indexp:bindex,shellp:bshell,btnp:btn
if( isset($_POST['editshell']) && isset($_POST['namep'])  &&
 isset($_POST['powerp']) && isset($_POST['indexp'])&& isset($_POST['shellp']) && isset($_POST['btnp']))
{
	Global $fun_list;
	$namep=urldecode($_POST['namep']);
	$powerp=$_POST['powerp'];
	$pindex=$_POST['indexp'];
	$pshell=$_POST['shellp'];
	$btnp=$_POST['btnp'];
	begin_write_config();
	if($btnp=='be')
	{
		$ob=new shell_ob();
		$ob->power=$powerp;
		$ob->index=$pindex;
		$ob->shell=$pshell;
		$fun_list->$namep=$ob;
	}else
	{
		unset($fun_list->$namep);
	}
	end_write_config();
	
}

//添加shell addshell:"addshell" namep:bname,powerp:bpower,pindex:bindex,pshell:bshell
if( isset($_POST['addshell']) && isset($_POST['namep'])  &&
 isset($_POST['powerp']) && isset($_POST['pindex'])&& isset($_POST['pshell']))
{

	$namep=urldecode($_POST['namep']);
	$powerp=$_POST['powerp'];
	$pindex=$_POST['pindex'];
	$pshell=$_POST['pshell'];
	$ob=new shell_ob();
	$ob->power=$powerp;
	$ob->index=$pindex;
	$ob->shell=$pshell;
	begin_write_config();
	$fun_list->$namep=$ob;
	end_write_config();


}

//检测index是否唯一

//文件枷锁访问


class account_ob 
{
	var $power;
	var $passwd;
}
class shell_ob
{
	var $power;
	var $index;
	var $shell;
}

function indent ($json) { 
	$result = ''; 
	$pos = 0; 
	$strLen = strlen($json); 
	$indentStr = ' '; 
	$newLine = "\n"; 
	$prevChar = ''; 
	$outOfQuotes = true; 

	for ($i=0; $i<=$strLen; $i++) { 

	// Grab the next character in the string. 
	$char = substr($json, $i, 1); 
	// Are we inside a quoted string? 
	if ($char == '"' && $prevChar != '\\') { 
	$outOfQuotes = !$outOfQuotes; 
	// If this character is the end of an element, 
	// output a new line and indent the next line. 
	} else if(($char == '}' || $char == ']') && $outOfQuotes) { 
	$result .= $newLine; 
	$pos --; 
	for ($j=0; $j<$pos; $j++) { 
	$result .= $indentStr; 
	} 
	} 
	// Add the character to the result string. 
	$result .= $char; 
	// If the last character was the beginning of an element, 
	// output a new line and indent the next line. 
	if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) { 
	$result .= $newLine; 
	if ($char == '{' || $char == '[') { 
	$pos ++; 
	} 
	for ($j = 0; $j < $pos; $j++) { 
	$result .= $indentStr; 
	} 
	} 
	$prevChar = $char; 
	} 
	return $result; 

} 
function regestuser($email,$passwd)
{
	Global $accounts;
	if(property_exists($accounts,$email))
	{
		echo "0";
		return 0;
	}
	Global $bison_key;
	$mypwd="${bison_key}${email}${passwd}";
	$mypwd=md5($mypwd);


	$ob=new account_ob();
	$ob->passwd=$mypwd;
	$ob->power=2;
	begin_write_config();
	$accounts->$email=$ob;
	end_write_config();
	echo "1";
	return 1;
}

function change_passwd($email,$passwdold,$passwdnew)
{
	Global $accounts;
	if(property_exists($accounts,$email)==false)
	{//账号不存在
		echo "0";
		return 0;
	}
	Global $bison_key;
	$mypwd="${bison_key}${email}${passwdold}";
	$mypwd=md5($mypwd);
	if($accounts->$email->passwd !=$mypwd)
	{//旧密码不正确
		echo "1";
		return 0;
	}
	//修改密码
	$mypwd="${bison_key}${email}${passwdnew}";
	$mypwd=md5($mypwd);
	begin_write_config();
	$accounts->$email->passwd=$mypwd;
	end_write_config();

	echo "2";
	return 0;

}

function filter_by_value ($arrayp){ 
	Global $power;
	$newarray=array( );
	foreach ($arrayp as $key => $val) 
	{
		if($val->power >=$power)
		{
			$newarray[$key]=$val->index;
		}
	}
	return $newarray;
    
} 


function get_server_shell($arrayp)
{
	Global $logger;
	$newarray=array( );
	foreach ($arrayp as $key => $val) 
	{
		$newarray[$val->index]=$val->shell;
	}
	return $newarray;
}


$acc=$accounts->$account;
$power=$acc->power;

function get_my_list()
{
	Global $fun_list;
	return filter_by_value($fun_list);
}
function check_login($accountp,$pwd)
{
	Global $accounts;
	if(!isset($accounts->$accountp))
	{
		return false;
	}
	Global $bison_key;
	$mypwd="${bison_key}${accountp}${pwd}";
	$mypwd=md5($mypwd);
	if($accounts->$accountp->passwd !=$mypwd)
	{
		return false;
	}
	

	return true;
}


//配置文件读写

function read_config_json()
{
	Global $filename;
	$fd = fopen($filename, "r"); 
	$retries = 1;
	while(!flock($fd, LOCK_SH) && $retries < 100) {    //对文件加锁,尝试100次
	    $retries += 1;
	}
	if($retries >= 100) {
		$logger->debug("管理员正在修改配置文件，请稍后登陆");
		$_SESSION['islogin']=false;
		header("location: login.html");
	    return "";
	}

	$json_string= fread($fd, filesize($filename)); 
	flock($fd, LOCK_UN); 
	fclose($fd); 
	return $json_string;
}

//开始修改之前先刷新内存数据，对文件进行加锁,两个函数一定要配对出现
function begin_write_config()
{
	Global $filename,$write_config_fd;
	Global $config_data,$fun_list,$accounts,$address;
	Global $logger;

	$json_string= read_config_json();
	$config_data=json_decode($json_string);
	$fun_list=$config_data->shell;
	$accounts=$config_data->account;
	$address=$config_data->address;
	$write_config_fd = fopen($filename, "w+"); 
	$retries = 1;
	while(!flock($write_config_fd, LOCK_NB |LOCK_EX) && $retries < 100) {    //对文件加锁,尝试100次
	    $retries += 1;
	}
	if($retries >= 100) {
		$logger->debug("其他人正在修改配置文件，请稍后登陆");
		$_SESSION['islogin']=false;
		header("location: login.html");
	    return false;
	}

	
	return true;
}
function end_write_config()
{
	Global $config_data,$write_config_fd;
	$json=json_encode($config_data,JSON_UNESCAPED_UNICODE);
	fwrite($write_config_fd, indent($json));
	flock($write_config_fd, LOCK_UN); 
	fclose($write_config_fd); 
}

?>
