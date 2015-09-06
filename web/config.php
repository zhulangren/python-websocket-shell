<?php
header("Content-Type: text/html;charset=utf-8");
session_start();
$account="guest";
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
