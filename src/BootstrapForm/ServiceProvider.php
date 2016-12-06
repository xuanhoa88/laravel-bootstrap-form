<?php 
namespace Llama\BootstrapForm;

use Collective\Html\FormBuilder;
use AdamWathan\Form\FormBuilder;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFormBuilder();
        $this->registerBasicFormBuilder();
        $this->registerHorizontalFormBuilder();
        $this->registerBootForm();
    }

    protected function registerFormBuilder()
    {
        $this->app['adamwathan.form'] = $this->app->share(function ($app) {
            $formBuilder = clone $this->app['form'];
            $formBuilder->setErrorStore($app['adamwathan.form.errorstore']);
            $formBuilder->setOldInputProvider($app['adamwathan.form.oldinput']);
            $formBuilder->setToken($app['session.store']->getToken());

            return $formBuilder;
        });
    }

    protected function registerBasicFormBuilder()
    {
        $this->app['bootform.basic'] = $this->app->share(function ($app) {
            return new BasicFormBuilder($app['adamwathan.form']);
        });
    }

    protected function registerHorizontalFormBuilder()
    {
        $this->app['bootform.horizontal'] = $this->app->share(function ($app) {
            return new HorizontalFormBuilder($app['adamwathan.form']);
        });
    }

    protected function registerBootForm()
    {
        $this->app['bootform'] = $this->app->share(function ($app) {
            return new BootForm($app['bootform.basic'], $app['bootform.horizontal']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bootstrapform'];
    }
}
