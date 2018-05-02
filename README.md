#ISSCO test

Add host

	- 127.0.0.1       discounts-api.local
	- 127.0.0.1       discounts-app.local



In command line execure

	- docker-compose up --build
	

Steps from users's point of view:

- the user types http://discounts-app.local:8080 in browser
- if he is authenticated there will be a section on the page where he can see the list of discounts available for him
- each list item has a link to view the details for that offer
- on that page there is a button which allows him to add that offer in the cart

Steps from app's point of view:

- the user types http://discounts-app.local:8080 in browser
- some js functionality checks if the user is authenticated via an ajax/websocket call in api
- if so, some other js functionality makes another call in api to load user's offers and displays that info somewhere on the page


There are 3 kinds of discounts:

- per order (10% on the entire order if he is an old customer)
- per category (buy 5 get 6th for free if added in cart)
- per product (2||100 get 20% of the cheapest one)

All conditions must be met

We have an overall discount for products and an $orderDiscount

	- $productsDiscount = 0;

	- $orderDiscount = 0;

How the discount works

	- load from database all products that have been ordered left join with products_discounts left join with categories left join with categories_discounts
	
	- in php group the products by category
	
	- get unique products_discounts for each category
	
	- find the value of the discount for each category and add it to $productsDiscount

When we have finished with the discounts per product and per category. Get the value of $orderDiscount;

Then check if $productsDiscount is at least equal with $orderDiscount. If not add to 

	- $orderDiscount 

the value of 

	 - $orderDiscount - $productsDiscount

This way all the conditions are met
