#ISSCO test

Add host

	- 127.0.0.1       discounts-api.local
	- 127.0.0.1       discounts-app.local

In command line execure

	- docker-compose up --build
	
	- type http://discounts-app.local:8080 in browser

There are 2 kinds of discounts:

- per order (10% on the entire order if he is a loyal customer)
- per category (buy 5 get 6th for free if added in cart) OR (2||100 get 20% of the cheapest one)

All conditions must be met

We have an overall discount for products and an $orderDiscount

	- $productsDiscount = $orderDiscount = $productsValue = $discountValue = $totalValue = 0;

How the discount works

	- load from database all products that have been ordered
	left join with categories 
	left join with categories_discounts
	
	- in php, group the products by category
	
	- get unique products_discounts for each category
	
	- find the value of the discount for each category and add it to $productsDiscount

When we have finished with the discounts per category. Get the value of 

	$productsValue (the sum of all products prices);

If is loyal customer he gets a 10 % discount on order

	$discountMinValue = $productsValue * 10/100;

Then check if 

	$productsDiscount < $discountMinValue

If so

	$orderDiscount = $discountMinValue - $productsDiscount;


Having all this data available results in

	$discountValue = $productsDiscount + $orderDiscount;

	$totalValue = $productsValue - $discountValue;

This way all the conditions are met
