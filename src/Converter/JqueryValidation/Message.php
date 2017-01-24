<?php

namespace Llama\BootstrapForm\Converter\JqueryValidation;

use Llama\BootstrapForm\Converter\Base\Message as BaseMessage;

class Message extends BaseMessage {
	public function Required($parameters, $name) {
		return [ 
				'data-msg-required' => $this->trans ( $name, 'required' ) 
		];
	}
	public function Email($parameters, $name) {
		return [ 
				'data-msg-email' => $this->trans ( $name, 'email' ) 
		];
	}
	public function Url($parameters, $name) {
		return [ 
				'data-msg-url' => $this->trans ( $name, 'url' ) 
		];
	}
	public function Int($parameters, $name) {
		return [ 
				'data-msg-digits' => $this->trans ( $name, 'integer' ) 
		];
	}
	public function Numeric($parameters, $name) {
		return [ 
				'data-msg-number' => $this->trans ( $name, 'numeric' ) 
		];
	}
	public function Ip($parameters, $name) {
		return [ 
				'data-msg-ipv4' => $this->trans ( $name, 'ip' ) 
		];
	}
	public function Same($parameters, $name) {
		return [ 
				'data-msg-equalto' => $this->trans ( $name, 'same', [ 
						'attribute' => $name 
				] ) 
		];
	}
	public function Regex($parameters, $name) {
		return [ 
				'data-msg-regex' => $this->trans ( $name, 'regex' ) 
		];
	}
	public function Alpha($parameters, $name) {
		return [ 
				'data-msg-regex' => $this->trans ( $name, 'alpha' ) 
		];
	}
	public function Alphanum($parameters, $name) {
		return [ 
				'data-msg-regex' => $this->trans ( $name, 'alphanum' ) 
		];
	}
	public function Image($parameters, $name) {
		return [ 
				'data-msg-accept' => $this->trans ( $name, 'image' ) 
		];
	}
	public function Date($parameters, $name) {
		return [ 
				'data-msg-date' => $this->trans ( $name, 'date' ) 
		];
	}
	public function Min($parameters, $name, $type) {
		$message = $this->trans ( $name, 'min', [ 
				'min' => $parameters [0] 
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
	public function Max($parameters, $name, $type) {
		$message = $this->trans ( $name, 'max', [ 
				'max' => $parameters [0] 
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
	public function Between($parameters, $name, $type) {
		$message = $this->trans ( $name, 'between', [ 
				'min' => $parameters [0],
				'max' => $parameters [1] 
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
	public function Unique($parameters, $name) {
		return [ 
				'data-msg-remote' => $this->trans ( $name, 'unique' ) 
		];
	}
	public function Exists($parameters, $name) {
		return [ 
				'data-msg-remote' => $this->trans ( $name, 'exists' ) 
		];
	}
}
