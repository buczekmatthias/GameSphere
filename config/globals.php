<?php

return [
	'form' => [
		'description' => [
			'min_length' => 50
		],
		'files' => [
			'thumbnail' => [
				'accept_type' => 'image/jpeg,image/png,image/webp'
			],
			'media' => [
				'accept_type' => 'image/jpeg,image/png,image/webp,video/mp4',
				'max_files' => 6
			]
		]
	]
];
