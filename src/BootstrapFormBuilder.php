<?php

namespace Llama\BootstrapForm;

use Collective\Html\FormBuilder;
use Llama\BootstrapForm\Converter\Base\Converter;

class BootstrapFormBuilder extends FormBuilder {
	/**
	 * Form Id
	 *
	 * @var int
	 */
	private static $frmId = 0;
	
	/**
	 * HTML tabindex Attribute
	 *
	 * @var int
	 */
	private static $tabIndex = 0;
	
	/**
	 *
	 * @var Converter $converter
	 */
	private $converter;
	
	/**
	 * Set binded converter class.
	 *
	 * @param Converter $converter        	
	 * @return BootstrapFormBuilder
	 */
	public function setConverter($converter) {
		$this->converter = $converter;
		
		return $this;
	}
	
	/**
	 * Set rules for validation.
	 *
	 * @param array $rules        	
	 * @return BootstrapFormBuilder
	 */
	public function setValidateRules(array $rules) {
		$this->getConverter ()->set ( $rules );
		
		return $this;
	}
	
	/**
	 * Get binded converter class.
	 *
	 * @return Converter
	 */
	public function getConverter() {
		return $this->converter;
	}
	
	/**
	 * Open up a new HTML form.
	 *
	 * @param array $options        	
	 * @return \Illuminate\Support\HtmlString
	 */
	public function open(array $options = []) {
		// Auto generate form name.
		if (! isset ( $options ['name'] )) {
			$options ['name'] = ++ static::$frmId;
		}
		
		// Make sure form always have attribute named 'id'.
		if (! isset ( $options ['id'] )) {
			$options ['id'] = $this->getId ( $options ['name'] );
		}
		
		// Add novalidate tag if HTML5.
		if (! isset ( $options ['novalidate'] )) {
			$options ['novalidate'] = 'novalidate';
		}
		
		// Set validate rules
		if (! empty ( $options ['rules'] )) {
			$this->setValidateRules ( $options ['rules'] );
			unset ( $options ['rules'] );
		}
		
		return parent::open ( $this->removeInvalidAttributes($options) );
	}
	
	/**
	 * Create a new model based form builder.
	 *
	 * @param array $model        	
	 * @param array $options        	
	 * @param array $rules        	
	 * @see Illuminate\Html\FormBuilder
	 */
	public function model($model, array $options = [], array $rules = []) {
		$this->setValidateRules ( $rules );
		
		return parent::model ( $model, $options );
	}
	
	/**
	 * Close the current form.
	 *
	 * @return string
	 */
	public function close() {
		static::$tabIndex = 0;
		
		// Reset validation rules.
		$this->getConverter ()->reset ();
		
		return parent::close ();
	}
	
	/**
	 * Open a new form group.
	 *
	 * @param string $name        	
	 * @param array $options        	
	 *
	 * @return string
	 */
	public function openGroup($name, array $options = []) {
		$options = $this->appendClassToOptions ( $options, 'form-group' );
		
		// Check to see if error blocks are enabled
		if ($this->hasErrors ( $name )) {
			// If the form element with the given name has any errors,
			// apply the 'has-error' class to the group.
			$options = $this->appendClassToOptions ( $options, 'has-error' );
		}
		
		$options = $this->removeInvalidAttributes($options);
		
		// If a label is given, we set it up here. Otherwise, we will just
		// set it to an empty string.
		$attributes = [ ];
		foreach ( $options as $key => $value ) {
			if (! in_array ( $key, [ 
					'error' 
			] )) {
				$attributes [$key] = $value;
			}
		}
		return '<div' . $this->html->attributes ( $attributes ) . '>';
	}
	
	/**
	 * Close out the last opened form group.
	 *
	 * @return string
	 */
	public function closeGroup() {
		return '</div>';
	}
	
	/**
	 * Create a form label element.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 * @param bool $escape_html        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function label($name, $value = null, $options = [], $escape_html = true) {
		$options = $this->removeInvalidAttributes($options);
		return parent::label ( $this->getId ( $name ), $value, $this->appendClassToOptions ( $options, 'control-label' ), $escape_html );
	}
	
	/**
	 * Create a form input field.
	 *
	 * @param string $type        	
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function input($type, $name, $value = null, $options = []) {
		if (! isset ( $options ['id'] )) {
			$options ['id'] = $this->getId ( $name );
		}
		
		// Add tabindex.
		$this->makeTabIndex ( $options );
		
		$options = $this->converter->make ( $name ) + $options;
		$options = $this->removeInvalidAttributes($options);
		
		return parent::input ( $type, $name, $value, $options );
	}
	
	/**
	 * Create a hidden input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function hidden($name, $value = null, $options = []) {
		$options ['tabindex'] = - 1;
		return $this->input ( 'hidden', $name, $value, $options );
	}
	
	/**
	 * Create a text input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function text($name, $value = null, $options = []) {
		return $this->input ( 'text', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a password input field.
	 *
	 * @param string $name        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function password($name, $options = []) {
		return $this->input ( 'password', $name, '', $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create an e-mail input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function email($name, $value = null, $options = []) {
		return $this->input ( 'email', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a tel input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function tel($name, $value = null, $options = []) {
		return $this->input ( 'tel', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a number input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function number($name, $value = null, $options = []) {
		return $this->input ( 'number', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a date input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function date($name, $value = null, $options = []) {
		return $this->input ( 'date', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a datetime input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function datetime($name, $value = null, $options = []) {
		return $this->input ( 'datetime', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a datetime-local input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function datetimeLocal($name, $value = null, $options = []) {
		return $this->input ( 'datetime-local', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a time input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function time($name, $value = null, $options = []) {
		return $this->input ( 'time', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a url input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function url($name, $value = null, $options = []) {
		return $this->input ( 'url', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a file input field.
	 *
	 * @param string $name        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function file($name, $options = []) {
		return $this->input ( 'file', $name, null, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a textarea input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function textarea($name, $value = null, $options = []) {
		if (! isset ( $options ['id'] )) {
			$options ['id'] = $this->getId ( $name );
		}
		
		// Add tabindex.
		$this->makeTabIndex ( $options );
		
		$options = $this->converter->make ( $name ) + $options;
		$options = $this->removeInvalidAttributes($options);
		
		return parent::textarea ( $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a select box field.
	 *
	 * @param string $name        	
	 * @param array $list        	
	 * @param string $selected        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function select($name, $list = [], $selected = null, $options = []) {
		if (! isset ( $options ['id'] )) {
			$options ['id'] = $this->getId ( $name );
		}
		
		// Add tabindex.
		$this->makeTabIndex ( $options );
		
		$options = $this->converter->make ( $name ) + $options;
		$options = $this->removeInvalidAttributes($options);
		
		return parent::select ( $name, $list, $selected, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a HTML reset input element.
	 *
	 * @param string $value        	
	 * @param array $attributes        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function reset($value, $attributes = []) {
		return $this->input ( 'reset', null, $value, $this->appendClassToOptions ( $attributes, 'form-control' ) );
	}
	
	/**
	 * Create a HTML image input element.
	 *
	 * @param string $url        	
	 * @param string $name        	
	 * @param array $attributes        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function image($url, $name = null, $attributes = []) {
		$attributes ['src'] = $this->url->asset ( $url );
		
		return $this->input ( 'image', $name, null, $this->appendClassToOptions ( $attributes, 'form-control' ) );
	}
	
	/**
	 * Create a color input field.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function color($name, $value = null, $options = []) {
		return $this->input ( 'color', $name, $value, $this->appendClassToOptions ( $options, 'form-control' ) );
	}
	
	/**
	 * Create a submit button element.
	 *
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function submit($value = null, $options = []) {
		if (! isset ( $options ['id'] )) {
			$options ['id'] = $this->getId ( $value );
		}
		
		$options = $this->removeInvalidAttributes($options);
		
		return parent::submit ( $value, $this->appendClassToOptions ( $options, 'btn' ) );
	}
	
	/**
	 * Create a button element.
	 *
	 * @param string $value        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function button($value = null, $options = []) {
		if (! isset ( $options ['id'] )) {
			$options ['id'] = $this->getId ( $value );
		}
		
		$options = $this->removeInvalidAttributes($options);
		
		return parent::button ( $value, $this->appendClassToOptions ( $options, 'btn' ) );
	}
	
	/**
	 * Create a help block.
	 *
	 * @param string $name        	
	 * @param array $options        	
	 * @return \Illuminate\Support\HtmlString
	 */
	public function help($name, array $options = []) {
		// Check to see if we are to include the formatted help block
		if ($label = $this->getFormattedErrors ( $name )) {
			$options = $this->appendClassToOptions ( $options, 'help-block' );
			$options = $this->removeInvalidAttributes($options);
			
			// Append the errors to the group and close it out.
			return $this->toHtmlString ( '<p' . $this->html->attributes ( $options ) . '>' . $label . '</p>' );
		}
		
		return '';
	}
	
	/**
	 * Create a checkable input field.
	 *
	 * @param string $type        	
	 * @param string $name        	
	 * @param mixed $value        	
	 * @param bool $checked        	
	 * @param array $options        	
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	protected function checkable($type, $name, $value, $checked, $options) {
		$options = $this->converter->make ( $name ) + $options;
		$options = $this->removeInvalidAttributes($options);
		
		return parent::checkable ( $type, $name, $value, $checked, $options );
	}
	
	/**
	 * Get element id
	 *
	 * @param string $name        	
	 * @return string
	 */
	private function getId($name) {
		// Bail early if no array notation detected
		if (! strstr ( $name, '[' )) {
			return $name;
		}
		// Strip array notation
		if ('[]' == substr ( $name, - 2 )) {
			$name = substr ( $name, 0, strlen ( $name ) - 2 );
		}
		
		$name = str_replace ( '][', '-', $name );
		$name = str_replace ( [ 
				']',
				'[' 
		], '-', $name );
		
		return trim ( $name, '-' );
	}
	
	/**
	 * Determine whether the form element with the given name
	 * has any validation errors.
	 *
	 * @param string $name        	
	 * @return bool
	 */
	private function hasErrors($name) {
		if (is_null ( $this->session ) || ! $this->session->has ( 'errors' )) {
			// If the session is not set, or the session doesn't contain
			// any errors, the form element does not have any errors
			// applied to it.
			return false;
		}
		
		// Get the errors from the session.
		$errors = $this->session->get ( 'errors' );
		// Check if the errors contain the form element with the given name.
		// This leverages Laravel's transformKey method to handle the
		// formatting of the form element's name.
		return $errors && $errors->has ( $this->transformKey ( $name ) );
	}
	
	/**
	 * Get the formatted errors for the form element with the given name.
	 *
	 * @param string $name        	
	 * @return string
	 */
	private function getFormattedErrors($name) {
		if (! $this->hasErrors ( $name )) {
			// If the form element does not have any errors, return
			// an emptry string.
			return '';
		}
		
		// Get the errors from the session.
		$errors = $this->session->get ( 'errors' );
		
		// Return the formatted error message, if the form element has any.
		return $errors ? $errors->first ( $this->transformKey ( $name ) ) : '';
	}
	
	/**
	 * Append the given class to the given options array.
	 *
	 * @param array $options        	
	 * @param string $extendHtmlClass        	
	 * @return array
	 */
	private function appendClassToOptions(array $options, $extendHtmlClass) {
		$htmlClass = [ ];
		if (array_key_exists ( 'class', $options )) {
			$htmlClass = array_map ( 'trim', explode ( ' ', $options ['class'] ) );
		}
		array_unshift ( $htmlClass, $extendHtmlClass );
		$options ['class'] = implode ( ' ', array_unique ( $htmlClass ) );
		
		return $options;
	}
	
	/**
	 * Make tabindex
	 *
	 * @param array $options        	
	 * @return void
	 */
	protected function makeTabIndex(array &$options) {
		if (! isset ( $options ['tabindex'] )) {
			if (! (isset ( $options ['disabled'] ) || isset ( $options ['readonly'] ) || in_array ( 'disabled', $options ) || in_array ( 'readonly', $options ))) {
				$options ['tabindex'] = 0;
			} else {
				$options ['tabindex'] = -1;
			}
		} else {
			if (0 <= ( int ) $options ['tabindex']) {
				$options ['tabindex'] = 0;
			}
		}
	}
	
	/**
	 * Remove invalid attributes
	 * 
	 * @param array $options
	 * @return array
	 */
	protected function removeInvalidAttributes(array $options)
	{
		return array_filter($options, function($v, $k) {
			if (trim($k) === '') {
				return (is_string($v) || is_numeric($v)) && !(trim($v) === '');
			}
			
			if (is_null($v)) {
				return false;
			}
			if (is_string($v) && trim($v) === '') {
				return false;
			} 
			if ((is_array($v) || $v instanceof Countable) && count($v) < 1) {
				return false;
			}
			return true;
		}, ARRAY_FILTER_USE_BOTH);
	}
}
