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

function regestuser($email,$passwd)
{
	Global $accounts;
	Global $config_data;
	if(property_exists($accounts,$email))
	{
		echo "0";
		return 0;
	}

	$myfile = fopen("./config.json", "w");
	$ob=new account_ob();
	$ob->passwd=$passwd;
	$ob->power=2;
	$accounts->$email=$ob;
	$json=json_encode($config_data,JSON_UNESCAPED_UNICODE);
	fwrite($myfile, indent($json));
	fclose($myfile);
	echo "1";
	return 1;
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
	if($accounts->$accountp->passwd !=$pwd)
	{
		return false;
	}
	

	return true;
}

?>
