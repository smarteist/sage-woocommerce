# Sage Woocommerce
![CI](https://travis-ci.org/smarteist/sage-woocommerce.svg?branch=master)

This package enables WooCommerce integration for Sage 10 themes and Blade templates.

## Installation

```
composer require hexbit/sage-woocommerce
```

## Usage
Add ```Hexbit\Woocommerce\Providers\WooCommerceServiceProvider``` to your providers array in ```yourtheme/config/app.php```
```php
...
    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [
        /**
         * Package Service Providers
         */
         Hexbit\Woocommerce\Providers\WooCommerceServiceProvider::class
    ]
...
```
Or you can discover service provider by using wp cli.
```
wp acorn package:discover
```
Then create ```app/woocommerce.php``` in your ```app``` folder and override default blade files in ```resources/views/woocommerce```, or you can create automatically by running this command 
```
wp acorn vendor:publish --tag="woocommerce"
```
& finally change view renderer in your index file like this:

```
<?php echo \Roots\view(\Roots\app('sage.woocommerce'), \Roots\app('sage.data'))->render(); ?>
```


Done! now you will be able to override woocommerce templates with blade TE in ```yourtheme/resources/views/woocommerce/``` directory.


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
