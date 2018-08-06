# Symfony Image Search

A basic app for searching, downloading, augmenting and bundling images on popular sites like "Google Image", "Flickr" etc.

(Under Development!!!)

## Getting Started

### Prerequisites


```
composer
php-gd
(check composer.json for other requirements)
```

### Install and Run


```
composer install
```


```
php bin/console server:run
```

You need to add your own API keys to below entries in ```services.yaml```
```
FLICKR_API_KEY
FLICKR_API_SECRET
GOOGLE_CX
GOOGLE_API_KE
```

Result Limits (```services.yaml```)
```
Edit this entry for changing the limit.
RESULT_LIMIT: 10
```


## Example

### Endpoint

Send a "POST" request to ```/search```

Example JSON request:
```
[
	{
		"query": "cat",  // required
		"source": "google_image",  // required, can be flickr, google_image or your own custom search engine
		"opts": ["zoom", "rotate"]  // optional and only accepts "zoom" and "rotate" for now
	},
	{
		"query": "dog",
		"source": "flickr",
		"opts": ["rotate"]
	}
]
```

### Custom Search Engines

You can add your own custom search engines with a basic way.

* Extend ```BaseSearchEngine``` class and implement required methods (```search``` and ```prepare```)
* You can check ```Searchable``` interface to see those methods.
* Also check ```FlickrSearchEngine``` and ```GoogleImageSearchEngine``` classes for examples.

## IMPORTANT
You have to add your custom engine to ````earch_engine.yaml```` as child service of ```BaseSearchEgine``` and 
they should start with ````search.engine_name```` prefix. So you can call it in json query as ```engine_name```

Example:
````
  search.engine_name:
    class: App\Service\SearchEngine\YourSearchEngine
    parent: App\Service\SearchEngine\BaseSearchEngine
````

## Authors

* **Rüzgar Burak Topal** - *lexygon* - [hello@ruzgar.me](mailto:hello@ruzgar.me)

