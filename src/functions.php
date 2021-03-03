<?php  
function rsearch($folder, $pattern) {
    $dir = new \RecursiveDirectoryIterator($folder);
    $ite = new \RecursiveIteratorIterator($dir);
    $files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);
    $fileList = array();
    foreach($files as $file) {
        $fileList = array_merge($fileList, $file);
    }
    return $fileList;
}

function getProjects()
{
	global $pdo;
	$stmt = $pdo->query("SELECT * FROM td_projects");
	$projects = $stmt->fetchAll();
	return $projects;
}

function getProject($project_id)
{
	global $pdo;
	$stmt = $pdo->prepare("SELECT * FROM td_projects WHERE id = ?");
	$stmt->execute([$project_id]);
	$project = $stmt->fetch();
	return $project;
}

function getTags()
{
	global $pdo;
	$stmt = $pdo->query("SELECT * FROM td_tags");
	$tags = $stmt->fetchAll();
	return $tags;
}

function getTagAndCount()
{	
	global $pdo;
	$stmt_t = $pdo->query("SELECT * FROM td_tags  ORDER BY name ASC");
	$tags = $stmt_t->fetchAll();

	$stmt = $pdo->prepare("SELECT COUNT(*) FROM td_tasks_tags WHERE tag_id = ? AND task_id IN (SELECT id FROM td_tasks WHERE deleted_at IS NULL)");
	$new_tags = [];
	foreach ($tags as $tag) {
		$stmt->execute([$tag['id']]);
		$new_tags[] = [
			"id"=>$tag['id'],
			"name"=>$tag['name'],
			"first_letter"=>mb_strtoupper(mb_substr($tag['name'], 0,1)),
			"count"=>$stmt->fetchColumn()
		];
	}
	return $new_tags;
}

function getFirstLetterTag($tags)
{
	global $pdo;
	$first_letters = [];
	foreach ($tags as $tag) {
		$first_letters[] = $tag['first_letter'];
	}
	return (array_unique($first_letters));
}

function setTaskTags($task_id,$tag_id)
{	
	global $pdo;
	$stmt_task_tag = $pdo->prepare("INSERT INTO td_tasks_tags(task_id,tag_id) VALUES (?,?)");
    $stmt_task_tag->execute([$task_id,$tag_id]);
}