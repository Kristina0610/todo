<?php 

$stmt = $pdo->query("SELECT * FROM td_projects");
$projects = $stmt->fetchAll();



include ("../templates/index.phtml");