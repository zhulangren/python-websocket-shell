<?php
header("Content-Type: text/html;charset=utf-8");
session_start();
$account="guest@gmail.com";
if(isset($_SESSION['account']))
{
  $account=$_SESSION['account'];
}

$filename = "./config.json";
$json_string = file_get_contents($filename);
$config_data=json_decode($json_string);

$fun_list=$config_data->shell;
$accounts=$config_data->account;
$address=$config_data->address;


class account_ob 
{
	var $power;
	var $passwd;
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
$bison_key="2da2d990f2abad8-f0f6d6e46556d7-9ad";
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
