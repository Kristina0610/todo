<?php 
$errors = [];

if (!isset($_GET['id'])) {
	$errors['project_not_found'] = "Не передан идентификатор проекта";
} else {
	$project = getProject(@$_GET['id']);

	if (!$project) {
		$errors['project_not_found'] = "Данный проект не найден в БД";
	} 
}

if (isset($_POST['submit'])) {
	if (empty($_POST['delete'])) {
		$errors[] = "Передано пустое значение поля, чтобы произошло удаление - следуйте инструкции!";
	} else {
		$key_phrase = rtrim(mb_strtolower($_POST['delete']));

		if ($key_phrase !== "я хочу удалить проект!") {
			$errors[] = "Проверьте правильность ввода ключевой фразы!";
		}
	}

	if (!$errors) {
		try {
			$pdo->beginTransaction();

			$stmt = $pdo->prepare("DELETE FROM td_projects WHERE id = ?");
			$stmt->execute([$project['id']]);
			$project_result = $stmt->rowCount() > 0;

			if ($stmt->rowCount() > 0) {
				
				$stmt_task = $pdo->prepare("SELECT id FROM td_tasks WHERE project_id = ?");
				$stmt_task->execute([$project['id']]);
				$tasks = $stmt_task->fetchAll();
				
				$stmt_delete_subt = $pdo->prepare("DELETE FROM td_subtasks WHERE task_id = ?");
				foreach ($tasks as $task) {
					$stmt_delete_subt->execute([$task['id']]);
				}

				$stmt_delete_t = $pdo->prepare("DELETE FROM td_tasks WHERE project_id = ?");
				$stmt_delete_t->execute([$project['id']]);
			} else {
				throw new Exception("Не удалось удалить проект"); 
			}

			$pdo->commit();
		} catch (Exception $e) {
			$pdo->rollback();
			dump($e->getMessage());
			
			$errors[] = "Произошла системная ошибка, попробуйте удалить проект позже или обратитесь к администратору";
			
		}
	}
}

$projects = getProjects();

include("../templates/project_deleted.phtml");