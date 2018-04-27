<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Model\Manager as ModelsManager;

// create dependency injection container
$di = new FactoryDefault();

// connect to database
$di->set('db', function(){
	return new PdoMysql([
		"host"     => "mysql",
		"dbname"   => getenv('MYSQL_DATABASE'),
		"port"     => 3306,
		"username" => getenv('MYSQL_USER'),
		"password" => getenv('MYSQL_PASSWORD'),
	]);
});

$loader = new Loader();

// register namspaces
$loader->registerNamespaces(
    [
        "Issco\\Store" => __DIR__."/models/store/",
        "Issco\\Accounts" => __DIR__."/models/accounts/",
    ]
);

$loader->register();

$app = new Micro($di);

// get the list of products
$app->get('/', function () use ($app) {
	$data = [];

	$phql = "SELECT * FROM Issco\\Store\\Products ORDER BY id";

	$products = $app->modelsManager->executeQuery($phql);

	foreach ($products as $product) {
		$data[] = [
			'id'   => $product->id,
			'description' => $product->description,
		];
	}

	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => $data,
		'message' => 'api root'
	]);
	   
	return $response;
});

$app->post('/validate-token', function () use ($app) {
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

$app->get('/products', function () use ($app) {
	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => $data['products'],
		'message' => 'the list of products'
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

