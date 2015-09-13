<?php
header("Content-Type: text/html;charset=utf-8");
require_once('log4php/Logger.php');  

session_start();
$account="guest@gmail.com";
$bison_key="2da2d990f2abad8-f0f6d6e46556d7-9ad";
$filename = "./config.json";
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
		$res['data']=json_decode(file_get_contents($filename));
		$res['data']->servershell=get_server_shell($res['data']->shell);
	}
	print(json_encode($res,JSON_UNESCAPED_UNICODE));
	die(0);
}








if(isset($_SESSION['account']))
{
  $account=$_SESSION['account'];
}

$json_string = file_get_contents($filename);
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
	if($haschange==true)
	{
		Global $config_data;
		$myfile = fopen("./config.json", "w");
		$json=json_encode($config_data,JSON_UNESCAPED_UNICODE);
		fwrite($myfile, indent($json));
		fclose($myfile);
	}
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

	Global $config_data;
	$myfile = fopen("./config.json", "w");
	$json=json_encode($config_data,JSON_UNESCAPED_UNICODE);
	fwrite($myfile, indent($json));
	fclose($myfile);
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
	$fun_list->$namep=$ob;

	
	$myfile = fopen("./config.json", "w");
	$json=json_encode($config_data,JSON_UNESCAPED_UNICODE);
	fwrite($myfile, indent($json));
	fclose($myfile);
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
	Global $config_data;
	if(property_exists($accounts,$email))
	{
		echo "0";
		return 0;
	}
	Global $bison_key;
	$mypwd="${bison_key}${email}${passwd}";
	$mypwd=md5($mypwd);


	$myfile = fopen("./config.json", "w");
	$ob=new account_ob();
	$ob->passwd=$mypwd;
	$ob->power=2;
	$accounts->$email=$ob;
	$json=json_encode($config_data,JSON_UNESCAPED_UNICODE);
	fwrite($myfile, indent($json));
	fclose($myfile);
	echo "1";
	return 1;
}

function change_passwd($email,$passwdold,$passwdnew)
{
	Global $accounts;
	Global $config_data;
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
	$accounts->$email->passwd=$mypwd;

	$myfile = fopen("./config.json", "w");
	$json=json_encode($config_data,JSON_UNESCAPED_UNICODE);
	fwrite($myfile, indent($json));
	fclose($myfile);
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

?>
