<?php 
header('Content-Type: application/json');
//6LfDzX0aAAAAACtkM-jCSPdBueePWEAqMguPQVFQ  - секретный ключ 
//6LfDzX0aAAAAAEHlspKjdFAwe2oT_DiEz7FLfSIv  - ключ сайта
use Curl\Curl;

if ($_SERVER['REQUEST_METHOD'] === "POST") {

	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	$errors = [];
	if (empty($_POST['firstname'])) {
		$errors['firstname'] = "Укажите имя";
	}
	if (empty($_POST['phone'])) {
		$errors['phone'] = "Укажите номер телефона";
	} elseif (preg_match('/^\+7[0-9]{10}$/', $_POST['phone']) == false) {
		$errors['phone'] = "Не верный формат номера телефона";
	} else {
		$stmt = $pdo->prepare("SELECT COUNT(*) FROM td_users WHERE phone = ?");
		$stmt->execute([$_POST['phone']]);
		if ($stmt->fetchColumn() > 0) {
			$errors['phone'] = "Данный номер телефона уже зарегистрирован";
		}
	}
	if (empty($_POST['password'])) {
		$errors['password'] = "Укажите пароль";
	} elseif (mb_strlen($_POST['password']) < 6) {
		$errors['password'] = "Пароль должен быть не менее 6-ти символов";
	}
	if (empty($_POST['re_password'])) {
		$errors['re_password'] = "Повторите пароль";
	} elseif ($_POST['password'] !== $_POST['re_password']) {
		$errors['re_password'] = "Повтор пароля не совпадает с заданным паролем";
	}
	if (@$_POST['agree'] !== 'true') {
		$errors['agree'] = "Необходимо дать согласие на пользовательское соглашение";
	}
	/*if (empty($_POST['g-recaptcha-response'])) {
		$errors['g-recaptcha-response'] = "Подтвердите, что вы не робот";
	} else {
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$key = "6LfDzX0aAAAAACtkM-jCSPdBueePWEAqMguPQVFQ";
		$query = $url.'?secret='.$key.'&response='.$_POST['g-recaptcha-response'];
	
		$data = json_decode(file_get_contents($query));

		if ($data->success == false) {
			$errors['g-recaptcha-response'] = "Стоит пройти re-captcha снова!";
		}
	}*/
	

	if ($errors) {
		echo json_encode([
			"errors"=>$errors
		]);
	} else {
		
		$curl = new Curl("https://smsc.ru/sys/send.php");
		$curl->get([
			"login" => "Kristi0610",
			"psw" => "parol123",
			"phones" => $_POST['phone'],
			"mes" => "code",
			"call" => "1",
			"fmt" => "3"
		]);
		
		if (!isset($curl->response->code)) {
			echo json_encode([
				"errors" => "Сбой отправки кода! Обратитесь к администратору"
			]);
			exit;
		} else {
			$user_id = userStore($_POST['firstname'],$_POST['phone'],$_POST['password'],$_POST['lastname']);
			verificationCreate($curl->response->code,$user_id);
			
			echo json_encode([
				"data"=>"success"
			]);
		}
	}
}

