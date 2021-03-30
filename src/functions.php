<?php  
use Carbon\Carbon;
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

function getTagAndCount2()
{	
	global $pdo;
	$stmt_t = $pdo->query("SELECT * FROM td_tags  ORDER BY name ASC");
	$tags = $stmt_t->fetchAll();

	$stmt = $pdo->prepare("SELECT COUNT(*) FROM td_tasks_tags WHERE tag_id = ? AND task_id IN (SELECT id FROM td_tasks WHERE deleted_at IS NULL)");
	$new_tags = [];
	foreach ($tags as $tag) {
		$stmt->execute([$tag['id']]);
		$new_tags[mb_strtoupper(mb_substr($tag['name'], 0,1))][] = [
			"id"=>$tag['id'],
			"name"=>$tag['name'],
			"count"=>$stmt->fetchColumn()
		];
	}
	return $new_tags;
}

function userStore($firstname = NULL, $phone = NULL, $password = NULL, $lastname = NULL, $status = NULL, $id = NULL){
	global $pdo;
	try {
		if($id) {
			$stmt = $pdo->prepare("UPDATE td_users SET status = :status WHERE id = :id");
			$stmt->execute([
				"status"=>'active',
				"id" => $id
			]);

			return $stmt->rowCount() > 0;

		} else {
			$stmt = $pdo->prepare("INSERT INTO td_users(firstname,lastname,phone,password,status) VALUES(:firstname,:lastname,:phone,:password,:status)");
			$stmt->execute([
				"firstname"=>$firstname,
				"lastname"=>$lastname,
				"phone"=>$phone,
				"password"=>password_hash($password, PASSWORD_DEFAULT),
				"status"=>$status ?? 'not_verified'
			]);

			return $pdo->lastInsertId();
		}
	} catch(Exception $e) {
		var_dump($e->getMessage());
		return false;
	}
}

function verificationCreate($code,$user_id,$expired_minutes = 15) {
	global $pdo;
	try {
		$stmt = $pdo->prepare("INSERT INTO td_phone_verification(code,created_at,expired_at,user_id) VALUES (:code,NOW(),:expired_at,:user_id)");
		$stmt->execute([
			"code"=>$code,
			"expired_at"=>Carbon::now()->addMinutes($expired_minutes)->toDateTimeString(),
			"user_id"=>$user_id
		]);
		
		return $pdo->lastInsertId();	
	} catch (Exception $e) {
		//var_dump($e->getMessage());
		return false;
	}
}