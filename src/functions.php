<?php  

function getErrorsOfTasks($task_name,$title_name)
{
	global $pdo;
	$task_name = rtrim(mb_strtoupper(mb_substr($task_name, 0,1)).mb_substr($task_name, 1));

	$stmt = $pdo->prepare("SELECT name FROM td_tasks WHERE LOWER(name) LIKE LOWER(?) AND project_id IN (SELECT id FROM td_projects WHERE name = ?)");
	$stmt->execute([$task_name,$title_name]);
	$result = $stmt->fetch();

	return $result;
}
