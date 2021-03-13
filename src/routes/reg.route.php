<?php 

//6LfDzX0aAAAAACtkM-jCSPdBueePWEAqMguPQVFQ  - секретный ключ 
//6LfDzX0aAAAAAEHlspKjdFAwe2oT_DiEz7FLfSIv  - ключ сайта
$errors = [];


if (isset($_POST['reg-submit'])) {
	dump($_POST);
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	if (empty($_POST['firstname'])) {
		$errors['firstname'] = "Укажите имя";
	}
	if (empty($_POST['phone'])) {
		$errors['phone'] = "Укажите номер телефона";
	} elseif (preg_match('/^\+7[0-9]{10}$/', $_POST['phone']) == false) {
		$errors['phone'] = "Не верный формат номера телефона";
	}
	if (empty($_POST['password'])) {
		$errors['password'] = "Укажите пароль";
	} elseif (mb_strlen($_POST['password']) < 6) {
		$errors['password'] = "Пароль должен быть не менее 6-ти символов";
	}
	if (empty($_POST['re-password'])) {
		$errors['re-password'] = "Повторите пароль";
	} elseif ($_POST['password'] !== $_POST['re-password']) {
		$errors['re-password'] = "Повтор пароля не совпадает с заданным паролем";
	}
	if (@$_POST['checkbox'] !== 'on') {
		$errors['checkbox'] = "Необходимо дать согласие на пользовательское соглашение";
	}
	if (!$_POST['g-recaptcha-response']) {
		$errors['g-recaptcha-response'] = "Подтвердите, что вы не робот";
	} else {
		$url = "https://www.google.com/recaptcha/api/siteverify";
		$key = "6LfDzX0aAAAAACtkM-jCSPdBueePWEAqMguPQVFQ";
		$query = $url.'?secret='.$key.'&response='.$_POST['g-recaptcha-response'];
	
		$data = json_decode(file_get_contents($query));

		if ($data->success == false) {
			$errors['g-recaptcha-response'] = "Стоит пройти re-captcha снова!";
		}
	}

}


include("../templates/reg.phtml");