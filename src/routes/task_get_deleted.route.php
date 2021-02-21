<?php 
$errors = [];


if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
	$errors['project_not_found'] = "Не передан идентифиикатор проекта";
} else {
	$project = getProject($_GET['project_id']);
	if (!$project) {
		$errors['project_not_found'] = "Данный проект не найден в БД";
	} else {
		$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE deleted_at IS NOT NULL AND project_id = ?");
		$stmt->execute([$_GET['project_id']]);
		$tasks_deleted = $stmt->fetchAll();

		if (!$tasks_deleted) {
			$errors['project_not_found'] = "У данного проекта нет удаленных задач";
		}
	}
}


if (isset($_POST['tasks'])) {
	if (!$errors) {
		try {
			$stmt = $pdo->prepare("UPDATE td_tasks SET deleted_at = NULL WHERE id = ?");
			foreach ($_POST['tasks'] as $task_id) {
				$stmt->execute([$task_id]);	
			}

			header("Location: /?section=project&id=".$_GET['project_id']);
			exit;
			
		} catch (Exception $e) {
			$errors[] = "Не удалось восстановить задачи. Системная ошибка, обратитесь к администратору";
		}
	}
}



$projects = getProjects();
include("../templates/task_get_deleted.phtml");