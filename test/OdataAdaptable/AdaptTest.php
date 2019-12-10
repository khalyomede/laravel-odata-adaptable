<?php

use PHPUnit\Framework\TestCase;
use Illuminate\Http\Request;
use Khalyomede\OdataAdaptable;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

$db = new DB;

$db->addConnection([
	'driver'    => 'sqlite',
	'database'  => 'test/database.sqlite',
]);

$db->setEventDispatcher(new Dispatcher(new Container));
$db->setAsGlobal();
$db->bootEloquent();

class Book extends Model {
	use OdataAdaptable;

	protected $table = "book";
	protected $hidden = [
		"authorId"
	];
}

final class AdaptTest extends TestCase {	
	public function testShouldReturnAllTheFieldsIfNoOdataQueryStringsArePresentInTheRequestUri() {
		$request = Request::create('https://example.com/', "GET");
		$expected = [
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"description" => "All you need to know to build scalable and fun to use web apps using the wonderful Laravel+Vue.js combo."
			],
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"description" => "Learn how to bundle a modern Javascript web app using Gulp and Browserify"
			]
		];
		$actual = Book::where("id", ">=", 1)->adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnAllTheFieldsIfNoDataQueryStringsArePresentInTheRequestUriWhenCallingAdaptStatically() {
		$request = Request::create('https://example.com/', "GET");
		$expected = [
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"description" => "All you need to know to build scalable and fun to use web apps using the wonderful Laravel+Vue.js combo."
			],
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"description" => "Learn how to bundle a modern Javascript web app using Gulp and Browserify"
			]
		];
		$actual = Book::adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnOnlyTheNameIfTheSelectOdataQueryIsSetWithValueName() {
		$request = Request::create('https://example.com/?$select=title', "GET");
		$expected = [
			[
				"title" => "Build a website using Vue.js and Laravel",
			],
			[
				"title" => "The ultimate web bundler using Browserify and Gulp",
			]
		];
		$actual = Book::where("id", ">=", 1)->adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnOnlyTheNameIfTheSelectOdataQueryIsSetWithValueNameWhenCallingAdaptStatically() {
		$request = Request::create('https://example.com/?$select=title', "GET");
		$expected = [
			[
				"title" => "Build a website using Vue.js and Laravel",
			],
			[
				"title" => "The ultimate web bundler using Browserify and Gulp",
			]
		];
		$actual = Book::adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnTheLimitedRecordsIfTopOdataQueryStringIsPresentInTheRequestUri() {
		$request = Request::create('https://example.com/?$top=1', "GET");
		$expected = [
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"description" => "All you need to know to build scalable and fun to use web apps using the wonderful Laravel+Vue.js combo."
			]
		];
		$actual = Book::where("id", ">=", 1)->adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnTheLimitedRecordsIfTopOdataQueryStringIsPresentInTheRequestUriWhenCallingAdaptStatically() {
		$request = Request::create('https://example.com/?$top=1', "GET");
		$expected = [
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"description" => "All you need to know to build scalable and fun to use web apps using the wonderful Laravel+Vue.js combo."
			]
		];
		$actual = Book::adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnTheSecondRecordIfSkipIsSetToOneInTheQueryStrings() {
		$request = Request::create('https://example.com/?$top=1&$skip=1', "GET");
		$expected = [
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"description" => "Learn how to bundle a modern Javascript web app using Gulp and Browserify"
			]
		];
		$actual = Book::where("id", ">=", 1)->adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnTheSecondRecordIfSkipIsSetToOneInTheQueryStringsWhenCallingAdaptStatically() {
		$request = Request::create('https://example.com/?$top=1&$skip=1', "GET");
		$expected = [
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"description" => "Learn how to bundle a modern Javascript web app using Gulp and Browserify"
			]
		];
		$actual = Book::adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnTheDisorderedRecordsIfOrderByDescIsSetInTheQueryStrings() {
		$request = Request::create('https://example.com/?$orderby=title%20desc', "GET");
		$expected = [
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"description" => "Learn how to bundle a modern Javascript web app using Gulp and Browserify"
			],
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"description" => "All you need to know to build scalable and fun to use web apps using the wonderful Laravel+Vue.js combo."
			]
		];
		$actual = Book::where("id", ">=", 1)->adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldReturnTheDisorderedRecordsIfOrderByDescIsSetInTheQueryStringsWhenCallingAdaptStatically() {
		$request = Request::create('https://example.com/?$orderby=title%20desc', "GET");
		$expected = [
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"description" => "Learn how to bundle a modern Javascript web app using Gulp and Browserify"
			],
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"description" => "All you need to know to build scalable and fun to use web apps using the wonderful Laravel+Vue.js combo."
			]
		];
		$actual = Book::adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}

	public function testShouldThrowAnInvalidArgumentExceptionIfOneOfTheSelectColumnsIsPartOfHiddenColumns(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("one of the columns in the \$select query string value of the request is part of the hidden columns of its Eloquent model");
		
		$request = Request::create('https://example.com/?$select=name,authorId', "GET");

		Book::where("id", ">=", 1)->adapt($request);
	}

	public function testShouldThrowAnInvalidArgumentExceptionIfOneOfTheSelectColumnsIsPartOfHiddenColumnsWhenCallingAdaptStatically(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("one of the columns in the \$select query string value of the request is part of the hidden columns of its Eloquent model");
		
		$request = Request::create('https://example.com/?$select=name,authorId', "GET");

		Book::adapt($request);
	}
}
