<?php

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

$app = new Micro();

$app->get('/', function () use ($app) {
	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => [],
		'message' => 'api root'
	]);
	   
	return $response;
});

$app->post('/', function () use ($app) {
	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => [],
		'message' => 'check if the user is authenticated'
	]);
	   
	return $response;
});

$app->post('/auth', function () use ($app) {
	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => [],
		'data' => 'try to authenticated the user'
	]);
	
	return $response;
});

$app->get('/get-discounts', function () use ($app) {
	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => [],
		'data' => 'get users`s discounts'
	]);
	
	return $response;
});

$app->notFound(function () use ($app) {
	$app->response
		->setStatusCode(404, 'Not Found')
		->sendHeaders()
		->setContent('Ooops... The requested page could not be found')
		->send();
});

$app->handle();

