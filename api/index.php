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

function validOrder($order)
{
	$output = false;

	if(array_key_exists('customer-id', $order) && array_key_exists('items', $order) && is_array($order['items']) && count($order['items']) > 0)
	{
		$output = true;

		foreach($order['items'] as $item)
		{
			if(!array_key_exists('product-id', $item) || !array_key_exists('quantity', $item))
			{
				$output = false;

				break;
			}
		}
	}

	return $output;
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

// place an order to get discounts
$app->post('/place-order', function () use ($app) {
	$response = new Response();

	$output = [
		'status' => 'ERROR',
		'code' => 1,
		'message' => 'Invalid request'
	];

	if($app->request->isPost())
	{
		$filter = new Filter();

		$decodedOrder = json_decode($app->request->getPost('order'), true);

		$orderData = null;

		$productsDiscount = $orderDiscount = $productsValue = $discountValue = $totalValue = 0;

		if(json_last_error() == JSON_ERROR_NONE && validOrder($decodedOrder))
		{
			$productsIDs = [];

			$quantities = [];

			foreach($decodedOrder['items'] as $item)
			{
				$productsIDs[] = $filter->sanitize($item['product-id'], "string");

				$quantities[] = intval($item['quantity']);
			}

			$syntax = "
				SELECT 
				`products`.`id` AS `productID`,
				`products`.`description` AS `productDescription`,
				`products`.`category` AS `productCategoryID`,
				`products`.`price` AS `productPrice`,
				`categories`.`name` AS `categoryName`,
				`categories_discounts`.`discpountType`,
				`categories_discounts`.`discountName`,
				`categories_discounts`.`discountPercentage`,
				`categories_discounts`.`discountBuy`,
				`categories_discounts`.`discountBonus`
				FROM `products`
				LEFT JOIN `categories` ON `products`.`category` = `categories`.`id`
				LEFT JOIN `categories_discounts` ON `categories_discounts`.`category` = `categories`.`id`
				WHERE 
					`products`.`id` IN ('".implode("', '", $productsIDs)."')";

			$results = $app->db->query($syntax);
	
			$results->setFetchMode(\Phalcon\Db::FETCH_OBJ);

			$orderData = $results->fetchAll();

			$customerID = intval($decodedOrder['customer-id']);

			$customer = $app->db->query("SELECT * FROM `customers` WHERE `id` = ? LIMIT 1", [$customerID]);
		
			$customer->setFetchMode(\Phalcon\Db::FETCH_OBJ);

			if(!empty($orderData) && !empty($customer->fetch()))
			{
				$groupedByCategory = [];

				foreach($orderData as $product)
				{
					$clone = clone($product);

					if(!array_key_exists($product->productCategoryID, $groupedByCategory))
					{
						$groupedByCategory[$product->productCategoryID] = [
							'discount' => $clone,
							'products' => []
						];
					}

					if(!array_key_exists($product->productID, $groupedByCategory[$product->productCategoryID]['products']))
					{
						$groupedByCategory[$product->productCategoryID]['products'][$product->productID] = $clone;
					}
				}

				foreach($groupedByCategory as $categoryID => $category)
				{
					switch($category['discount']->discpountType)
					{
						case 'percentageOfCheapest':
							usort($category['products'], function($a, $b){
								return $a->productPrice <=> $b->productPrice;
							});
							$productsDiscount += round($category['products'][0]->productPrice * $category['discount']->discountPercentage / 100, 2);
							break;
						case 'bonusProduct':
							$productsCount = count($category['products']);

							usort($category['products'], function($a, $b){
								return $b->productPrice <=> $a->productPrice;
							});

							$bonusProductsCount = floor($productsCount / ($category['discount']->discountBuy + $category['discount']->discountBonus)) * $category['discount']->discountBonus;

							if($bonusProductsCount > 0)
							{
								$bonusProducts = [];

								for($i = $productsCount - $bonusProductsCount; $i < $productsCount; $i++)
								{
									$bonusProducts[] = $category['products'][$i];
								}

								foreach($bonusProducts as $product)
								{
									$productsDiscount += $product->productPrice;
								}
							}
							break;
						default:
							break;
					}

					foreach($category['products'] as $product)
					{
						$productsValue += $product->productPrice;
					}
				}

				$usersWithOrderDiscount = [1 => false, 2 => true, 3 => false];

				if(array_key_exists($customerID, $usersWithOrderDiscount) && true === $usersWithOrderDiscount[$customerID])
				{
					$discountMinValue = $productsValue * .1;

					if($productsDiscount < $discountMinValue)
					{
						$orderDiscount = round($discountMinValue - $productsDiscount, 2);
					}
				}
			}
		}

		$discountValue = $productsDiscount + $orderDiscount;

		$totalValue = $productsValue - $discountValue;

		if(!empty($orderData))
		{
			$output = [
				'status' => 'OK',
				'code' => 0,
				'data' => [
					'productsValue' => $productsValue,
					'discountValue' => $discountValue,
					'totalValue' => $totalValue,
				]
			];
		}
		else
		{
			$output['code'] = 2;

			$output['message'] = 'Discounts not found';
		}
	}
	
	$response->setJsonContent($output);
	
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

