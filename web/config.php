<?php
header("Content-Type: text/html;charset=utf-8");
session_start();
$account="guest";
if(isset($_SESSION['account']))
{
  $account=$_SESSION['account'];
}
$accounts = [
    "zhulangren@gmail.com" => ["passwd"=>"123456","power"=>1],
    "guest" =>  ["passwd"=>"123456","power"=>1],
];

$fun_list=[
"Android"=>1,
"本地数据"=>1,
"外网数据"=>1,
"本地Server更新"=>2,
"外网Server更新"=>1,
];

$acc=$accounts[$account];
$power=$acc["power"];
function filterp($var)
{
	Global $power;
    return($var >=$power);
}

function get_my_list()
{
	Global $fun_list;
	return array_filter($fun_list,"filterp");
}
function check_login($accountp,$pwd)
{
	Global $accounts;
	if(!isset($accounts[$accountp]))
	{
		return false;
	}
	if($accounts[$accountp]['passwd'] !=$pwd)
	{
		return false;
	}
	return true;
}

?>
