<?php
return array(
	'managers' => array(
		'admin' => array(
			'pass' => 'password',
			'mail' => 'admin@localhost',
			'm_type' => 0,
			'notepad' => '',
		),
	),
	'm_types' => array(
		array(
			'name' => 'Администратор',
		),
		array(
			'name' => 'Тестер',
			'mods' => array(
				'system|userPage',
				'system|settingsPage',
				'system|managersPage',
				'system|mTypesPage',
				'system|constsPage',
				'system|pagesPage',
				'system|tplsPage',
				'files|folderPage',
			),
		),
	),
);
?>