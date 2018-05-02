<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Filter;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Model\Manager as ModelsManager;

use Phalcon\Config;

$cumulativePromotions = false;

function pre()
{
	$numargs = func_num_args();
	echo '---------------------------------------';
	echo '<pre>';

	if($numargs > 0)
	{
		$arg_list = func_get_args();

		foreach($arg_list as $arg)
		{
			print_r($arg);
		}
	}
	else
	{
		print_r('No arguments passed');
	}
	echo '</pre>';
	echo '---------------------------------------';
}

// create dependency injection container
$di = new FactoryDefault();

// connect to database
$di->set('db', function(){
	return new PdoMysql([
		"host" => "mysql",
		"dbname" => getenv('MYSQL_DATABASE'),
		"port" => 3306,
		"username" => getenv('MYSQL_USER'),
		"password" => getenv('MYSQL_PASSWORD')
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

// api root
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

// authenticate user
$app->get('/auth', function () use ($app) {
	$response = new Response();

	$output = [
		'status' => 'ERROR',
		'code' => 1,
		'data' => [],
		'message' => 'Invalid request'
	];

	$isAjaxPost = true;// $app->request->isPost() && $request->isAjax();

	if($isAjaxPost)
	{
		$filter = new Filter();
	
		$username = $filter->sanitize($app->request->get('name'), "string");

		$userObj = null;

		if(!empty($username))
		{
			$result = $app->db->query("SELECT * FROM `customers` WHERE `name` = :name: ORDER BY `id` LIMIT 1", ['name' => $username]);
		
			$result->setFetchMode(\Phalcon\Db::FETCH_OBJ);
			
			$userObj = $result->fetch();
		}

		if(!empty($userObj))
		{
			$output = [
				'status' => 'OK',
				'code' => 0,
				'data' => $userObj,
				'message' => 'User found'
			];
		}
		else
		{
			$output['code'] = 2;

			$output['message'] = 'User not found';
		}
	}
	
	$response->setJsonContent($output);
	
	return $response;
});

// get user discount
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

// get the list of products
$app->get('/load-products', function () use ($app) {
	$products = $app->db->fetchAll("SELECT * FROM `products` ORDER BY `id`", \Phalcon\Db::FETCH_OBJ);

	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => $products,
		'message' => 'api root'
	]);
	   
	return $response;
});

$app->post('/validate-user', function () use ($app) {
	$response = new Response();
	
	$response->setJsonContent([
		'status' => 'OK',
		'code' => 0,
		'data' => [],
		'message' => 'check if the user is valid'
	]);
	   
	return $response;
});

// page not found
$app->notFound(function () use ($app) {
	$app->response
		->setStatusCode(404, 'Not Found')
		->sendHeaders()
		->setContent('Ooops... The requested page could not be found')
		->send();
});

$app->handle();

