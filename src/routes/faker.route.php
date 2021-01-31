<?php 

$faker = Faker\Factory::create('ru_RU');

$stmt =$pdo->query("SELECT id FROM td_projects");
$project_ids = array_column($stmt->fetchAll(), 'id');
dump($project_ids);	

if (@$_POST['submit']) {

	for ($i=0; $i < 10; $i++) { 
		//Формирую данные для таблицы td_projects
		$project_name = $faker->unique()->word;

		//Формирую данные для таблицы td_tsks
		$task_name = $faker->unique()->sentence(6,true);
		$project_id = $faker->randomElement($array = $project_ids);

		$stmt = $pdo->prepare("INSERT INTO td_projects(name) VALUES(?)");
		$stmt->execute([$project_name]);

		$stmt_tasks = $pdo->prepare("INSERT INTO td_tasks(name,project_id) VALUES(?,?)");
		$stmt_tasks->execute([$task_name,$project_id]);
	}
}



include ("../templates/faker.phtml");
