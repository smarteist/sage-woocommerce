<?php

namespace Hexbit\Woocommerce;

use Roots\Acorn\Application;
use Roots\Acorn\Sage\ViewFinder;
use Roots\Acorn\View\FileViewFinder;
use Illuminate\Support\Str;
use WC_Template_Loader;
use function \Roots\view;

/**
 * Class WooCommerce
 * This class provides all the logic needed to make
 * WooCommerce compatible with Blade Templating Engine
 * @package Hexbit\Woocommerce
 */
class WooCommerce
{
    /**
     * @var null|string Determines whether or not we are
     * currently on a woocommerce page template.
     */
    private $woocommerceTemplate = null;
    /**
     * @var FileViewFinder
     */
    private $fileFinder;
    /**
     * @var ViewFinder
     */
    private $sageFinder;
    /**
     * @var Application
     */
    private $app;

    public function __construct(
        ViewFinder $sageFinder,
        FileViewFinder $fileFinder,
        Application $app
    )
    {
        $this->app = $app;
        $this->fileFinder = $fileFinder;
        $this->sageFinder = $sageFinder;
    }

    /**
     * This method allows this class to send the current view name to the blade,
     * based on the @return string
     * @see $woocommerceTemplate
     * @see \Roots\view
     */
    public function __toString()
    {
        if ($this->woocommerceTemplate && realpath($this->woocommerceTemplate)) {
            return $this->fileFinder->getPossibleViewNameFromPath($this->woocommerceTemplate);
        }
        return $this->app['sage.view'];
    }

    /**
     * Load template hook overrides file if available in app/ folder of theme.
     */
    public function loadThemeTemplateHooks()
    {
        locate_template('app/woocommerce.php', true, true);
    }

    /**
     * Declare theme support.
     */
    public function addThemeSupport()
    {
        add_theme_support('woocommerce');
    }

    /**
     * @hooked template_include
     * Support blade templates for the main template include.
     * @param string $template
     * @return string main page template directory
     */
    public function templateInclude(string $template)
    {
        if ($this->isWooCommerceTemplatePart($template)) {
            return $this->templatePart($template);
        }
        $woocommerceFound = WC_Template_Loader::template_loader($template);
        // determine if its a woocommerce page template
        if ($woocommerceFound != $template) {
            // save this path as a field
            $this->woocommerceTemplate = $this->locateThemeTemplate($woocommerceFound);
        }
        return $template;
    }


    /**
     * @hooked wc_get_template
     * Add blade templates for the woocommerce status page if user is admin and
     * loaded wc status page.
     * @param string $template template directory
     * @param string $templateName template file name
     * @param array $args Arguments. (default: array).
     * @return string wc template part
     */
    public function getTemplate(string $template, string $templateName, array $args)
    {
        // return theme filename for status screen
        if (is_admin() &&
            !wp_doing_ajax() &&
            get_current_screen() &&
            get_current_screen()->id === 'woocommerce_page_wc-status') {
            $themeTemplate = $this->locateThemeTemplate($templateName);
            return $themeTemplate ?: $template;
        }

        // return default template, output already rendered by "templatePart" method.
        return $template;
    }

    /**
     * @hooked woocommerce_locate_template
     * @hooked wc_get_template_part
     * Filter all template parts that available in overrides and return our blade
     * template loaders as needed.
     * @param string $template part to find
     * @return string loader output
     */
    public function templatePart(string $template)
    {
        // Locate any matching template within the theme.
        $themeTemplate = $this->locateThemeTemplate($template);

        // Include directly unless it's a blade file.
        if (
            $themeTemplate &&
            Str::endsWith($themeTemplate, '.blade.php') &&
            realpath($themeTemplate)
        ) {
            // Gather data to be passed to view
            $data = array_reduce(get_body_class(), function ($data, $class) use ($themeTemplate) {
                return apply_filters("sage/template/{$class}/data", $data, $themeTemplate);
            }, []);
            // We have a template, create a loader file and return it's path.
            return view(
                $this->fileFinder->getPossibleViewNameFromPath($themeTemplate),
                $data
            )->makeLoader();
        }

        return $template;
    }

    /**
     * Check if template is a WooCommerce template part.
     * @param string $template file directory
     * @return bool is file in woocommerce template directory or not.
     */
    protected function isWooCommerceTemplatePart(string $template)
    {
        return strpos($template, \WC_ABSPATH) !== false;
    }

    /**
     * Locate the theme's WooCommerce blade template when available.
     * @param string $template to search
     * @return string founded directory path
     */
    protected function locateThemeTemplate(string $template)
    {
        $themeTemplate = WC()->template_path() . str_replace(\WC_ABSPATH . 'templates/', '', $template);
        return locate_template($this->sageFinder->locate($themeTemplate));
    }
}
