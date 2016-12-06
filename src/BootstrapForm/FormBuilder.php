<?php
namespace Llama\BootstrapForm;

class FormBuilder extends \Collective\Html\FormBuilder
{
	/**
	 * Handle element html class
	 * 
	 * @param array $options
	 * @param string $extendHtmlClass
	 * @return array
	 */
	protected function handleHtmlClass(array $options, $extendHtmlClass)
	{
		$htmlClass = [];
		if (array_key_exists('class', $options)) {
			$htmlClass = array_map('trim', explode(' ', $options['class']));
		}
		array_unshift($htmlClass, $extendHtmlClass);
		$options['class'] = array_unique($htmlClass);
		
		return $options;
	}
	
	/**
	 * Create a form label element.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @param  bool   $escape_html
	 *
	 * @return \Illuminate\Support\HtmlString
	 */
	public function label($name, $value = null, $options = [], $escape_html = true)
	{
		return parent::label($name, $value, $this->handleHtmlClass($options, 'control-label'), $escape_html);
	}

    /**
     * Create a form input field.
     *
     * @param  string $type
     * @param  string $name
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function input($type, $name, $value = null, $options = [])
    {
        return parent::input($type, $name, $value, $this->handleHtmlClass($options, 'form-control'));
    }

    /**
     * Create a textarea input field.
     *
     * @param  string $name
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function textarea($name, $value = null, $options = [])
    {
        return parent::textarea($name, $value, $this->handleHtmlClass($options, 'form-control'));
    }

    /**
     * Create a select box field.
     *
     * @param  string $name
     * @param  array  $list
     * @param  string $selected
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function select($name, $list = [], $selected = null, $options = [])
    {
        return parent::select($name, $list, $selected, $this->handleHtmlClass($options, 'form-control'));
    }

    /**
     * Create a submit button element.
     *
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function submit($value = null, $options = [])
    {
        return parent::input('submit', null, $value, $this->handleHtmlClass($options, 'btn'));
    }

    /**
     * Create a button element.
     *
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function button($value = null, $options = [])
    {
        return parent::button($value, $this->handleHtmlClass($options, 'btn'));
    }
}
