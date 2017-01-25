BootForms
===============
BootForms builds on top of my more general [Form](https://github.com/LaravelCollective/html) package by adding another layer of abstraction to rapidly generate markup for standard Bootstrap3 forms. Probably not perfect for your super custom branded ready-for-release apps, but a *huge* time saver when you are still in the prototyping stage!

- [Installation](#installing)
- [Feature Overview](#feature-overview)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Extending](#extending)
- [Plugins and Supported Rules](#plugins-and-supported-rules)
- [Licence](#licence)

### Feature Overview

- Multi-Plugin Support  *//For now, there is just one :)*
    - `Jquery Validation` 
- Extendible
- Laravel form builder based
- Validation rules can be set from controller
- Distinguishing between numeric input and string input
- User friendly input names
- Remote rules such as unique and exists

### Installing

You can install this package via Composer by running this command in your terminal in the root of your project:

```bash
composer require llama-laravel/bootstrap-form
```

If you are using Laravel 5, you can get started very quickly by registering the included service provider.

Modify the `providers` array in `config/app.php`:

```php
'providers' => [
    //...
    Llama\BootstrapForm\BootstrapFormServiceProvider::class
  ],
```

Add the `BootstrapForm` facade to the `aliases` array in `config/app.php`:

```php
'aliases' => [
    //...
    'Form' => Llama\BootstrapForm\BootstrapFormFacade::class
  ],
```

Also you need to publish configuration file and assets by running the following Artisan commands.

```php
$ php artisan vendor:publish --provider="Llama\BootstrapForm\BootstrapFormServiceProvider"
```

You can now start using BootstrapForm follow [here](https://laravelcollective.com/docs/5.3/html).

### Configuration

After publishing configuration file, you can find it in config/laravalid folder. Configuration parameters are as below:

| Parameter | Description | Values |
|-----------|-------------|--------|
| plugin | Choose plugin you want to use | See [Plugins and Supported Rules](#plugins-and-supported-rules) |
| useBuiltinMessage | If it is true, laravel validation messages are used in client side otherwise messages of chosen plugin are used  | true/false | 
| route | Route name for remote validation | Any route name (default: js-validation-remote) |

### Usage

```php
    $rules = ['name' => 'required|max:100', 'email' => 'required|email', 'birthdate' => 'date'];
    Form::open(['url' => 'foo/bar', 'method' => 'put', 'rules' => $rules]);
    Form::text('name');
    Form::text('email');
    Form::text('birthdate');
    Form::close(); // don't forget to close form, it reset validation rules
```
Also if you don't want to struggle with $rules at view files, you can set it in Controller or route with or without form name by using Form::setValidation($rules, $formName). If you don't give form name, this sets rules for first Form::open
```php    
    // in controller or route
    $rules = ['name' => 'required|max:100', 'email' => 'required|email', 'birthdate' => 'date'];
    Form::setValidateRules($rules);
    
    // in view
    Form::open(['url' => 'foo/bar', 'method' => 'put', 'name' => 'firstForm', 'rules' => $rules]);
    // some form inputs
    Form::close();
```
For rules which is related to input type in laravel (such as max, min), the package looks for other given rules to understand which type is input. If you give integer or numeric as rule with max, min rules, the package assume input is numeric and convert to data-rule-max instead of data-rule-maxlength.
```php
    $rules = ['age' => 'numeric|max'];
```
The converter assume input is string by default. File type is not supported yet.

**Validation Messages**

Converter uses validation messages of laravel (app/lang/en/validation.php) by default for client-side too. If you want to use jquery validation messages, you can set useLaravelMessages, false in config file of package which you copied to your config dir. 

#### Plugins

**Jquery Validation**
While using Jquery Validation as html/js validation plugin, you can include any [extension method](https://jqueryvalidation.org/extension-method/) in your views, too. After assets published, it will be copied to your public folder. The last thing you should do at client side is initializing jquery validation plugin as below:
```html
<script type="text/javascript">
$('form').validate({onkeyup: false}); //while using remote validation, remember to set onkeyup false
</script>
```

### Extending
There are two ways to extend package with your own rules. 
First, you can extend current converter plugin dynamically like below:
```php
Form::getConverter()->getRuleTransformer()->extend('someotherrule', function($parsedRule, $attribute, $type){
    // some code
    return ['data-rule-someotherrule' => 'blablabla'];
});
Form::getConverter()->getMessageTransformer()->extend('someotherrule', function($parsedRule, $attribute, $type){
    // some code
    return ['data-message-someotherrule' => 'Some other message'];
});
Form::getConverter()->getRouteTransformer()->extend('someotherrule', function($name, $parameters){
    // some code
    return ['valid' => false, 'messages' => 'Seriously dude, what kind of input is this?'];
});

```
Second, you can create your own converter (which extends baseconverter or any current plugin converter) in `Llama\BootstrapForm\Converter` namespace and change plugin configuration in config file with your own plugin name.

> **Note:** If you are creating a converter for some existed html/js plugin please create it in `converters` folder and send a pull-request.

### Plugins and Supported Rules

**Jquery Validation**
To use Jquery Validation, change plugin to `Llama\BootstrapForm\Converter\JqueryValidation\Converter` in config file.

| Rules          | Jquery Validation |
| ---------------|:----------------:|
| ---------------|:----------------:|
| Accepted  | - |
| Active URL  | - |
| After (Date)  | - |
| Alpha  | `+` |
| Alpha Dash  | - |
| Alpha Numeric  | - |
| Array  | - |
| Before (Date)  | - |
| Between  | `+` |
| Boolean  | - |
| Confirmed  | - |
| Date  | `+` |
| Date Format  | - |
| Different  | - |
| Digits  | - |
| Digits Between  | - |
| E-Mail  | `+` |
| Exists (Database)  | `+` |
| Image (File)  | - |
| In  | - |
| Integer  | - |
| IP Address  | `+` |
| Max  | `+` |
| MIME Types  | - |
| Min  | `+` |
| Not In  | - |
| Numeric  | `+` |
| Regular Expression  | `+` |
| Required  | `+` |
| Required If  | - |
| Required With  | - |
| Required With All  | - |
| Required Without  | - |
| Required Without All  | - |
| Same  | `+` |
| Size  | - |
| String  | - |
| Timezone  | - |
| Unique (Database)  | `+` |
| URL  | `+` |

> **Note:** It is easy to add some rules. Please check `Rule` class of related converter.

### Contribution
You can fork and contribute to development of the package. All pull requests is welcome.

**Convertion Logic**
Package converts rules by using converters (in src/converters). It uses Converter class of chosen plugin which extends BaseConverter/Converter class. 
You can look at existed methods and plugins to understand how it works. Explanation will be ready, soon.

### Additional Tips

#### Help Blocks

You can add a help block underneath a form element using the `helpBlock()` helper.

`Form::help('A strong password should be long and hard to guess.')`

> Note: This help block will automatically be overridden by errors if there are validation errors.

### License
Licensed under the MIT License
