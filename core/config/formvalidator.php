<?php
return array (
	'register' => array (
		array (
			'field' => 'mail',
			'validator' => 'preg',
			'args' => array (
				'/^[^@]+@[^@]+\.[a-z0-9]{2,6}$/ui',
			),
			'error' => array (
				'ru' => 'Введите правильный почтовый адрес!',
				'en' => '',
			),
		),
		array (
			'field' => 'pass',
			'validator' => 'minlen',
			'args' => array (
				6,
			),
			'error' => array (
				'ru' => 'Пароль короче 6 символов!',
				'en' => '',
			),
		),
		array (
			'field' => 'pass',
			'validator' => 'equals',
			'args' => array (
				'pass2',
			),
			'error' => array (
				'ru' => 'Пароли не совпадают!',
				'en' => '',
			),
		),
		array (
			'field' => 'pass',
			'validator' => 'maxlen',
			'args' => array (
				56,
			),
			'error' => array (
				'ru' => 'Имя длинниее 56 символов!',
				'en' => '',
			),
		),
		array (
			'field' => 'name',
			'validator' => 'minlen',
			'args' => array (
				3,
			),
			'error' => array (
				'ru' => 'Введите свое имя!',
				'en' => '',
			),
		),
		array (
			'field' => 'nick',
			'validator' => 'minlen',
			'args' => array (
				3,
			),
			'error' => array (
				'ru' => 'Минимальная длинна ника 3 символа!',
				'en' => '',
			),
		),
		array (
			'field' => 'nick',
			'validator' => 'maxlen',
			'args' => array (
				16,
			),
			'error' => array (
				'ru' => 'Максимальная длинна ника 16 символа!',
				'en' => '',
			),
		),
	),
);