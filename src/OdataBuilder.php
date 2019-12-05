<?php

namespace Khalyomede;

use Khalyomede\OdataQueryParser;
use Illuminate\Database\Eloquent\Builder;

class OdataBuilder extends Builder {
	public function adapt(string $url) {
		$data = OdataQueryParser::parse($url);

		if (isset($data["select"])) {
			
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
