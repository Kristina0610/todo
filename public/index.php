<?php 
include("../src/connect.php");

if(isset($_GET['section'])) {
	$section = $_GET['section'];
} else {
	$section = "index";
}


require_once "../src/routes/".$section.".route.php";

