<?php 

$password = '';

$directory = '.';
$subdirectories = false;
$databases = array(
	array(
		'path'=> 'database1.sqlite',
		'name'=> 'Database 1'
	),
	array(
		'path'=> 'database2.sqlite',
		'name'=> 'Database 2'
	),
);
$theme = 'phpliteadmin.css';
$language = 'en';
$rowsNum = 30;

$charsNum = 300;

$custom_functions = array(
	'md5', 'sha1', 'time', 'strtotime',
	
);
$cookie_name = '';

$debug = false;
$allowed_extensions = array('db','db3','sqlite','sqlite3');

