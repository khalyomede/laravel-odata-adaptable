<?php

namespace Khalyomede;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Khalyomede\OdataQueryParser;
use Illuminate\Database\Eloquent\Builder;

class OdataBuilder extends Builder {
	public function adapt(Request $request) {
		$data = OdataQueryParser::parse($request->fullUrl());

		if (isset($data["select"])) {
			$hiddenColumns = $this->model->getHidden();

			if (is_array($hiddenColumns) && !empty(\array_intersect($hiddenColumns, $data["select"]))) {
				throw new InvalidArgumentException("one of the columns in the \$select query string value of the request is part of the hidden columns of its Eloquent model");
			}

			$this->select(...$data["select"]);
		}
		
		if (isset($data["top"])) {
			$this->limit($data["top"]);
		} 
		
		if (isset($data["skip"])) {
			$this->skip($data["skip"]);
		}

		if (isset($data["orderBy"])) {
			foreach ($data["orderBy"] as $orderBy) {
				$this->orderBy($orderBy["property"], $orderBy["direction"]);
			}
		}

		return $this;
	}

	public static function createFromBuilder( Builder $builder ) {
		$instance = new static($builder->getQuery());

		$objValues = get_object_vars($builder);
		
		foreach($objValues as $key => $value) {
            $instance->$key = $value;
        }
		
		return $instance;
	}
}
