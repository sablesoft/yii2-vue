<?php
namespace sablerom\vue;

use Yii;
use yii\web\JsExpression;
use yii\web\View;
use yii\base\Widget;
use yii\helpers\Json;
use sablerom\vue\assets\VueAsset;
use sablerom\vue\assets\AxiosAsset;
use sablerom\vue\assets\LodashAsset;
use yii\base\InvalidConfigException;

/**
 * Class Vue
 * @package sablerom\vue
 *
 */
class Vue extends Widget {

    const INVALID_TYPE = 'Invalid vue js type';

    /** @var string $sourcePath */
    public $sourcePath;

    /** @var string $jsName */
    public $jsName = 'app';

    /** @var $type string - instance or component */
    public $type = 'instance';

    /** @var null|string[] $delimiters */
    public $delimiters;

    /**
     *
     * @var array|string
     */
    public $data;
    
    /** @var string|null $template */
    public $template;

    /** @var array|null */
    public $props;

    /** @var array|null */
    public $model;
    
    /**
     * @var array|string
     */
    public $methods;
    
    /**
     *
     * @var array|string
     */ 
    public $watch;
    
    /**
     *
     * @var array|string
     */
    public $computed;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $beforeCreate;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $created;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $beforeMount;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $mounted;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $beforeUpdate;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $updated;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $activated;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $deactivated;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $beforeDestroy;

    /**
     *
     * @var \yii\web\JsExpression|string
     */
    public $destroyed;

    /** @var array $components */
    public $components = [];

    /** @var string[] $_jsFields */
    protected static $_jsFields = [
        'data', 'delimiters', 'methods', 'watch', 'computed',
        'beforeCreate', 'created', 'beforeMount', 'mounted',
        'beforeUpdate', 'updated', 'beforeDestroy', 'destroyed',
        'activated', 'deactivated', 'props', 'model'
    ];

    /** @var string[] $types */
    protected $types = [ 'instance', 'component' ];

    /**
     * @throws InvalidConfigException
     */
    public function init() {
        parent::init();
        if( !in_array( $this->type, $this->types ) )
            throw new InvalidConfigException( self::INVALID_TYPE );
        $this->view->registerAssetBundle(VueAsset::class );
        $this->view->registerAssetBundle(AxiosAsset::class );
        $this->view->registerAssetBundle(LodashAsset::class );
        // prepare vue config from source path:
        $this->checkSource();
    }


    /**
     * @param $config
     */
    protected function checkSource() {
        if( empty( $this->sourcePath ) ) return;
        $path = \Yii::getAlias( rtrim( $this->sourcePath, '/' ) );
        if( file_exists( $path ) ) {
            // load js files:
            foreach( self::jsFields() as $field )
                if( $content = self::loadFile( "$path/$field.js" ) )
                    $this->$field = new JsExpression( $content );
            // load template file:
            if( $content = self::loadFile( "$path/template.html" ) )
                $this->template = $content;
        }
        // check components:
        $compPath = "$path/components";
        if( file_exists( $compPath ) )
            if( $resources = scandir( $compPath ) )
                foreach( $resources as $resource )
                    if( $this->isComponent( $compPath, $resource ) )
                        $this->components[ $resource ] = [
                            'sourcePath' => "$compPath/$resource"
                        ];
    }

    /**
     * @param $path
     * @param $check
     */
    protected function isComponent( $path, $check ) {
        return ( $check !== '.' && $check !== '..' && is_dir( "$path/$check" ) );
    }

    /**
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function run() {
        // register components first:
        $this->registerComponents();
        $js = $this->jsHead();
        // prepare js fields:
        foreach( $this::$_jsFields as $field ) {
            $content = $this->prepareJs( $this->$field );
            $js .= !empty( $content ) ? " $field : $content, " : null;
        }
        // prepare template field:
        if( $this->template )
            $js .= " template : '$this->template' ";
        $js .= "});";
        Yii::$app->view->registerJs( $js, View::POS_END );
    }

    /**
     * @return string[]
     */
    public static function jsFields() {
        return self::$_jsFields;
    }

    /**
     * @param $file
     * @return string|null
     */
    public static function loadFile($path ) {
        if( is_string( $path ) && is_file( $path ) )
            // try to load js file:
            try {
                return rtrim( file_get_contents( Yii::getAlias( $path ) ), ';' );
            } catch( \ErrorException $e ) {
                // todo - log warn
            }
        return null;
    }

    /**
     * @throws InvalidConfigException
     * @return string
     */
    protected function jsHead() {
        switch( $this->type ) {
            case 'instance':
                $js = "
                    var {$this->jsName} = new Vue({
                        el: '#{$this->id}',";
                break;
            case 'component':
                $js = "
                    Vue.component( '{$this->id}', {";
                break;
            default:
                throw new InvalidConfigException( self::INVALID_TYPE ); break;
        }
        return $js;
    }

    /**
     * @param mixed $config
     * @return null|string
     * @throws InvalidConfigException
     */
    protected function prepareJs( $config = null ) {
        if( empty( $config ) ) return null;
        $content = $config;
        if( $config instanceof JsExpression )
            $content = $content->expression;
        // check is file:
        if( is_string( $config ) )
            $content = self::loadFile( $config );
        if( is_array( $config ) )
            $content = Json::encode( $config );
        return $content;
    }

    /**
     * @throws \Exception
     */
    protected function registerComponents() {
        if( empty( $this->components ) ) return;
        foreach( (array) $this->components as $key => $component ) {
            if( !is_int( $key ) )
                $component['id'] = $key;
            $component['type'] = 'component';
            Vue::widget( $component );
        }

    }

}
