$(document).ready(function () {
	var savedName = localStorage.getItem('userName'),
		apiUrl = 'http://discounts-app.local:8081/',
		productsLoaded = false,
		cache = {
			templates: {}
		},
		$productsContainer = $('#productsContainer'),
		$userContainer = $('#userContainer');

	if (null == savedName) savedName = '';

	/**
	 * Validate user
	 */
	validateUser();

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
			method: 'GET',
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
	 * Validate stored user
	 */
	function validateUser()
	{
		$.ajax({
			method: 'POST',
			url: apiUrl + 'validate-user',
			data: {
				name: savedName
			}
		})
		.done(function(response){
			if(undefined != response.data.name)
			{
				savedName = response.data.name;
				
				buildDom('#discountTemplate', response.data, function(dom){
					$userContainer.html(dom);
				});
			}
			else
			{
				savedName = '';
				
				buildDom('#loginTemplate', {}, function(dom){
					$userContainer.html(dom);
				});
			}
	
			localStorage.setItem('userName', savedName);
		})
		.fail(function () {
			console.log('user validation request failed');
		})
		.always(function () {
			loadProducts(savedName);
		});
	}
});