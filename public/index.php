<?php 
include("../src/connect.php");
include ("../vendor/autoload.php");
include ("../src/functions.php");
include ("../src/config.php");
session_start();
$flash = new \Dtkahl\FlashMessages\FlashMessages;



if(isset($_GET['section'])) {
	$section = $_GET['section'];
} else {
	$section = "index";
}


require_once "../src/routes/".$section.".route.php";

