<?php

return [
	'except' => ['_debugbar.*'],
	'groups' => [
		'security' => [
			'security.logout',
			'security.login',
			'security.register',
			'home',
			'security.password.request',
			'security.password.email',
			'security.password.reset',
			'security.password.store',
			'security.password.confirm',
			'security.verification.notice',
			'security.verification.verify',
			'security.verification.send',
		],
		'guest' => [
			'storage.local',
		],
		'user' => [
			'settings.profile.edit',
			'settings.profile.update',
			'settings.profile.destroy',
			'settings.password.update',
			'settings.password.destroy',
			'settings.appearance',
		],
		'game_creator' => [],
		'mod' => [],
		'admin' => [],
		'dev' => []
	]
];
