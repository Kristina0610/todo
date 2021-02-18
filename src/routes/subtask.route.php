<?php 
$errors = [];

switch (@$_GET['operation']) {
	case 'delete':
		
		if (!isset($_GET['subtask_id'])) {
			$errors[] = "Не передан идентификатор подзадачи";
		} else {
			$stmt = $pdo->prepare("SELECT * FROM td_subtasks WHERE id = ?");
			$stmt->execute([$_GET['subtask_id']]);
			$subtask = $stmt->fetch();

			if (!$subtask) {
				$errors[] = "Данная подзадача не найдена в БД";
			} 

			if (!$errors) {
				try {
					$stmt_t = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ?");
					$stmt_t->execute([$subtask['task_id']]);
					$task = $stmt_t->fetchAll();

					$stmt = $pdo->prepare("DELETE FROM td_subtasks WHERE id = ?");
					$stmt->execute([$_GET['subtask_id']]);

					if ($stmt->rowCount() > 0) {
						
						dump($task['project_id']);
						header("Location: /?section=project&id=".$task['project_id']);
						exit;
					}
				} catch (Exception $e) {
					$errors[] = "Системная ошибка, попробуйте удалить позже!";
				}
			}
		}
		break;
}
dump($errors);

$projects = getProjects();

include ("../templates/project.phtml");