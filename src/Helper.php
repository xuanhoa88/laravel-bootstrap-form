<?php

namespace Llama\BootstrapForm;

/**
 * Helper class.
 */
class Helper
{
    /**
     * Get user friendly validation message.
     *
     * @return string
     */
    public static function getValidationMessage($attribute, $rule, $data = [], $type = null)
    {
        $path = $rule;
        if ($type !== null) {
            $path .= '.'.$type;
        }

        if (\Lang::has('validation.custom.'.$attribute.'.'.$path)) {
            $path = 'custom.'.$attribute.'.'.$path;
        }

        $niceName = !\Lang::has('validation.attributes.'.$attribute) ? $attribute : \Lang::get('validation.attributes.'.$attribute);

        return \Lang::get('validation.'.$path, $data + ['attribute' => $niceName]);
    }

    /**
     * Get the raw attribute name without array braces.
     *
     * @return string
     */
    public static function getFormAttribute($name)
    {
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
