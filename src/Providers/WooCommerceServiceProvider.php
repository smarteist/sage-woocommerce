<?php

namespace Hexbit\Woocommerce\Providers;

use Hexbit\Woocommerce\WooCommerce;
use Roots\Acorn\Application;
use Roots\Acorn\ServiceProvider;


/**
 * Class WooCommerceServiceProvider
 * @package Hexbit\Woocommerce\Providers
 */
class WooCommerceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sage.woocommerce', WooCommerce::class);
        $this->app->singleton('sage.woocommerce.view', function (Application $app) {
            return strval($app['sage.woocommerce']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (defined('WC_ABSPATH')) {
            $this->app['sage.woocommerce']->loadThemeTemplateHooks();
            $this->bindSetupAction();
            $this->bindFilters();
        }

        // publishing command
        $this->publishes([
            dirname(__DIR__) . '/Publishes/app/woocommerce.php' => $this->app->path('woocommerce.php'),
            dirname(__DIR__) . '/Publishes/resources/views/woocommerce' => $this->app->resourcePath('views/woocommerce'),
        ], 'woocommerce');

    }

    /**
     * Appends all filters in wordpress actions
     * @hooks template_include
     * @hooks comments_template
     * @hooks woocommerce_locate_template
     * @hooks wc_get_template_part
     * @hooks wc_get_template
     */
    public function bindFilters()
    {
        $woocommerce = $this->app['sage.woocommerce'];

        add_filter('template_include', [$woocommerce, 'templateInclude'], 100, 1);
        add_filter('comments_template', [$woocommerce, 'templateInclude'], 100, 1);
        add_filter('woocommerce_locate_template', [$woocommerce, 'locateTemplate'], PHP_INT_MAX, 2);
        add_filter('wc_get_template_part', [$woocommerce, 'templatePart'], PHP_INT_MAX, 1);
        add_filter('wc_get_template', [$woocommerce, 'getTemplate'], 100, 3);

    }

    /**
     * WooCommerce support for theme
     */
    public function bindSetupAction()
    {
        add_action('after_setup_theme', [$this->app['sage.woocommerce'], 'addThemeSupport']);
    }
}
