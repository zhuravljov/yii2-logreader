Yii2 Log Reader
===============

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist zhuravljov/yii2-logreader "*"
```

or add

```
"zhuravljov/yii2-logreader": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    'bootstrap' => ['logreader'],
    'modules' => [
        'logreader' => [
            'class' => 'zhuravljov\yii\logreader\Module',
            'aliases' => [
                'Frontend Errors' => '@frontend/runtime/logs/app.log',
                'Backend Errors' => '@backend/runtime/logs/app.log',
                'Console Errors' => '@console/runtime/logs/app.log',
            ],
        ],
    ],
];
```

You can then access Log Reader using the following URL:

```
http://localhost/path/to/index.php?r=logreader
```

or if you have enabled pretty URLs, you may use the following URL:

```
http://localhost/path/to/logreader
```
