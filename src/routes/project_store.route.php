<?php 

$stmt = $pdo->query("SELECT * FROM td_projects");
$projects = $stmt->fetchAll();
//dump($projects);

$stmt = $pdo->query("SELECT * FROM td_tasks");
$tasks = $stmt->fetchAll();
//dump($tasks);

$stmt = $pdo->query("SELECT td_tasks.*,td_projects.name AS project_name FROM `td_tasks`,td_projects WHERE td_projects.id = td_tasks.project_id ORDER BY td_projects.id");
$tasks_and_projects = $stmt->fetchAll();
//dump($tasks_and_projects);

$projects_id = array_column($projects, 'id');
dump($projects_id);
$arr = array_combine($projects_id, $tasks_and_projects);
dump($arr);
include ("../templates/project_store.phtml");