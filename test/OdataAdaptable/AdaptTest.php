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
}

final class AdaptTest extends TestCase {	
	public function testShouldReturnAllTheFieldsIfNoOdataQueryStringsArePresentInTheRequestUri() {
		$request = Request::create('https://example.com/', "GET");
		$expected = [
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"authorId" => 1
			],
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"authorId" => 1
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
				"authorId" => 1
			],
			[
				"id" => 2,
				"title" => "The ultimate web bundler using Browserify and Gulp",
				"authorId" => 1
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
				"authorId" => 1
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
				"authorId" => 1
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
				"authorId" => 1
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
				"authorId" => 1
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
				"authorId" => 1
			],
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"authorId" => 1
			],
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
				"authorId" => 1
			],
			[
				"id" => 1,
				"title" => "Build a website using Vue.js and Laravel",
				"authorId" => 1
			],
		];
		$actual = Book::adapt($request)->get()->toArray();
		
		$this->assertEquals($expected, $actual);
	}
}
