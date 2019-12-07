<?php

namespace Khalyomede;

use Illuminate\Http\Request;
use Khalyomede\OdataBuilder;
use InvalidArgumentException;
use Khalyomede\OdataQueryParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait OdataAdaptable {
	public static function adapt(Request $request): Builder {
		$instance = new static;

		$builder = $instance->newEloquentBuilder(
            $instance->newBaseQueryBuilder()
		)->setModel($instance);

		$data = OdataQueryParser::parse($request->fullUrl());

		if (isset($data["select"])) {
			$hiddenColumns = $instance->getHidden();

			if (is_array($hiddenColumns) && !empty(\array_intersect($hiddenColumns, $data["select"]))) {
				throw new InvalidArgumentException("one of the columns in the \$select query string value of the request is part of the hidden columns of its Eloquent model");
			}

			$builder->addSelect(...$data["select"]);
		}
		
		if (isset($data["top"])) {
			$builder->limit($data["top"]);
		}

		if (isset($data["skip"])) {
			$builder->skip($data["skip"]);
		}

		if (isset($data["orderBy"])) {
			foreach ($data["orderBy"] as $orderBy) {
				$builder->orderBy($orderBy["property"], $orderBy["direction"]);
			}
		}
		
		return $builder;
	}

	public static function __callStatic($name, $arguments) {
		$return = parent::__callStatic($name, $arguments);

		if ($return instanceof Builder) {
			return OdataBuilder::createFromBuilder($return);
		}

		return $return;
	}
}
