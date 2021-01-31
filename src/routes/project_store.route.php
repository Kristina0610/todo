<?php 

$stmt = $pdo->query("SELECT * FROM td_projects");
$projects = $stmt->fetchAll();
//dump($projects);
$items = [];
foreach ($projects as $index => $project) {
	$items[] = [
		"data-parent" => 0,
		"data-level" => 1,
		"data-id" => $project['id'],
		"name" => $project['name']	
	];
	$stmt = $pdo->prepare("SELECT * FROM td_tasks WHERE project_id = ?");
	$stmt->execute([$project['id']]);
	while ($task = $stmt->fetch()) {
	 	$items[] = [
			"data-parent" => $project['id'],
			"data-level" => 2,
			"data-id" => $task['id'],
			"name" => $task['name']	
		];
	}
}

dump($items);

include ("../templates/project_store.phtml");