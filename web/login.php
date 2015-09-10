<?php    
header("Content-Type: text/html;charset=utf-8");
require_once('log4php/Logger.php');  
require "config.php";

$logger = Logger::getRootLogger();
Logger::configure(dirname(__FILE__).'/log4php.properties');

//注册
if(isset($_POST['emailp']) && isset($_POST['passwordp']))
{
		$res= regestuser( $_POST['emailp'],$_POST['passwordp']);
		die(0);
}
//修改密码
if(isset($_POST['emailp']) && isset($_POST['passwordold']) && isset($_POST['passwordnew'] ))
{
		$res= change_passwd( $_POST['emailp'],$_POST['passwordold'],$_POST['passwordnew']);
		die(0);
}



if(isset($_POST['loginoutbtn']))
{
	$logger->debug("loginout");
	$_SESSION['islogin']=false;
	header("location: login.html");
}else
{
	$logger->debug("login");
	$email = $_POST[ 'email'];    
	$_SESSION['account']=$email;



	if(check_login($email,$_POST[ 'password']))
	{
		$bison_key="45a1df1c9e2656e4f4c742cf-4753775d";
		$_SESSION['islogin']=true;
		$_SESSION['time']=time();
		$tokenstr="${bison_key}${_SESSION['account']}${_SESSION['time']}";
		$token=md5($tokenstr);
		$logger->debug($tokenstr);
		$logger->debug($token);
		$_SESSION['token']="account|${_SESSION['account']}|${_SESSION['time']}|$token";
		header("location: index.php");
	}else
	{

		$_SESSION['islogin']=false;
		header("location: login.html");
	}
}



?>
