<?php

return [
	'except' => ['_debugbar.*'],
	'groups' => [
		'security' => [
			'security.logout',
			'security.login',
			'security.register',
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
			'home',
			'storage.local',
			'user.profile',
			'games.index',
			'games.show',
		],
		'user' => [
			'settings.profile.edit',
			'settings.profile.update',
			'settings.profile.destroy',
			'settings.password.edit',
			'settings.password.update',
			'settings.password.destroy',
			'settings.appearance',
		],
		'game_creator' => [
			'games.store',
			'games.create',
			'games.edit',
			'games.update',
			'games.destroy',
		],
		'mod' => [],
		'admin' => [],
		'dev' => []
	]
];
