<?php
if (isset($_POST['submit'])) {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = trim($value);
    }
    $errors = [];

    if (!isset($_GET['project_id'])) {
        $errors['project_not_found'] = "Не указан проект, в который необходимо добавить задачу";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM td_projects WHERE id = ?");
        $stmt->execute([$_GET['project_id']]);
        $project = $stmt->fetch();

        if (!$project) {
            $errors['project_not_found'] = "Проект, в который Вы хотите добавить задачу не найден в БД";
        } else {
            if (empty($_POST['title'])) {
                $errors['title'] = "Вы не указали название задачи";
            } else {
                $_POST['title'] = mb_strtoupper(mb_substr($_POST['title'], 0,1)).mb_substr($_POST['title'],1);

                $stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE LOWER(name) LIKE LOWER(?) AND project_id = ?");
                $stmt->execute([$_POST['title'],$_GET['project_id']]);
                $result = $stmt->fetch();

                if ($result) {
                    $errors['title'] = "В данном проекте уже существует задача с таким названием.";
                }
            }
        }
    }

    if (!$errors) {
       try {
        $data = [$_POST['title'],$_GET['project_id']];

        if (isset($_GET['task_id'])) {
            $stmt = $pdo->prepare("UPDATE td_tasks SET name = ? WHERE project_id = ? AND id = ?");
            $data[] = $_GET['task_id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO td_tasks(name,project_id) VALUES (?,?)");
        }

        $stmt->execute($data);

        header("Location: /?section=project&id=".$_GET['project_id']);
        exit;

       } catch (Exception $e) {
            $errors[] = "Системная ошибка, не удалось выполнить вставку задачи в БД. Обратитесь к администратору";
       }
    }
}

if (isset($_GET['task_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ? AND project_id = ?");
    $stmt->execute([$_GET['task_id'],$_GET['project_id']]);
    $task = $stmt->fetch();

    if(!$task) {
        $errors['task_not_found'] = "Данной задачи нет в этом проекте";
    } else {
        $fields['title'] = $task['name'];
    }
}

$fields = isset($_POST['submit']) ? $_POST : @$fields;

$tags = getTagAndCount();
$projects = getProjects();

include ("../templates/task_store.phtml");