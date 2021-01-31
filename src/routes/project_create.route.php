<?php

if (isset($_POST['submit'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}

	$errors = [];
	$errors[] = 1;
	if (empty($_POST['title'])) {
		$errors['title'] = "Вы не указали название проекта";
	} else {
		$_POST['title'] = rtrim(mb_strtoupper(mb_substr($_POST['title'], 0,1)).mb_substr($_POST['title'], 1));
		
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
		getErrorsOfTasks($_POST['task_0'],$_POST['title']);
		if ($result) {
			$errors['task_0'] = "В данном проекте уже есть задача с таким названием, как у задачи 1";		
		}
	}
	for ($i=1; $i < 5; $i++) { 
		if (!empty($_POST['task_'.$i])) {
			getErrorsOfTasks($_POST['task_'.$i],$_POST['title']);
			if ($result) {
				$errors['task_'.$i] = "В данном проекте уже есть задача с таким названием, как у задачи ".($i+1);
			}
		}
	}
	dump($errors);
	if (!$errors) {
		dump(123);
		try {
			$pdo->beginTransaction();
			
			$stmt_projects = $pdo->prepare("INSERT INTO td_projects(name) VALUES(?)");
			$stmt_projects->execute([$_POST['title']]);

			$last_id = $pdo->lastInsertId();

			for ($i=0; $i < 5; $i++) { 
				if (isset($_POST['task_'.$i])) {
					$stmt_tasks = $pdo->prepare("INSERT INTO td_tasks(name,project_id) VALUES (?,?)");
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


$fields = isset($_POST['submit']) ? $_POST : @$fields;



include ("../templates/project_create.phtml");