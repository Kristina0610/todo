<?php 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === $_POST) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	$errors = [];
	echo json_encode(["test" => "123"]);
	if (empty($_POST['code'])) {
		$errors['code'] = "Не забудь указать 6-значный код";
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
			userStore($user_id);
			//dump(userStore($user_id));
			echo json_encode([
				"data" => "Ok"
			]);
		}
	}
}