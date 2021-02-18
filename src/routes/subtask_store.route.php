<?php

if (isset($_POST['submit'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}

	$errors = [];

    if (!isset($_GET['task_id'])) {
        $errors['task_not_found'] = "Не указана задача, в которую необходимо добавить подзадачу";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ?");
        $stmt->execute([$_GET['task_id']]);
        $task = $stmt->fetch();

        if (!$task) {
            $errors['task_not_found'] = "Задача, в которую Вы хотите добавить подзадачу не найдена в БД";
        } else {
            if (empty($_POST['title'])) {
                $errors['title'] = "Вы не указали название подзадачи";
            } else {
                $_POST['title'] = mb_strtoupper(mb_substr($_POST['title'], 0,1)).mb_substr($_POST['title'],1);

                $stmt = $pdo->prepare("SELECT * FROM td_subtasks WHERE LOWER(name) LIKE LOWER(?) AND task_id = ?");
                $stmt->execute([$_POST['title'],$_GET['task_id']]);
                $result = $stmt->fetch();

                if ($result) {
                    $errors['title'] = "В данной задаче уже существует подзадача с таким названием.";
                }
            }
        }
    }

	if (!$errors) {
		try {
			$data = [$_POST['title'],$_GET['task_id']];

			if (isset($_GET['subtask_id'])) {
				$stmt = $pdo->prepare("UPDATE td_subtasks SET name = ?, task_id = ?, status_completed = NULL WHERE id = ?");
				$data[] = $_GET['subtask_id'];
			} else {
				$stmt = $pdo->prepare("INSERT INTO td_subtasks(name,task_id,status_completed) VALUES(?,?,NULL)");
			}
			
			$stmt->execute($data);

			header("Location: /?section=project&id=".$task['project_id']);
			exit;

		} catch (Exception $e) {
			$errors[] = "Попробуйте выполнить вставку позже или обратитесь к администратору";
		}
	}
	
}

//dump($_POST);


if (isset($_GET['subtask_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM td_subtasks WHERE id = ?");
    $stmt->execute([$_GET['subtask_id']]);
    $subtask = $stmt->fetch();

    if(!$subtask) {
        $errors['subtask_not_found'] = "Данной подзадача не найдена в БД";
    } else {
        $fields['title'] = $subtask['name'];
    }
}

$fields = isset($_POST['submit']) ? $_POST : @$fields;

$projects = getProjects();

include ("../templates/subtask_store.phtml");