<?php

namespace Llama\BootstrapForm\Converter\JqueryValidation;

use Llama\BootstrapForm\Helper;
use Lang;
use Llama\BootstrapForm\Converter\Base\Message as BaseMessage;

class Message extends BaseMessage {
	public function ip($parsedRule, $attribute, $type) {
		return [ 
				'data-msg-ipv4' => Helper::getValidationMessage ( $attribute, $parsedRule ['name'] ) 
		];
	}
	public function same($parsedRule, $attribute, $type) {
		return [ 
				'data-msg-equalto' => Lang::get ( 'validation.' . $parsedRule ['name'], [ 
						'attribute' => $attribute 
				] ) 
		];
	}
	public function alpha($parsedRule, $attribute, $type) {
		return [ 
				'data-msg-regex' => Helper::getValidationMessage ( $attribute, $parsedRule ['name'] ) 
		];
	}
	public function alphanum($parsedRule, $attribute, $type) {
		return [ 
				'data-msg-regex' => Helper::getValidationMessage ( $attribute, $parsedRule ['name'] ) 
		];
	}
	public function integer($parsedRule, $attribute, $type) {
		return [ 
				'data-msg-number' => Helper::getValidationMessage ( $attribute, $parsedRule ['name'] ) 
		];
	}
	public function numeric($parsedRule, $attribute, $type) {
		return [ 
				'data-msg-number' => Helper::getValidationMessage ( $attribute, $parsedRule ['name'] ) 
		];
	}
	public function max($parsedRule, $attribute, $type) {
		$message = Helper::getValidationMessage ( $attribute, $parsedRule ['name'], [ 
				'max' => $parsedRule ['parameters'] [0] 
		], $type );
		switch ($type) {
			case 'numeric' :
				return [ 
						'data-msg-max' => $message 
				];
			default :
				return [ 
						'data-msg-maxlength' => $message 
				];
		}
	}
	public function min($parsedRule, $attribute, $type) {
		$message = Helper::getValidationMessage ( $attribute, $parsedRule ['name'], [ 
				'min' => $parsedRule ['parameters'] [0] 
		], $type );
		switch ($type) {
			case 'numeric' :
				return [ 
						'data-msg-min' => $message 
				];
			default :
				return [ 
						'data-msg-minlength' => $message 
				];
		}
	}
	public function between($parsedRule, $attribute, $type) {
		$message = Helper::getValidationMessage ( $attribute, $parsedRule ['name'], [ 
				'min' => $parsedRule ['parameters'] [0],
				'max' => $parsedRule ['parameters'] [1] 
		], $type );
		switch ($type) {
			case 'numeric' :
				return [ 
						'data-msg-range' => $message 
				];
			default :
				return [ 
						'data-msg-minlength' => $message,
						'data-msg-maxlength' => $message 
				];
		}
	}
}
