<?php 
namespace Llama\BootstrapForm;

use Illuminate\Support\ServiceProvider;
use Llama\BootstrapForm\Converter\Base\Converter;

class BootstrapFormServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerNamespaces();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFormBuilder();
        $this->app->alias('form', BootstrapFormBuilder::class);
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
        	$converter = $app['config']->get('llama.form.plugin');
            $form = new BootstrapFormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());
            $form->setConverter(new $converter());
            return $form->setSessionStore($app['session.store']);
        });
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../config/config.php';
        $this->mergeConfigFrom($configPath, 'llama.form');
        $this->publishes([
            $configPath => config_path('llama/form.php'),
        ], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['form', BootstrapFormBuilder::class];
    }
}
