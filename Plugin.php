<?php namespace GromIT\Catalog;

use Backend;
use Backend\Classes\Controller;
use System\Classes\PluginBase;

/**
 * Catalog Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Catalog',
            'description' => 'No description provided yet...',
            'author'      => 'GromIT',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Controller::extend(function (Controller $controller) {
            $controller->addCss('/plugins/gromit/catalog/assets/css/style.css');
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'GromIT\Catalog\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'gromit.catalog.some_permission' => [
                'tab' => 'Catalog',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'catalog' => [
                'label'       => 'Каталог',
                'url'         => Backend::url('gromit/catalog/products'),
                'icon'        => 'icon-leaf',
                'permissions' => ['gromit.catalog.*'],
                'order'       => 500,
                'sideMenu' => [
                    'categories' => [
                        'label' => 'Категории',
                        'url'   => Backend::url('gromit/catalog/categories'),
                        'iconSvg'  => '/plugins/gromit/catalog/assets/img/categories.svg',
                    ],
                    'products' => [
                        'label' => 'Товары',
                        'url'   => Backend::url('gromit/catalog/products'),
                        'iconSvg'  => '/plugins/gromit/catalog/assets/img/products.svg',
                    ],
                    'offers' => [
                        'label' => 'Предложения',
                        'url'   => Backend::url('gromit/catalog/offers'),
                        'iconSvg'  => '/plugins/gromit/catalog/assets/img/offers.svg',
                    ],
                    'groups' => [
                        'label' => 'Наборы характеристик',
                        'url'   => Backend::url('gromit/catalog/groups'),
                        'iconSvg'  => '/plugins/gromit/catalog/assets/img/groups.svg',
                    ],
                    'properties' => [
                        'label' => 'Характеристики',
                        'url'   => Backend::url('gromit/catalog/properties'),
                        'iconSvg'  => '/plugins/gromit/catalog/assets/img/properties.svg',
                    ],
                ],
            ],
        ];
    }
}
