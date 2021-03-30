<?php 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === "POST") {

	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	$errors = [];
	
	if (empty($_POST['code'])) {
		$errors[] = "Не забудь указать 6-значный код";
	}

	if ($errors) {
		echo json_encode([
			"errors" => $errors
		]);
	} else {
		$stmt = $pdo->prepare("SELECT user_id FROM td_phone_verification WHERE code = :code");
		$stmt->execute(["code" => $_POST['code']]);

		$user_id = $stmt->fetchColumn(); 

		if (!$user_id) {
			echo json_encode([
				"errors" => ["Код указан неверно!"]
			]);
		} else {
			//userStore($user_id);
			$stmt = $pdo->prepare("UPDATE td_users SET status = 'active' WHERE id = :id");
			$stmt->execute(["id" => $user_id]);
			if ($stmt->rowCount() < 1) {
				echo json_encode([
					"errors" => ["Произошла системная ошибка! Повторите попытку позже"]
				]);
			} else {
				echo json_encode([
					"data" => "Ok"
				]);
			}
		}
	}
}