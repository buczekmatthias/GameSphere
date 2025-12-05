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
				'max_files' => 6,
				'max_size' => 10 * 1024 * 1024, //10MB
				'max_size_display' => '10MB'
			]
		]
	]
];
