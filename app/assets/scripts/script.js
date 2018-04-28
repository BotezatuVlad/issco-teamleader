$(document).ready(function () {
	var savedName = localStorage.getItem('userName'),
		apiUrl = 'http://discounts-app.local:8081/',
		productsLoaded = false;

	if(null === savedName) savedName = '';

	$.ajax({
		method: 'POST',
		url: apiUrl + 'validate-user',
		data: {
			name: savedName
		}
	})
	.done(function (response) {
		console.log('response', response)

		savedName = response.data.name;

		localStorage.setItem('userName', savedName);
	})
	.fail(function () {
		console.log('user validation request failed');
	})
	.always(function () {
		loadProducts(savedName);
	});

	/**
	 * Load the list products via ajax request
	 * 
	 * @param {string} userName 
	 */
	function loadProducts(userName)
	{
		console.log('load products userName', userName);

		if(undefined == userName) userName = '';

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
	 * Append the products
	 * 
	 * @param {Array} products 
	 */
	function appendProducts(products)
	{
		console.log('append products', products)
	}
});