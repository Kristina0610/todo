<?php 
$errors = [];

switch (@$_GET['operation']) {
	case 'delete':
		
		if (!isset($_GET['subtask_id'])) {
			$errors[] = "Не передан идентификатор подзадачи";
		} else {
			try {
				$stmt = $pdo->prepare("DELETE FROM td_subtasks WHERE id = ?");
				$stmt->execute([$_GET['subtask_id']]);

				if ($stmt->rowCount() > 0) {
					header("Location: /?section=subtask");
					exit;
				} else {
					$errors[] = "Данная подзадача не найдена в БД";
				}
			} catch (Exception $e) {
				$errors[] = "Системная ошибка, попробуйте удалить позже!";
			}	
		}
		break;
}


$projects = getProjects();

include ("../templates/subtask.phtml");