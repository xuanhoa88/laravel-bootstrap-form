<?php

namespace Llama\BootstrapForm\Converter\JqueryValidation;

use Llama\BootstrapForm\Converter\Base\Converter as BaseConverter;

class Converter extends BaseConverter
{
    public function __construct()
    {
        $this->rule = new Rule();
        $this->message = new Message();
        $this->route = new Route();
    }
}
