<?php
//Удаление задач из проекта soft-delete начало


if (!isset($_GET['task_id'])) {
	$flash->setError('task_delete_'.$_GET['task_id'], 'Не передан идентификатор задачи');
} 
	
if (!$flash->hasError('task_delete_'.$_GET['task_id'])) {
	try {
		$stmt = $pdo->prepare("UPDATE td_tasks SET deleted_at = NOW() WHERE id = ?");
		$stmt->execute([$_GET['task_id']]);

		if($stmt->rowCount() > 0) {
			$flash->setSuccess('task_delete', 'success');
		} else {
			$flash->setError('task_delete_'.$_GET['task_id'],'Переданный идентификатор не найден в БД');
		}

	} catch(Exception $e) {
		$flash->setError('task_delete_'.$_GET['task_id'],'Попробуйте выполнить удаление задачи позже или обратитесь к администратору');
	}
}
header("Location: /?section=project&id=".$_GET['project_id']);
exit;
//Удаление задач из проекта soft-delete конец