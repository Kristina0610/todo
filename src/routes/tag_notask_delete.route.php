<?php
try {
	$stmt = $pdo->query("DELETE FROM td_tags WHERE id NOT IN (SELECT DISTINCT tag_id FROM td_tasks_tags, td_tasks WHERE td_tasks_tags.task_id = td_tasks.id AND deleted_at IS NULL)");
	$stmt = $pdo->query("DELETE FROM td_tasks_tags WHERE tag_id NOT IN (SELECT id FROM td_tags)");
	
	if ($stmt->rowCount() > 0) {
		$flash->setSuccess('tag_notask_delete','success');
	}
} catch (Exception $e) {
	$flash->setError('tag_notask_delete','Попробуйте выполнить удаление тегов без задач позже или обратитесь к администратору');
}

header("Location: /?section=index");
exit;