<?php 
$errors = [];
if (isset($_GET['id'])) {
	$stmt = $pdo->prepare("SELECT * FROM td_projects WHERE id = ?");
	$stmt->execute([$_GET['id']]);
	$project = $stmt->fetch();
	
	if (!$project) {
		$errors['project_not_found'] = "Данный проект не найден в БД";
	}
	if (!$errors) {
		$project_id = $_GET['id'];
		$stmt=$pdo->prepare("SELECT * FROM td_tasks WHERE project_id = ? AND deleted_at IS NULL");
		$stmt->execute([$project_id]);
		
		$items = [];

		while($task = $stmt->fetch()) {
			$items[] = [
				"data-parent" => 0,
				"data-id" => $task['id'],
				"name" => $task['name'],
				"status"=>NULL
			];

			$stmt_sub = $pdo->prepare("SELECT * FROM td_subtasks WHERE task_id = ?");
			$stmt_sub->execute([$task['id']]);
			while ($subtask = $stmt_sub->fetch()) {
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
