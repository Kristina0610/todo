<?php

if (isset($_POST['submit'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}

	$errors = [];
	if (empty($_POST['title'])) {
		$errors['title'] = "Вы не указали название проекта";
	} else {
		$_POST['title'] = mb_strtoupper(mb_substr($_POST['title'], 0,1)).mb_substr($_POST['title'], 1);
		
		$stmt = $pdo->prepare("SELECT name FROM td_projects WHERE LOWER(name) LIKE LOWER(?)");
		$stmt->execute([$_POST['title']]);
		$result = $stmt->fetch();

		if($result) {
			$errors['title'] = "Проект с таким названием уже существует.";
		}
	}

	if (empty($_POST['task_0'])) {
		$errors['task_0'] = "Укажите задачу номер 1";
	} else {
		for ($i=0; $i < 5; $i++) { 
			if (!empty($_POST['task_'.$i])) {
				if (!isUniqueTask($_POST['task_'.$i],$_POST['title'])) {
					$errors['task_'.$i] = "В данном проекте уже есть задача с таким названием, как у задачи ".($i+1);
				}
			}
		}
	}

	if (!$errors) {
		try {
			$pdo->beginTransaction();
			
			$stmt_projects = $pdo->prepare("INSERT INTO td_projects(name) VALUES(?)");
			$stmt_projects->execute([$_POST['title']]);

			$last_id = $pdo->lastInsertId();

			$stmt_tasks = $pdo->prepare("INSERT INTO td_tasks(name,project_id) VALUES (?,?)");

			for ($i=0; $i < $count_tasks; $i++) { 
				if (isset($_POST['task_'.$i])) {
					$stmt_tasks->execute([$_POST['task_'.$i],$last_id]);
				}
			}
			
			$pdo->commit();
			header("Location: /?section=index");
			exit;

		} catch (Exception $e) {
			$errors[] = "Попробуйте выполнить вставку позже или обратитесь к администратору";
			$pdo->rollback();
		}
	}
	
}

//dump($_POST);


if (isset($_GET['id'])) {
	$get_start_id = mb_substr($_GET['id'], 0,1);
	$get_finish_id = mb_substr($_GET['id'], 1);

	if ($get_start_id == 'p') {
		$stmt_project = $pdo->prepare("SELECT * FROM td_projects WHERE id = ?");
		$stmt_project->execute([$get_finish_id]);
		$project = $stmt_project->fetch();

		if ($project > 0) {
			$fields['title'] = $project['name'];
			//dump($fields);
			$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM td_tasks WHERE project_id = ?");
			$stmt_count->execute([$get_finish_id]);
			$count_tasks = $stmt_count->fetch();
			dump($count_tasks);
			if ($count_tasks['COUNT(*)'] > 0) {
				$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE project_id = ?");
				$stmt->execute([$get_finish_id]);
				$tasks = $stmt->fetchAll();
				dump($tasks);
				for ($i=0; $i < $count_tasks['COUNT*'] ; $i++) { 
					dump($i);
					$fields['task_'.$i] = $tasks[$i]['name'];
				}
			}
		}
	
	}
}

$fields = isset($_POST['submit']) ? $_POST : @$fields;



include ("../templates/project_store.phtml");