<?php 
$errors = [];

if (isset($_GET['task_id'])) {
	$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ?");
	$stmt->execute([$_GET['task_id']]);
	$task = $stmt->fetch();

	if (!$task) {
		$errors['task_not_found'] = "Данная задача не найдена в БД";
	} else {
		$stmt = $pdo->prepare("SELECT * FROM td_subtasks WHERE task_id = ?");
		$stmt->execute([$_GET['task_id']]);
		$subtasks = $stmt->fetchAll();
	}
}
$projects = getProjects();

include ("../templates/task.phtml");