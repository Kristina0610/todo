<?php 
$errors = [];
if (isset($_GET['id'])) {
	
	$project = getProject($_GET['id']);
	
	if (!$project) {
		$errors['project_not_found'] = "Данный проект не найден в БД";
	}
	if (!$errors) {
		$project_id = $_GET['id'];
		$stmt=$pdo->prepare("SELECT * FROM td_tasks WHERE project_id = ? AND deleted_at IS NULL");
		$stmt->execute([$project_id]);
		
		$items = [];
		
		while($task = $stmt->fetch()) {
			$stmt_t = $pdo->prepare("SELECT * FROM td_tasks_tags WHERE task_id = ?");
			$stmt_t->execute([$task['id']]);
			$rez=$stmt_t->fetch();
			if (!$rez) {
				$items[] = [
					"data-parent" => 0,
					"data-id" => $task['id'],
					"name" => $task['name'],
					"priority" => $task['priority'],
					"status"=>NULL,
					"tags" => NULL
				];	
			} else {
				$stmt_tags = $pdo->prepare("SELECT td_tags.* FROM td_tasks_tags, td_tags WHERE td_tasks_tags.task_id = ? AND td_tasks_tags.tag_id = td_tags.id");
				$stmt_tags->execute([$task['id']]);
				
				while ($tags = $stmt_tags->fetchAll()) {
					$items[] = [
						"data-parent" => 0,
						"data-id" => $task['id'],
						"name" => $task['name'],
						"priority" => $task['priority'],
						"status"=>NULL,
						"tags" => $tags
					];	
				}
			}
			
			$stmt_sub = $pdo->prepare("SELECT * FROM td_subtasks WHERE task_id = ?");
			$stmt_sub->execute([$task['id']]);
			while ($subtask = $stmt_sub->fetch()) {
				$items[] = [
					"data-parent" =>$task['id'],
					"data-id" => $subtask['id'],
					"name" => $subtask['name'],
					"priority" => NULL,
					"status"=>$subtask['status_completed'],
					"tags" => NULL
				];	
			}
		}
		/*foreach ($items as $item) {
			switch ($item['priority']) {
				case 'extreme':
					$color = 'red';
					break;
				case 'high':
					$color = 'yellow';
					break;
				case 'middle':
					$color = 'green';
					break;
				case 'low':
					$color = 'grey';
					break;
				default:
					break;
			}
		}*/
		
		//Удаленные задачи:
		$stmt_td = $pdo->prepare("SELECT COUNT(*) FROM td_tasks WHERE project_id = ? AND deleted_at IS NOT NULL");
		$stmt_td->execute([$project_id]);
		$count_tasks_deleted = $stmt_td->fetchColumn();
	}
}

$tags = getTagAndCount();
$projects = getProjects();

include("../templates/project.phtml");
