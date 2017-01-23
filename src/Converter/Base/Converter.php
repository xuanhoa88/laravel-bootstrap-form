<?php

namespace Llama\BootstrapForm\Converter\Base;

use Llama\BootstrapForm\Helper;
use Llama\BootstrapForm\ConverterInterface;

class Converter implements ConverterInterface {
	/**
	 * Rule converter class instance.
	 *
	 * @var Rule
	 */
	protected $rule;
	
	/**
	 * Message converter class instance.
	 *
	 * @var Message
	 */
	protected $message;
	
	/**
	 * Route redirecter class instance.
	 *
	 * @var Route
	 */
	protected $route;
	
	/**
	 * Rules which specify input type is numeric.
	 *
	 * @var array
	 */
	protected $validateRules = [];
	
	/**
	 * Rules which specify input type is numeric.
	 *
	 * @var array
	 */
	protected $numericRules = [ 
			'integer',
			'numeric',
			'int',
			'digits'
	];
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->rule = new Rule ();
		$this->message = new Message ();
		$this->route = new Route ();
	}
	
	/**
	 * Get rule
	 * 
	 * @return Rule
	 */
	public function rule() {
		return $this->rule;
	}
	
	/**
	 * Gets message
	 * 
	 * @return Message
	 */
	public function message() {
		return $this->message;
	}
	
	/**
	 * Gets route
	 * 
	 * @return Route
	 */
	public function route() {
		return $this->route;
	}
	
	/**
	 * Set rules for validation.
	 *
	 * @param array $rules
	 *        	Laravel validation rules
	 */
	public function set(array $rules = []) {
		$this->validateRules = $rules;
	}
	
	/**
	 * Reset validation rules.
	 */
	public function reset() {
		$this->validateRules = [];
	}
	
	/**
	 * Determine whether rule already exists.
	 *
	 * @param string $rule
	 * @return bool
	 */
	public function has($rule) {
		return array_key_exists($rule, $this->validateRules);
	}
	
	/**
	 * Generate html attributes from validate rules
	 * 
	 * @param string $name
	 * @return array 
	 */
	public function make($name) {
		$name = $this->formatInputName ( $name );
		if (!$this->has ( $name )) {
			return [ ];
		}
		
		$rules =  is_array ( $this->validateRules[$name] ) ? $this->validateRules[$name] : explode ( '|', $this->validateRules[$name] );
		$type = $this->getTypeOfInput ( $rules );
		$atts = [ ];
		foreach ( $rules as $rule ) {
			$parser = $this->parseValidateRule ( $rule );
			$atts = $atts + $this->rule ()->convert ( $parser ['name'], [ 
					$parser,
					$name,
					$type 
			] );
			
			if (\Config::get ( 'jsvalidation.useMessages', true )) {
				$messageAttributes = $this->message ()->convert ( $parser ['name'], [ 
						$parser,
						$name,
						$type 
				] );
			}
				
			// if empty message attributes
			if (empty ( $messageAttributes )) {
				$messageAttributes = $this->getDefaultErrorMessage ( $parser ['name'], $name );
			}
			
			$atts = $atts + $messageAttributes;
		}
		
		return $atts;
	}
	
	/**
	 * Get all rules and return type of input if rule specifies type
	 * Now, just for numeric.
	 *
	 * @return string
	 */
	protected function getTypeOfInput(array $rules) {
		foreach ( $rules as $key => $rule ) {
			$parser = $this->parseValidateRule ( $rule );
			if (in_array ( $parser ['name'], $this->numericRules )) {
				return 'numeric';
			} 
			
			if ($parser ['name'] === 'array') {
				return 'array';
			}
		}
		
		return 'string';
	}
	
	/**
	 * Parses validition rule of laravel.
	 *
	 * @return array
	 */
	protected function parseValidateRule($rule) {
		$explodedRule = explode ( ':', $rule );
		return [
				'name' => array_shift ( $explodedRule ),
				'parameters' => explode ( ',', array_shift ( $explodedRule ) )
		];
	}
	
	/**
	 * Gets default error message.
	 *
	 * @return string
	 */
	protected function getDefaultErrorMessage($rule, $attribute) {
		return [ 
				'data-msg-' . $rule => Helper::getValidationMessage ( $attribute, $rule ) 
		];
	}
	
	/**
	 * Format recursive array like input names to laravel validation format
	 * Example name[en] will transform to name.en.
	 *
	 * @param string $name        	
	 *
	 * @return string
	 */
	protected function formatInputName($name) {
		preg_match_all ( '/\[(\s*[\w]*\s*)\]/', $name, $output, PREG_PATTERN_ORDER );
		
		if (! isset ( $output [1] )) {
			return $name;
		}
		
		$replaceWith = $output [1];
		$replace = $output [0];
		
		foreach ( $replaceWith as $key => $r ) {
			$replaceWith [$key] = '.' . $r;
		}
		
		return str_replace ( $replace, $replaceWith, $name );
	}
}
