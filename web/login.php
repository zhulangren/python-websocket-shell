<?php    
header("Content-Type: text/html;charset=utf-8");
require "config.php";
require_once('log4php/Logger.php');  
$logger = Logger::getRootLogger();
Logger::configure(dirname(__FILE__).'/log4php.properties');
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
		$_SESSION['islogin']=true;
		header("location: index.php");
	}else
	{

		$_SESSION['islogin']=false;
		header("location: login.html");
	}
}



?>
