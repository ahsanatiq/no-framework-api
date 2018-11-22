# Search API Service

A simple Search API service where users can search the recipes.

## Application Architecture

> All the key-design points of the application are already described in the `recipe-service`. It follows all the same principles with the following additional packages:

##### Store & sync recipes data in Elasticsearch:

Recipes information are being stored and updated in the Elasticsearch as the events (createRecipe, updateRecipe, deleteRecipe) are being happened on the `recipe-service`. The events with the recipe data are being transfered in a real-time using the fast redis-server. To implement and utilize the message queue we have used the library [Illuminate/Queues](https://github.com/illuminate/queue) and [Predis](https://github.com/nrk/predis) on `recipe-service` and `search-service`. All the jobs are stored in the `app/Jobs` directory, which are triggered when the events occured in redis.

##### Search the recipes from Elasticsearch:

To update and fetch the information from Elasticsearch we have used the official client library [Elasticsearch-PHP](https://github.com/elastic/elasticsearch-php). All the queries are being executed on the elastsearch to fetch the recipes in fast and efficient way.

