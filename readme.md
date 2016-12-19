BootForms
===============
BootForms builds on top of my more general [Form](https://github.com/LaravelCollective/html) package by adding another layer of abstraction to rapidly generate markup for standard Bootstrap 3 forms. Probably not perfect for your super custom branded ready-for-release apps, but a *huge* time saver when you are still in the prototyping stage!

- [Installation](#installing-with-composer)

## Installing with Composer

You can install this package via Composer by running this command in your terminal in the root of your project:

```bash
composer require llama-laravel/bootstrap-form
```

### Laravel

If you are using Laravel 5, you can get started very quickly by registering the included service provider.

Modify the `providers` array in `config/app.php`:

```php
'providers' => [
    //...
    Llama\BootstrapForm\ServiceProvider::class
  ],
```

Add the `BootstrapForm` facade to the `aliases` array in `config/app.php`:

```php
'aliases' => [
    //...
    'Form' => Llama\BootstrapForm\FormFacade::class
  ],
```

You can now start using BootstrapForm follow [here](https://laravelcollective.com/docs/5.3/html):

### Additional Tips

#### Help Blocks

You can add a help block underneath a form element using the `helpBlock()` helper.

`Form::help('A strong password should be long and hard to guess.')`

> Note: This help block will automatically be overridden by errors if there are validation errors.
