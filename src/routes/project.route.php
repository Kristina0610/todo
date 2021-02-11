<?php 
$errors = [];
if (isset($_GET['id'])) {
	$stmt = $pdo->prepare("SELECT * FROM td_projects WHERE id = ?");
	$stmt->execute([$_GET['id']]);
	$arr_project = $stmt->fetch();// Пришлось назвать $arr_project, так как происходил конфликт с $projects, усли называть $project
	
	if (!$arr_project) {
		$errors['project_not_found'] = "Данный проект не найден в БД";
	}
	if (!$errors) {
		$project_id = $_GET['id'];
		$stmt=$pdo->prepare("SELECT * FROM td_tasks WHERE project_id = ?");
		$stmt->execute([$project_id]);
		$tasks = $stmt->fetchAll();

		$items = [];

		foreach ($tasks as $index => $task) {
			$items[] = [
				"data-parent" => 0,
				"data-id" => $task['id'],
				"name" => $task['name'],
				"status"=>NULL
			];

			$stmt = $pdo->prepare("SELECT * FROM td_subtasks WHERE task_id = ?");
			$stmt->execute([$task['id']]);
			while ($subtask = $stmt->fetch()) {
				$items[] = [
					"data-parent" =>$task['id'],
					"data-id" => $subtask['id'],
					"name" => $subtask['name'],
					"status"=>$subtask['status_completed']
				];	
			}
		}
	}

}

$projects = getProjects();
include("../templates/project.phtml");