$(document).ready(function () {
	var savedName = localStorage.getItem('userName'),
		apiUrl = 'http://discounts-app.local:8081/',
		productsLoaded = false,
		cache = {
			templates: {}
		},
		$productsContainer = $('#productsContainer'),
		$ordersContainer = $('#ordersContainer'),
		orders = {},
		loadedOrders = 0;

	if (null == savedName) savedName = '';

	/**
	 * Set some events
	 */
	$('body').off('click', '.place-order').on('click', '.place-order', function(event){
		placeOrder(parseInt($(this).data('order-id')))
	});

	/**
	 * Load orders
	 */
	loadOrders();

	/**
	 * Validate user
	 */
	loadProducts(savedName);

	/**
	 * Append all orders
	 */
	function appendOrders()
	{
		$ordersContainer.html('');

		_.each(orders, function(order){
			buildDom('#orderTemplate', order, function(dom){
				$ordersContainer.append(dom);
			})
		});
	}

	/**
	 * Append the products
	 * 
	 * @param {Array} products 
	 */
	function appendProducts(products)
	{
		if(products.length > 0)
		{
			$productsContainer.html('');

			_.each(products, function(product){
				buildDom('#productTemplate', product, function(dom){
					$productsContainer.append(dom);
				})
			});
		}
		else
		{
			buildDom('#noProductTemplate', {}, function(dom){
				$productsContainer.html(dom);
			});
		}
	}

	/**
	 * Return dom from underscore template and data
	 * 
	 * @param {String} elementID 
	 * @param {Object} data 
	 */
	function buildDom(elementID, data, callback)
	{
		data = data || {};

		callback = callback || function() {};

		if(undefined == cache.templates[elementID]) cache.templates[elementID] = _.template($(elementID).html());

		callback(cache.templates[elementID](data));
	}

	/**
	 * Load the list products via ajax request
	 * 
	 * @param {string} savedName 
	 */
	function loadProducts(savedName)
	{
		$.ajax({
			type: 'GET',
			url: apiUrl + 'load-products',
			data: {
				name: savedName
			}
		})
		.done(function (response) {
			appendProducts(response.data)
		})
		.fail(function () {
			console.log('load products request failed');
		})
		.always(function () {
		});
	}

	/**
	 * Load order by id
	 * 
	 * @param {int} id 
	 */
	function loadOneOrder(id)
	{
		$.getJSON("/orders/order" + id + ".json", function(data){
			orders["order" + id] = data;

			loadedOrders++;

			if(loadedOrders == 3) appendOrders();
		});
	}

	/**
	 * Load all orders
	 */
	function loadOrders()
	{
		var i,
			count = 3;

		for(i = 1; i <= count; i++)
		{
			loadOneOrder(i);
		}
	}

	/**
	 * Place order
	 * 
	 * @param {int} id 
	 */
	function placeOrder(id)
	{
		var orderID = 'order' + id;

		if(undefined != orders[orderID])
		{
			$.ajax({
				url: apiUrl + 'place-order',
				type: 'POST',
				data: {
					order: JSON.stringify(orders[orderID])
				},
			})
			.done(function(response){
				$('#discountsContainer').html(JSON.stringify(response));
			})
		}
	}
});