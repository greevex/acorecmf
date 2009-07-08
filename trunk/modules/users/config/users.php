<?php
return array(
	'enter_error' => array(
		'ru' => 'Пользователь не найден!',
		'en' => '...',
	),
	'enter_by' => array(
		'mail' => '',
		'pass' => 'md5',
	),
	'register_by' => array(
		'mail' => array(
			'/^[^@]+@[^\.]+\..+$/ui',
			array(
				'ru' => 'Введите правльный mail!',
				'en' => 'Enter valid mail!',
			),
			'',
		),
		'pass' => array(
			'/^.{6,}$/ui',
			array(
				'ru' => 'Длинная пароля должна быть не меньше 6 символов!',
				'en' => 'Password length must be >= 6!',
			),
			'md5',
		),
		'name' => array(
			'/^[a-zа-я ]+$/ui',
			array(
				'ru' => 'Введите правильное имя!',
				'en' => 'Enter valid name!',
			),
			'',
		),
	),
	'register_error' => array(
		'ru' => 'Данный почтовый адрес уже используется в системе!',
		'en' => '...',
	),
	'register_complite' => array(
		'ru' => 'Вы успешно зарегистрировались в системе!',
		'en' => '...',
	),
);
?>