<?php 

$errors = [];
switch (@$_GET['operation']) {
	case 'delete':
		if (!isset($_GET['id'])) {
			$errors[] = "Не передан идентификатор статьи";
		} else {
			$get_start_id = mb_substr($_GET['id'],0,1);

			$get_finish_id = mb_substr($_GET['id'], 1);
			if ($get_start_id == 't') {
				$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ?");
				$stmt->execute([$get_finish_id]);
				$task = $stmt->fetch();
				if (!$task) {
					$errors[] = "Данная задача не найдена в БД";
				}
			} elseif ($get_start_id == 'p') {
				$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ?");
				$stmt->execute([$get_finish_id]);
				$project = $stmt->fetch();
				if(!$project) {
					$errors[] = "Данный проект не найден в БД";
				}
			} else {
				$errors[] = "Проверьте правильность переданного id";
			}
			if (!$errors) {
				try {
					if ($get_start_id == 't') {
						$stmt = $pdo->prepare("DELETE FROM td_tasks WHERE id = ?");
						$stmt->execute([$get_finish_id]);
						if ($stmt->rowCount() > 0) {
							header("Location: /?section=project");
							exit;
						}
					}
					if ($get_start_id == 'p') {
						$pdo->beginTransaction();
						$stmt = $pdo->prepare("DELETE FROM td_projects WHERE id = ?");
						$stmt->execute([$get_finish_id]);

						if ($stmt->rowCount() > 0) {
							$stmt = $pdo->prepare("DELETE FROM td_tasks WHERE project_id = ?");
							$stmt->execute([$get_finish_id]);
							$pdo->commit();
							if ($stmt->rowCount() > 0) {
								header("Location: /?section=project");
								exit;
							}
						}
					}

				} catch (Exception $e) {
					$errors[] = "Системная ошибка. Попробуйте удалить позже";
					$pdo->rollBack();
				}
			}
		}
		break;
	
	default:
		break;
}

$stmt = $pdo->query("SELECT * FROM td_projects");
$projects = $stmt->fetchAll();

$items = [];

foreach ($projects as $index => $project) {
	$items[] = [
		"data-parent" => 0,
		"data-level" => 1,
		"data-id" => $project['id'],
		"name" => $project['name']
	];

	$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE project_id = ?");
	$stmt->execute([$project['id']]);
	while ($task = $stmt->fetch()) {
		$items[] = [
			"data-parent" =>$project['id'],
			"data-level" => 2,
			"data-id" => $task['id'],
			"name" => $task['name']
		];	
	}
}


include ("../templates/project.phtml");