<?php

namespace Llama\BootstrapForm\Converter\JqueryValidation;

use Llama\BootstrapForm\Converter\Base\Converter as BaseConverter;

class Converter extends BaseConverter
{
    /**
     * The cache of rule.
     *
     * @var array
     */
    protected static $ruleCache = [];
	
	/**
	 * Constructor
	 */
    public function __construct()
    {
        $this->setMessageTransformer(new Message());
        $this->setRuleTransformer(new Rule());
    }

    /**
     * Normalizes a rule so that we can accept short types.
     *
     * @param  string  $rule
     * @return string
     */
    protected function normalizeRule($rule)
    {
        switch ($rule) {
            case 'Integer':
                return 'Int';
            case 'Bool':
                return 'Boolean';
            default:
                return $rule;
        }
    }
}
