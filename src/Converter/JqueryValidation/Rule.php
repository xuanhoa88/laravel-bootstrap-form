<?php

namespace Llama\BootstrapForm\Converter\JqueryValidation;

use Llama\BootstrapForm\Helper;
use Llama\BootstrapForm\Converter\Base\Rule as BaseRule;

class Rule extends BaseRule {
	public function email($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-email' => 'true' 
		];
	}
	public function required($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-required' => 'true' 
		];
	}
	public function url($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-url' => 'true' 
		];
	}
	public function integer($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-digits' => 'true' 
		];
	}
	public function int($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-digits' => 'true' 
		];
	}
	public function numeric($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-number' => 'true' 
		];
	}
	public function ip($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-ipv4' => 'true' 
		];
	}
	public function same($parsedRule, $attribute, $type) {
		$value = vsprintf ( '*[name=%1s]', $parsedRule ['parameters'] );
		
		return [ 
				'data-rule-equalto' => $value 
		];
	}
	public function regex($parsedRule, $attribute, $type) {
		$rule = $parsedRule ['parameters'] [0];
		
		if (substr ( $rule, 0, 1 ) == substr ( $rule, - 1, 1 )) {
			$rule = substr ( $rule, 1, - 1 );
		}
		
		return [ 
				'data-rule-regex' => $rule 
		];
	}
	public function alpha($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-regex' => '^[A-Za-z _.-]+$' 
		];
	}
	public function alphanum($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-regex' => '^[A-Za-z0-9 _.-]+$' 
		];
	}
	public function image($parsedRule, $attribute, $type) {
		return [ 
				'accept' => 'image/*' 
		];
	}
	public function date($parsedRule, $attribute, $type) {
		return [ 
				'data-rule-date' => 'true' 
		];
	}
	public function min($parsedRule, $attribute, $type) {
		$minlength = vsprintf ( '%1s', $parsedRule ['parameters'] ) ;
		switch ($type) {
			case 'numeric' :
				return [ 
						'data-rule-min' => $minlength,
						'minlength' => mb_strlen(minlength)
				];
			default :
				return [ 
						'data-rule-minlength' => $minlength,
						'minlength' => minlength
				];
		}
	}
	
	public function max($parsedRule, $attribute, $type) {
		$maxlength = vsprintf ( '%1s', $parsedRule ['parameters'] );
		switch ($type) {
			case 'numeric' :
				return [ 
						'data-rule-max' => $maxlength,
						'maxlength' => mb_strlen($maxlength)
				];
			default :
				return [ 
						'data-rule-maxlength' => $maxlength,
						'maxlength' => $maxlength
				];
		}
	}
	
	public function between($parsedRule, $attribute, $type) {
		switch ($type) {
			case 'numeric' :
				$range = vsprintf ( '%1s,%2s', $parsedRule ['parameters'] );
				return [ 
						'data-rule-range' => $range,
						'minlength' => mb_strlen(explode(',', $range)[0]),
						'maxlength' => mb_strlen(explode(',', $range)[1])
				];
			default :
				return [ 
						'data-rule-minlength' => $parsedRule ['parameters'] [0],
						'minlength' => $parsedRule ['parameters'] [0],
						'data-rule-maxlength' => $parsedRule ['parameters'] [1],
						'maxlength' => $parsedRule ['parameters'] [1]
				];
		}
	}
	
	public function unique($parsedRule, $attribute, $type) {
		list ($table, $column, $except, $idColumn) = $parsedRule ['parameters'];
		$route = \Config::get ( 'jsvalidation.route' );
		
		return [ 
				'data-rule-remote' => url ( '/' . $route . '/unique' ) . '?' . http_build_query(compact('table', 'column', 'except', 'idColumn')) 
		];
	}
	
	public function exists($parsedRule, $attribute, $type) {
		list ($table, $column) = $parsedRule ['parameters'];
		$route = \Config::get ( 'jsvalidation.route' );
		
		return [ 
				'data-rule-remote' => url ( '/' . $route . '/exists' ) . '?' . http_build_query(compact('table', 'column')) 
		];
	}
}
