<?php

namespace Khalyomede;

use Illuminate\Http\Request;
use Khalyomede\OdataQueryParser;
use Illuminate\Database\Eloquent\Builder;

class OdataBuilder extends Builder {
	public function adapt(Request $request) {
		$data = OdataQueryParser::parse($request->fullUrl());

		if (isset($data["select"])) {
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
