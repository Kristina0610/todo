<?php 
$errors = [];
if (isset($_GET['id'])) {
	
	$project = getProject($_GET['id']);
	
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
		//Удаленные задачи:
		$stmt_td = $pdo->prepare("SELECT COUNT(*) FROM td_tasks WHERE project_id = ? AND deleted_at IS NOT NULL");
		$stmt_td->execute([$project_id]);
		$count_tasks_deleted = $stmt_td->fetchColumn();
	}

}

$projects = getProjects();

include("../templates/project.phtml");
