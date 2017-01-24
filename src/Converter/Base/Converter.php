<?php

namespace Llama\BootstrapForm\Converter\Base;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Converter {
	/**
	 * Rule converter class instance.
	 *
	 * @var Rule
	 */
	protected $ruleTransformer;
	
	/**
	 * Message converter class instance.
	 *
	 * @var Message
	 */
	protected $messageTransformer;
	
	/**
	 * Rules which specify input type is numeric.
	 *
	 * @var array
	 */
	protected $rules = [ ];
	
	/**
	 * Rules which specify input type is numeric.
	 *
	 * @var array
	 */
	protected $numericRules = [ 
			'Integer',
			'Numeric',
			'Int',
			'Digits' 
	];
	
	/**
	 * Determine whether flag for auto generate validation message has enabled yet.
	 *
	 * @var bool
	 */
	protected $useBuiltinMessage = false;
	
	/**
	 * Gets rule
	 *
	 * @return Rule
	 */
	public function getRuleTransformer() {
		return $this->ruleTransformer;
	}
	
	/**
	 * Sets rule
	 *
	 * @param Rule $rule
	 * @return Converter
	 */
	public function setRuleTransformer(Rule $rule) {
		$this->ruleTransformer = $rule;
		
		return $this;
	}
	
	/**
	 * Gets message
	 *
	 * @return Message
	 */
	public function getMessageTransformer() {
		return $this->messageTransformer;
	}
	
	/**
	 * Sets message
	 *
	 * @param Message $message
	 * @return Converter
	 */
	public function setMessageTransformer(Message $message) {
		$this->messageTransformer = $message;
		
		return $this;
	}
	
	/**
	 * Set rules for validation.
	 *
	 * @param array $rules
	 */
	public function set(array $rules = []) {
		$this->rules = array_merge ( $this->rules, $rules );
	}
	
	/**
	 * Reset validation rules.
	 */
	public function reset() {
		$this->rules = [ ];
	}
	
	/**
	 * Determine whether flag for auto generate validation message has enabled yet.
	 *
	 * @param bool $enable
	 * @return Converter
	 */
	public function enableBuiltinMessage($enable = true) {
		$this->useBuiltinMessage = $enable;
		
		return $this;
	}
	
	/**
	 * Generate html attributes from validate rules
	 *
	 * @param string $name
	 * @return array
	 */
	public function make($name) {
		$name = $this->getAttribute ( $name );
		if (! ($rules = $this->getExplicitRules ( $name ))) {
			return [ ];
		}
		
		$rules = is_array ( $rules ) ? $rules : explode ( '|', $rules );
		$attrs = [ ];
		foreach ( $rules as $rule ) {
			list ( $rule, $parameters ) = $this->parseRule ( $rule );
			
			if ($rule == '') {
				continue;
			}
			
			if (in_array ( $rule, $this->numericRules )) {
				$type = 'numeric';
			} elseif (in_array ( $rule, [ 
					'Array' 
			] )) {
				$type = 'array';
			} else {
				$type = 'string';
			}
			
			$attrs += $this->getRuleTransformer ()->make ( $rule, [ 
					$parameters,
					$name,
					$type 
			] );
			
			if ($this->useBuiltinMessage) {
				$attrs += $this->getMessageTransformer ()->make ( $rule, [ 
						$parameters,
						$name,
						$type 
				] );
			}
		}
		
		return $attrs;
	}
	
	/**
	 * Get the explicit keys from an attribute flattened with dot notation.
	 *
	 * @param string $name
	 * @return array
	 */
	protected function getExplicitRules($name) {
		foreach ( $this->rules as $attribute => $rules ) {
			if (Str::contains ( $attribute, '*' )) {
				$pattern = str_replace ( '\*', '([^\.]+)', preg_quote ( $attribute, '/' ) );
				if (preg_match ( '/^' . $pattern . '/', $name )) {
					return $rules;
				}
			}
			
			if ($attribute === $name) {
				return $rules;
			}
		}
		
		return [ ];
	}
	
	/**
	 * Extract the rule name and parameters from a rule.
	 *
	 * @param array|string $rules
	 * @return array
	 */
	protected function parseRule($rules) {
		if (is_array ( $rules )) {
			$rules = $this->parseArrayRule ( $rules );
		} else {
			$rules = $this->parseStringRule ( $rules );
		}
		
		if (method_exists ( $this, 'normalizeRule' )) {
			$rules [0] = $this->normalizeRule ( $rules [0] );
		}
		
		return $rules;
	}
	
	/**
	 * Parse an array based rule.
	 *
	 * @param array $rules
	 * @return array
	 */
	protected function parseArrayRule(array $rules) {
		return [ 
				Str::studly ( trim ( Arr::get ( $rules, 0 ) ) ),
				array_slice ( $rules, 1 ) 
		];
	}
	
	/**
	 * Parse a string based rule.
	 *
	 * @param string $rules
	 * @return array
	 */
	protected function parseStringRule($rules) {
		$parameters = [ ];
		
		// The format for specifying validation rules and parameters follows an
		// easy {rule}:{parameters} formatting convention. For instance the
		// rule "Max:3" states that the value may only be three letters.
		if (strpos ( $rules, ':' ) !== false) {
			list ( $rules, $parameter ) = explode ( ':', $rules, 2 );
			
			$parameters = $this->parseParameters ( $rules, $parameter );
		}
		
		return [ 
				Str::studly ( trim ( $rules ) ),
				$parameters 
		];
	}
	
	/**
	 * Parse a parameter list.
	 *
	 * @param string $rule
	 * @param string $parameter
	 * @return array
	 */
	protected function parseParameters($rule, $parameter) {
		if (strtolower ( $rule ) == 'regex') {
			return [ 
					$parameter 
			];
		}
		
		return str_getcsv ( $parameter );
	}
	
	/**
	 * Get the raw attribute name without array braces.
	 *
	 * @return string
	 */
	protected function getAttribute($name) {
		// Bail early if no array notation detected
		if (! strstr ( $name, '[' )) {
			return $name;
		}
		// Strip array notation
		if ('[]' == substr ( $name, - 2 )) {
			$name = substr ( $name, 0, strlen ( $name ) - 2 );
		}
		
		$name = str_replace ( '][', '.', $name );
		$name = str_replace ( [ 
				']',
				'[' 
		], '.', $name );
		
		return trim ( $name, '.' );
	}
}
