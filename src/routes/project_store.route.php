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

	if (!$errors) {
		try {

			$stmt_projects = $pdo->prepare("INSERT INTO td_projects(name) VALUES(?)");
			$stmt_projects->execute([$_POST['title']]);

			header("Location: /?section=index");
			exit;

		} catch (Exception $e) {
			$errors[] = "Попробуйте выполнить вставку позже или обратитесь к администратору";
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

		if (!$project) {
			$errors['project_not_found'] = "Данный проект не найден в БД";
		} else {
			$fields['title'] = $project['name'];
		
			$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE project_id = ?");
			$stmt->execute([$get_finish_id]);
			$project['tasks'] = $stmt->fetchAll();
			
			foreach ($project['tasks'] as $key => $value) {
				$fields['task_'.$key] = $value['name'];
			}
			dump($fields);
			
		}
	} 
}

$fields = isset($_POST['submit']) ? $_POST : @$fields;

$stmt = $pdo->query("SELECT * FROM td_projects");
$projects = $stmt->fetchAll();

include ("../templates/project_store.phtml");