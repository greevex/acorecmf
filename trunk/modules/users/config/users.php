<?php
return array(
	'enter_by' => array(
		'mail' => null,
		'pass' => "md5",
	),
	'register_by' => array(
		'mail' => array(
			'/^[^@]+@[^\.]+\..+$/ui',
			array (
				'ru' => 'Введите правльный mail!',
			),
			null,
		),
		'pass' => array(
			'/^.{6,}$/ui',
			array (
				'ru' => 'Длинная пароля должна быть не меньше 6 символов!',
			),
			'md5',
		),
		'name' => array(
			'/^[a-zа-я ]+$/ui',
			array (
				'ru' => 'Введите правильное имя!',
			),
			null,
		),
	),
);
?>