<?php

namespace Llama\BootstrapForm\Converter\Base;

abstract class Container {
	protected $customMethods = [ ];
	public function convert($name, $parameters) {
		$methodName = strtolower ( $name );
		
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
	public function extend($name, $function) {
		$this->customMethods [$name] = $function;
	}
}
