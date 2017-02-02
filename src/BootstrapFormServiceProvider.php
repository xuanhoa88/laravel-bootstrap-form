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
        $this->publishes([
            __DIR__.'/../config' => config_path('llama/form'),
        ], 'config');
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
    	$this->registerResources();
    	
        $this->app->singleton('form', function ($app) {
        	$converter = __NAMESPACE__ . '\\Converter\\' . \Config::get('llama.form.plugin') . '\\Converter';
            $form = new BootstrapFormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());
            $form->setConverter(new $converter());
            return $form->setSessionStore($app['session.store']);
        });
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $userConfigFile = app()->configPath() . '/llama/form.php';
        $packageConfigFile = __DIR__.'/../config/config.php';
        $config = $this->app['files']->getRequire($packageConfigFile);

        if (file_exists($userConfigFile)) {
            $userConfig = $this->app['files']->getRequire($userConfigFile);
            $config = array_replace_recursive($config, $userConfig);
        }

        $this->app['config']->set('llama.form', $config);
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
