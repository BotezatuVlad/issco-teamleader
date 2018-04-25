#ISSCO test

add host

	- 127.0.0.1       discounts-api.local
	- 127.0.0.1       discounts-app.local



in command line execure

	- docker-compose up --build
	

steps from users's point of view:

- the user types http://discounts-app.local:8080 in browser
- if he is authenticated there will be a section on the page where he can see the list of discounts available for him
- each list item has a link to view the details for that offer
- on that page there is a button which allows him to add that offer in the cart

steps from app's point of view:
- the user types http://discounts-app.local:8080 in browser
- some js functionality checks if the user is authenticated via an ajax/websocket call in api
- if so, some other js functionality makes another call in api to load user's offers and displays that info somewhere on the page