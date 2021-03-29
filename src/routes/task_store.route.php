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
            if (empty($_POST['name'])) {
                $errors['name'] = "Вы не указали название задачи";
            } else {
                $_POST['name'] = mb_strtoupper(mb_substr($_POST['name'], 0,1)).mb_substr($_POST['name'],1);
                if (!isset($_GET['task_id'])) {
                    $stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE LOWER(name) LIKE LOWER(?) AND project_id = ? AND deleted_at IS NULL");
                    $stmt->execute([$_POST['name'],$_GET['project_id']]);
                    $result = $stmt->fetch();

                    if ($result) {
                        $errors['name'] = "В данном проекте уже существует задача с таким названием.";
                    }
                }   
            }
        }
    }
    if (!$errors) {
       try {
        $pdo->beginTransaction();

        $data = [
            "name" => $_POST['name'],
            "priority" => $_POST['priority'] ?? 'low'
        ];

        if (isset($_GET['task_id'])) {
            $stmt = $pdo->prepare("UPDATE td_tasks SET name = :name, project_id = :project_id, priority = :priority WHERE  id = :id");
            $data['project_id'] = $_POST['project_id'];
            $data['id'] = $_GET['task_id'];
            $stmt->execute($data);
            $last_id = $_GET['task_id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO td_tasks(name,project_id,priority) VALUES (:name,:project_id,:priority)");
            $data['project_id'] = $_GET['project_id'];
            $stmt->execute($data);
            $last_id = $pdo->lastInsertId();
        }

        if (!empty($_POST['tags'])) {
            $tags = array_map('trim', explode(',', $_POST['tags']));
            //dump($tags); 

            $stmt = $pdo->prepare("DELETE FROM td_tasks_tags WHERE task_id = ?");
            $stmt->execute([$last_id]);
 
            foreach ($tags as $tag) {
                $stmt = $pdo->prepare("SELECT id FROM td_tags WHERE name LIKE ?");
                $stmt->execute([$tag]);
                $tag_id = $stmt->fetchColumn();

                if ($tag_id == false) {
                    $stmt = $pdo->prepare("INSERT INTO td_tags(name) VALUES (?)");
                    $stmt->execute([$tag]);
                    $last_tag_id = $pdo->lastInsertId();
                    setTaskTags($last_id,$last_tag_id);
                } else {
                    setTaskTags($last_id,$tag_id);
                }
                
            }
        }

        $pdo->commit();

        header("Location: /?section=project&id=".$data['project_id']);
        exit;

       } catch (Exception $e) {
            $errors[] = "Системная ошибка, не удалось выполнить вставку задачи в БД. Обратитесь к администратору";
            $pdo->rollback();
       }
    }
}
$fields = [];
if (isset($_GET['task_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE id = ?"); //AND project_id = ?");
    $stmt->execute([$_GET['task_id']]);
    $task = $stmt->fetch();

    if(!$task) {
        $errors['task_not_found'] = "Данная задача не найдена";
    } else {
        $fields['name'] = $task['name'];

        $stmt = $pdo->prepare("SELECT name FROM td_tasks_tags, td_tags WHERE td_tasks_tags.tag_id = td_tags.id AND td_tasks_tags.task_id = ?");
        $stmt->execute([$_GET['task_id']]);

        $tag_names = array_column($stmt->fetchAll(), 'name');
        $fields['tags'] = implode(', ', $tag_names);
        $fields['priority'] = $task['priority'];
        $fields['project_id'] = $task['project_id'];
    }
}

$priorities = ["extreme"=>"Экстримальный","high"=>"Высокий","middle"=>"Средний","low"=>"Низкий"];

$fields = isset($_POST['submit']) ? $_POST : $fields; 
$tags = getTagAndCount();
$projects = getProjects();

include ("../templates/task_store.phtml");