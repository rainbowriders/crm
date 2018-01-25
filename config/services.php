<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => null,
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'User',
		'secret' => '',
	],
	'sparkpost' => [
		'secret' => env('MAIL_SECRET'),
	],
	'linkedin' => [
		'client_id' => null,
		'client_secret' => null,
		'redirect' => 'http://localhost:8000',
	],
	'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', null),
        'client_secret' => env('GOOGLE_CLIEND_SECRET', null),
        'redirect' => config('app.url') . '/auth/google/callback',
        'scopes' => [
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/calendar.readonly',
            'https://www.googleapis.com/auth/gmail.modify',
        ],
    ],
];
