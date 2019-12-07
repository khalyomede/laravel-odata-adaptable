# laravel-odata-adaptable

Adapt your Eloquent model automatically according to the OData query strings.

[![Packagist Version](https://img.shields.io/packagist/v/khalyomede/laravel-odata-adaptable)](https://packagist.org/packages/khalyomede/laravel-odata-adaptable) [![Packagist](https://img.shields.io/packagist/l/khalyomede/laravel-odata-adaptable)](https://packagist.org/packages/khalyomede/laravel-odata-adaptable) [![Build Status](https://travis-ci.com/khalyomede/laravel-odata-adaptable.svg?branch=master)](https://travis-ci.com/khalyomede/laravel-odata-adaptable) [![Coverage Status](https://coveralls.io/repos/github/khalyomede/laravel-odata-adaptable/badge.svg?branch=master)](https://coveralls.io/github/khalyomede/laravel-odata-adaptable?branch=master) [![Infection MSI](https://badge.stryker-mutator.io/github.com/khalyomede/laravel-odata-adaptable/master)](https://infection.github.io)
[![Maintainability](https://api.codeclimate.com/v1/badges/01c4e27c4f6b7d525ee4/maintainability)](https://codeclimate.com/github/khalyomede/laravel-odata-adaptable/maintainability)

## Summary

- [About](#about)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Examples](#examples)
- [API](#api)
- [Known issues](#known-issues)

## About

I needed to customize how my client side application fetches results from my Laravel REST API. Mostly, I just need to filter which fields I want to get from my API. I checked for [POData-Laravel](https://github.com/Algo-Web/POData-Laravel), but I found it too much strict.

I wanted a drop-in solution, that does not change the way I work with my existing Eloquent models, so I decided to create this library.

## Features

- use a Trait on your model, which will make them automatically mutate the result according to the OData query strings
- Support for the following OData keywords: `$select`, `$orderBy`, `$top` and `$skip`
- Throw an error if you use `$select` using `$hidden` columns

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

## Examples

- [1. Calling it at the very first](#1-calling-it-at-the-very-first)
- [2. Chaining it after previous call to the Eloquent query builder](#2-chaining-it-after-previous-call-to-the-eloquent-query-builder)

### 1. Calling it at the very first

In this example, we will call the `adapt` method to adapt our Eloquent ORM result according to the query strings received from the controller's request. The file below is extracted from an hypothetical controller.

```php
class BookController extends Controller {
  public function index(Request $request) {
    $books = Book::adapt($request)->get();
  }
}
```

### 2. Chaining it after previous call to the Eloquent query builder

In this example, we will call the `adapt` method right after some methods, to show you how you can still take advantage of this library even after existing changes to the result of your Eloquent query.

```php
class VueJsBookController extends Controller {
  public function index(Request $request) {
    $books = Book::where("name", "like", "Vue.js")->adapt($request)->get();
  }
}
```

## API

```php
public function adapt(Illuminate\Http\Request $request): Illuminate\Database\Eloquent\Builder;
```

**parameters**

- `Request $request`: The request from your Controller. It will be used to extract the URL using `$request->fullUrl()`.

**return**

An instance of `Builder` that let you chain other query builder methods.

**exceptions**

See all the exceptions thrown by [OdataQueryParser](https://packagist.org/packages/khalyomede/odata-query-parser).

## Known issues

The following OData V4 query strings commands will not work:

- count
- filter
