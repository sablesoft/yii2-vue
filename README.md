Yii2 Vue.js Manager
===================

This is the component for the yii2 app. This is the component for the application. It is a manager that allows you to conveniently manage settings and rendering of [Vue.js](https://vuejs.org/).

Installation
------------

The preferred way to install the **vueManager** is through [composer](http://getcomposer.org/download/).

Either run

```
composer require sablerom/yii2-vue
```

or add

```json
"sablerom/yii2-vue": "*",
```

to the require section of your composer.json.

Then add **vueManager** in your app config:

```php
    ...
    'components' => [
        ...
        'vueManager'   => [
            'class'      => 'sablerom\vue\VueManager',
            'delimiters' => [ '[[', ']]' ],  // specify custom for smarty
            'isDev'      => true              // false is default
        ],
        ...
    ],
```

## How to Use

Just use **vueManager**::**register** method in your controller before rendering:

```php
    Yii::$app->vueManager->register([

        'type' => 'instance',  // is default, use 'component' type
                               // to register vue component

        // html element id (for vue class instance):
        'id' => 'buy-number',

        // vue class instance var name:
        'jsName'  => 'buyNumber',

        // path to your vue app sources ( see details below ):
        'sourcePath'    => '@yourAlias/path/to/vueApp/sources',

        // your vue app reactive data:
        'data' => [
           'routes'  => $yourRoutes,
           'flags'   => $someCustomFlags,
           'model'   => $yourModel->getAttributes(),
           ...
        ],

        // use jsExpression wrapper for short js
        // for long js use sourcePath ( see below ):
        'created' =>
            new jsExpression( "function() { console.log('Vue created!')}" ),

        // also you can use string value as path to your js:
        'computed' => '@yourAlias/path/to/computed.js',

        // for vue 'component' type:
        'props' => [...],
        'template' => '<li><span>...</span></li>'

    ]);
```

## Vue Source Path

Use sorcePath for simple development and maintenance of complex vue apps and components. Just put all your js files for your vue app in one place. For example:

```
    -app\
        ...
        views\

            // your controller views:
            hello\

                // source path to vue - @app/views/hello/index
                index\

                    // use 'components' directory for required components:
                    components\

                        // subdirectory name - is component tag
                        compA\
                            props.js
                            template.html
                        compB\
                            props.js
                            template.html
                         ...
                    // your vue app or component fields:
                    computed.js
                    methods.js
                    created.js

                // your action view:
                index.tpl
```

## Source JS Syntax

JS files in your sourcePath must have a specific syntax. For js objects use this wrappers:

```js
    (function(){
        return {
            fieldA : 'fieldValue',
            fieldB : function(){...},
            ...
        }
    })();
```

For single functions as 'created', 'mounted' and similar you can use the usual syntax:

```js
    function created() {
        console.log('Vue app created!');
    }
```

And don't forget for vue chrome extension! :)