<?php  

function getProjects()
{
	global $pdo;
	$stmt = $pdo->query("SELECT * FROM td_projects");
	$projects = $stmt->fetchAll();
	return $projects;
}