This extension provides the ability to have multiple rendering templates in one place for data models.


For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/vivre-tech/rest-data-renderer/v/stable.png)](https://packagist.org/packages/vivre-tech/rest-data-renderer)
[![Total Downloads](https://poser.pugx.org/vivre-tech/rest-data-renderer/downloads.png)](https://packagist.org/packages/vivre-tech/rest-data-renderer)
[![Build Status](https://travis-ci.org/vivre-tech/rest-data-renderer.svg?branch=master)](https://travis-ci.org/vivre-tech/rest-data-renderer)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vivre-tech/rest-data-renderer
```

or add

```json
"vivre-tech/rest-data-renderer": "*"
```

to the require section of your composer.json.


Usage
-----

Example:
```php
use vivretech\rest\renderer\DataRenderer;


class DummyModelRenderer extends DataRenderer
{

    /**
     * @param array $params
     * @return mixed
     */
    public function renderMain($params = [])
    {
        return [];
    }


    public function renderSummary($model)
    {
        return [
            'name' => $model['name'],
            'price' => $model['price'],
        ];
    }


    public function renderDetailed($model)
    {
        return [
            'id' => $model['id'],
            'name' => $model['name'],
            'price' => $model['price'],
            'created_at' => $model['created_at'],
        ];
    }

}


$render = new DummyModelRenderer();
$productModel = [
    'id' => 1,
    'name' => 'Product 1',
    'price' => 100,
    'created_at' => date('Y-m-d H:i:s')
];


/* Output JSON -> Dummy REST response. */
header("Content-Type: application/json;charset=utf-8");

echo
    json_encode([
        'productSummary' => $render->run('summary', [$productModel]),
        'productDetailed' => $render->run('Detailed', [$productModel]),
    ]);
```


Unit Testing
------
If you run the following command: `composer install` in a dev environment then you will find `phpunit` in `/vendor/bin/phpunit`.

In case `phpunit` in not installed via command `composer install`, just fallow next steps:
1. run in console/terminal `brew install phpunit`

To test, in the `root` of the project, base on how `phpunit` is installed you will have two choices to run:
1. installed via command `composer install` you will have to execute in console/terminal: `vendor/bin/phpunit`
2. installed via `bre` you will have to execute in console/terminal: `phpunit`
