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
			$data = [$_POST['title']];

			if (isset($_GET['id'])) {
				$stmt = $pdo->prepare("UPDATE td_projects SET name = ? WHERE id = ?");
				$data[] = $_GET['id'];
			} else {
				$stmt = $pdo->prepare("INSERT INTO td_projects(name) VALUES(?)");
			}
			
			$stmt->execute($data);

			header("Location: /?section=index");
			exit;

		} catch (Exception $e) {
			$errors[] = "Попробуйте выполнить вставку позже или обратитесь к администратору";
		}
	}
	
}

//dump($_POST);


if (isset($_GET['id'])) {
	
	$stmt_project = $pdo->prepare("SELECT * FROM td_projects WHERE id = ?");
	$stmt_project->execute([$_GET['id']]);
	$project = $stmt_project->fetch();

	if (!$project) {
		$errors['project_not_found'] = "Данный проект не найден в БД";
	} else {
		$fields['title'] = $project['name'];
	} 
}

$fields = isset($_POST['submit']) ? $_POST : @$fields;

$tags = getTagAndCount();
$projects = getProjects();

include ("../templates/project_store.phtml");