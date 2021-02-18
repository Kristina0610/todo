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

//Удаление задач из проекта soft-delete начало

switch (@$_GET['operation']) {
	case 'delete_task':
		if (!isset($_GET['task_id'])) {
			$errors['task_delete'] = "Не передан идентификатор задачи";
		} else {
			$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ?");
			$stmt->execute([$_GET['task_id']]);
			$task = $stmt->fetch();

			if (!$task) {
				$errors['task_delete'] = "Данная задача не найдена в БД";
			}
			if (!$errors) {
				try {
					$stmt = $pdo->prepare("UPDATE td_tasks SET deleted_at = NOW() WHERE id = ?");
					$stmt->execute([$_GET['task_id']]);

					header("Location: /?section=project&id=".$_GET['id']);
					exit;

				} catch(Exception $e) {
					$errors[] = "Попробуйте выполнить удаление задачи позже или обратитесь к администратору";
				}
			}
		}
		break;
}
//Удаление задач из проекта soft-delete конец

$projects = getProjects();

include("../templates/project.phtml");
