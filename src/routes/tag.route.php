<?php 
$errors = [];

if (isset($_GET['tag_id'])) {
	$stmt = $pdo->prepare("SELECT * FROM td_tags WHERE id = ?");
	$stmt->execute([$_GET['tag_id']]);
	$tag_all = $stmt->fetchAll();

	if (!$tag_all) {
		$errors['tag_not_found'] = "Тег не найден в БД";
	} else {
		$stmt = $pdo->prepare("SELECT task_id FROM td_tasks_tags WHERE tag_id = ? AND task_id IN (SELECT id FROM td_tasks WHERE deleted_at IS NULL)");
		$stmt->execute([$_GET['tag_id']]);
		$task_ids = array_column($stmt->fetchAll(), 'task_id');

		if (!$task_ids) {
			$errors['tag_dont_have_task'] = "К данному тегу не относится ни одна задача";
		} else {
			$items = [];
			$in  = str_repeat('?,', count($task_ids) - 1) . '?';  //  передаем значения массива в виде ?,?,?....
			$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id IN ({$in})");
			$stmt->execute($task_ids);
			$tasks = $stmt->fetchAll();
			foreach ($tasks as $task) {
				$stmt_p = $pdo->prepare("SELECT name FROM td_projects WHERE id = ?");
				$stmt_p->execute([$task['project_id']]);
				while ($project_name = $stmt_p->fetchColumn()) {
					$items[] = [
						"task_id" => $task['id'],
						"task_name" => $task['name'],
						"project_id" => $task['project_id'],
						"project_name" => $project_name
					];
				}
			}
		}
	}
}

$tags = getTagAndCount();
$projects = getProjects();

include ("../templates/tag.phtml");
dump($tag['name']);