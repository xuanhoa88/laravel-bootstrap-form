<?php

namespace Llama\BootstrapForm\Converter\Base;

abstract class Container {
	/**
	 * @var array $customMethods
	 */
	protected $customMethods = [ ];
	
	/**
	 * @param string $methodName
	 * @param array $parameters
	 * @return	mixed
	 */
	public function make($methodName, array $parameters) {
		if (isset ( $this->customMethods [$methodName] )) {
			return call_user_func_array ( $this->customMethods [$methodName], $parameters );
		}
		
		if (method_exists ( $this, $methodName )) {
			return call_user_func_array ( [ 
					$this,
					$methodName 
			], $parameters );
		}
		
		return [ ];
	}
	
	/**
	 * Extends validation method
	 * 
	 * @param string $name
	 * @param \Closure $fn
	 * @return Container
	 */
	public function extend($name, \Closure $fn) {
		$this->customMethods [$name] = $fn;
		
		return $this;
	}
}
