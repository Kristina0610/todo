<?php  

function getProjects()
{
	global $pdo;
	$stmt = $pdo->query("SELECT * FROM td_projects");
	$projects = $stmt->fetchAll();
	return $projects;
}

function getProject($project_id)
{
	global $pdo;
	$stmt = $pdo->prepare("SELECT * FROM td_projects WHERE id = ?");
	$stmt->execute([$project_id]);
	$project = $stmt->fetch();
	return $project;
}