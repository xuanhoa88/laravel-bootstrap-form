<?php

namespace Llama\BootstrapForm\Converter\JqueryValidation;

use Llama\BootstrapForm\Converter\Base\Rule as BaseRule;

class Rule extends BaseRule {
	public function Required($parameters, $attribute) {
		return [ 
				'data-rule-required' => 'true' 
		];
	}
	public function Email($parameters, $attribute) {
		return [ 
				'data-rule-email' => 'true' 
		];
	}
	public function Url($parameters, $attribute) {
		return [ 
				'data-rule-url' => 'true' 
		];
	}
	public function Int($parameters, $attribute) {
		return [ 
				'data-rule-digits' => 'true' 
		];
	}
	public function Numeric($parameters, $attribute) {
		return [ 
				'data-rule-number' => 'true' 
		];
	}
	public function Ip($parameters, $attribute) {
		return [ 
				'data-rule-ipv4' => 'true' 
		];
	}
	public function Same($parameters, $attribute) {
		return [ 
				'data-rule-equalto' => vsprintf ( '*[name=%1s]', $parameters ) 
		];
	}
	public function Regex($parameters, $attribute) {
		$rule = $parameters [0];
		if (substr ( $rule, 0, 1 ) == substr ( $rule, - 1, 1 )) {
			$rule = substr ( $rule, 1, - 1 );
		}
		
		return [ 
				'data-rule-regex' => $rule 
		];
	}
	public function Alpha($parameters, $attribute) {
		return [ 
				'data-rule-regex' => '^[A-Za-z _.-]+$' 
		];
	}
	public function Alphanum($parameters, $attribute) {
		return [ 
				'data-rule-regex' => '^[A-Za-z0-9 _.-]+$' 
		];
	}
	public function Image($parameters, $attribute) {
		return [ 
				'accept' => 'image/*' 
		];
	}
	public function Date($parameters, $attribute) {
		return [ 
				'data-rule-date' => 'true' 
		];
	}
	public function Min($parameters, $attribute, $type) {
		$minlength = vsprintf ( '%1s', $parameters );
		switch ($type) {
			case 'numeric' :
				return [ 
						'data-rule-min' => $minlength,
						'minlength' => mb_strlen ( minlength ) 
				];
			default :
				return [ 
						'data-rule-minlength' => $minlength,
						'minlength' => minlength 
				];
		}
	}
	public function Max($parameters, $attribute, $type) {
		$maxlength = vsprintf ( '%1s', $parameters );
		switch ($type) {
			case 'numeric' :
				return [ 
						'data-rule-max' => $maxlength,
						'maxlength' => mb_strlen ( $maxlength ) 
				];
			default :
				return [ 
						'data-rule-maxlength' => $maxlength,
						'maxlength' => $maxlength 
				];
		}
	}
	public function Between($parameters, $attribute, $type) {
		switch ($type) {
			case 'numeric' :
				$range = vsprintf ( '%1s,%2s', $parameters );
				return [ 
						'data-rule-range' => $range,
						'minlength' => mb_strlen ( explode ( ',', $range ) [0] ),
						'maxlength' => mb_strlen ( explode ( ',', $range ) [1] ) 
				];
			default :
				return [ 
						'data-rule-minlength' => $parameters [0],
						'minlength' => $parameters [0],
						'data-rule-maxlength' => $parameters [1],
						'maxlength' => $parameters [1] 
				];
		}
	}
	public function Unique($parameters, $attribute, $type) {
		list ( $table, $column, $except, $idColumn ) = $parameters;
		$route = \Config::get ( 'jsvalidation.route' );
		
		return [ 
				'data-rule-remote' => url ( '/' . $route ) . '?name=' . $attribute . '&rule=unique&' . http_build_query ( compact ( 'table', 'column', 'except', 'idColumn' ) ) 
		];
	}
	public function Exists($parameters, $attribute) {
		list ( $table, $column ) = $parameters;
		$route = \Config::get ( 'llama.form.route' );
		
		return [ 
				'data-rule-remote' => url ( '/' . $route ) . '?name=' . $attribute . '&rule=exists&' . http_build_query ( compact ( 'table', 'column' ) ) 
		];
	}
}
