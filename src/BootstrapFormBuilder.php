<?php
namespace Llama\BootstrapForm;

use Collective\Html\FormBuilder;

class BootstrapFormBuilder extends FormBuilder
{
	/**
     * An array containing the currently opened form groups.
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * An array containing the options of the currently open form groups.
     *
     * @var array
     */
    protected $groupOptions = [];
    
    /**
     * Open a new form group.
     *
     * @param  string $name
     * @param  mixed  $label
     * @param  array  $options
     * @param  array  $labelOptions
     *
     * @return string
     */
    public function openGroup($name, $label = null, $options = [], $labelOptions = []) {
        $options = $this->appendClassToOptions('form-group', $options);

        // Append the name of the group to the groupStack.
        $this->groupStack[] = $name;
        $this->groupOptions[] = $options;

        // Check to see if error blocks are enabled
        if ($this->errorBlockEnabled($options)) {
            if ($this->hasErrors($name)) {
                // If the form element with the given name has any errors,
                // apply the 'has-error' class to the group.
                $options = $this->appendClassToOptions('has-error', $options);
            }
        }

        // If a label is given, we set it up here. Otherwise, we will just
        // set it to an empty string.
        $label = $label ? $this->label($name, $label, $labelOptions) : '';
        $attributes = [];
        foreach ($options as $key => $value) {
            if (!in_array($key, ['error'])) {
                $attributes[$key] = $value;
            }
        }
        return '<div' . $this->html->attributes($attributes) . '>' . $label;
    }
    
    /**
     * Close out the last opened form group.
     *
     * @return string
     */
    public function closeGroup()
    {
        // Get the last added name from the groupStack and
        // remove it from the array.
        $name = array_pop($this->groupStack);

        // Get the last added options to the groupOptions
        // This way we can check if error blocks were enabled
        $options = array_pop($this->groupOptions);

        // Check to see if we are to include the formatted help block
        if ($this->errorBlockEnabled($options)) {
            // Get the formatted errors for this form group.
            $errors = $this->getFormattedErrors($name);
        }

        // Append the errors to the group and close it out.
        return $errors . '</div>';
    }

    /**
     * Determine whether the form element with the given name
     * has any validation errors.
     *
     * @param  string $name
     * @return bool
     */
    private function hasErrors($name)
    {
        if (is_null($this->session) || !$this->session->has('errors')) {
            // If the session is not set, or the session doesn't contain
            // any errors, the form element does not have any errors
            // applied to it.
            return false;
        }

        // Get the errors from the session.
        $errors = $this->session->get('errors');
        // Check if the errors contain the form element with the given name.
        // This leverages Laravel's transformKey method to handle the
        // formatting of the form element's name.
        return $errors && $errors->has($this->transformKey($name));
    }

    /**
     * Get the formatted errors for the form element with the given name.
     *
     * @param  string $name
     * @return string
     */
    private function getFormattedErrors($name)
    {
        if (!$this->hasErrors($name)) {
            // If the form element does not have any errors, return
            // an emptry string.
            return '';
        }
        // Get the errors from the session.
        $errors = $this->session->get('errors');
        // Return the formatted error message, if the form element has any.
        return $errors && $errors->first($this->transformKey($name),
            '<p class="help-block">:message</p>');
    }
    
    /**
     * Determine whether error block is enable.
     *
     * @param array $options
     * @return bool
     */
    private function errorBlockEnabled($options = [])
    {
        // Check to see if errorBlock key exists
        if (array_key_exists('error', $options)) {
            // Return the value from the array
            return $options['error'];
        }

        // Default to true if it does not exist
        return true;
    }
	
	/**
	 * Append the given class to the given options array.
	 * 
	 * @param array $options
	 * @param string $extendHtmlClass
	 * @return array
	 */
	protected function appendClassToOptions(array $options, $extendHtmlClass)
	{
		$htmlClass = [];
		if (array_key_exists('class', $options)) {
			$htmlClass = array_map('trim', explode(' ', $options['class']));
		}
		array_unshift($htmlClass, $extendHtmlClass);
		$options['class'] = implode(' ', array_unique($htmlClass));
		
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
		return parent::label($name, $value, $this->appendClassToOptions($options, 'control-label'), $escape_html);
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
        return parent::input($type, $name, $value, $this->appendClassToOptions($options, 'form-control'));
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
        return parent::textarea($name, $value, $this->appendClassToOptions($options, 'form-control'));
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
        return parent::select($name, $list, $selected, $this->appendClassToOptions($options, 'form-control'));
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
        return parent::input('submit', null, $value, $this->appendClassToOptions($options, 'btn'));
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
        return parent::button($value, $this->appendClassToOptions($options, 'btn'));
    }

    /**
     * Create a help block.
     *
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function help($value, array $options = [])
    {
    	$options = $this->appendClassToOptions($options, 'help-block');
        return $this->toHtmlString('<p' . $this->html->attributes($options) . '>' . $value . '</p>');
    }

    /**
     * Create a checkable input field.
     *
     * @param  string $type
     * @param  string $name
     * @param  mixed  $value
     * @param  bool   $checked
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function checkable($type, $name, $value, $checked, $options)
    {
        $checked = $this->getCheckedState($type, $name, $value, $checked);

        if ($checked) {
            $options['checked'] = 'checked';
        }

        return parent::input($type, $name, $value, $options);
    }
}
