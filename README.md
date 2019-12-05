# laravel-odata-adaptable

Adapt your Eloquent model automatically according to the OData query strings.

## Summary

- [About](#about)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Known issues](#known-issues)

## About

I needed to customize how my client side application fetches results from my Laravel REST API. Mostly, I just need to filter which fields I want to get from my API. I checked for [POData-Laravel](https://github.com/Algo-Web/POData-Laravel), but I found it too much strict.

I wanted a drop-in solution, that does not change the way I work with my existing Eloquent models, so I decided to create this library.

## Requirements

- PHP >= 7.2.0
- [Composer](https://getcomposer.org)

## Installation

Add the dependency:

```bash
composer require khalyomede/laravel-odata-adaptable
```

## Usage

Add the `OdataAdaptable` trait to your existing model.

```php
// app/Book.php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Khalyomede\OdataAdaptable;

class Book extends Model {
	use OdataAdaptable;
}
```

You should have a route like:

```php
// routes/api.php

Route::resource("book", "BookController");
```

In your controller, add the capability to adapt to the OData V4 query strings to your Eloquent model like this:

```php
namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class BookController extends Controller {
    public function index(Request $request) {
		return Book::adapt($request)->get();
	}
}
```

Now, if you run this HTTP query

```
GET /api/book
```

You will get

```
[
  {
    "id": 1,
    "authorId": 1,
    "name": "Build an SPA using Gulp & Vue.js"
  }
]
```

But if you use OData query strings, you will be able to do

```
GET /api/book?$select=name
```

And you will get

```
[
  {
    "name": "Build an SPA using Gulp & Vue.js"
  }
]
```

## Known issues

The following OData V4 query strings commands will not work:

- count
- orderBy
- top
- skip
- filter
