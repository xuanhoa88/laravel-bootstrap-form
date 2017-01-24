<?php

namespace Llama\BootstrapForm\Converter\Base;

abstract class Message extends Container {
	/**
	 * Get user friendly validation message.
	 *
	 * @return string
	 */
	protected function trans($name, $rule, array $data = [], $type = null) {
		$path = $rule;
		if ($type !== null) {
			$path .= '.' . $type;
		}
		
		if (\Lang::has ( 'validation.custom.' . $name . '.' . $path )) {
			$path = 'custom.' . $name . '.' . $path;
		}
		
		return \Lang::get ( 'validation.' . $path, $data + [ 
				'attribute' => ! \Lang::has ( 'validation.attributes.' . $name ) ? $name : \Lang::get ( 'validation.attributes.' . $name ) 
		] );
	}
}
