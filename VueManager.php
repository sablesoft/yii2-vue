<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 12.04.18
 * Time: 18:39
 */

namespace sablerom\vue;

use yii\base\BaseObject;
use sablerom\vue\assets\VueAsset;

/**
 * Class VueManager
 *
 * @package sablerom\vue
 * @property \yii\web\View $view
 *
 */
class VueManager extends BaseObject {

    /** @var array $delimiters */
    public $delimiters;

    /** @var string $directivesPath */
    public $directivesPath;

    protected $mergeFields = [
      'delimiters'
    ];

    /** @var \yii\web\View $_view */
    protected $_view;

    public $isDev = false;

    /**
     * @param array $config
     */
    public function register( $config = [] ) {

        // register custom directives:
        $this->registerDirectives();
        $this->registerDirectives( $this->directivesPath );

        $this->prepareConfig( $config );
        try {
            Vue::widget( $config );
        } catch (\Exception $e ) {
            // todo
        }
    }

    public function registerDirectives( $path = null ) {

        $this->view->registerAssetBundle( VueAsset::class );

        if( !$path ) $path = __DIR__ . '/directives';
        $path = \Yii::getAlias( rtrim( $path, '/' ) );

        if( file_exists( $path ) )
            if( $resources = scandir( $path ) )
                foreach( $resources as $resource )
                    if( $this->isDirective( $path, $resource ) )
                        if( $js = Vue::loadFile( "$path/$resource" ) )
                            $this->view->registerJs( $js, View::POS_END );
    }

    /**
     * @return \yii\web\View
     */
    public function getView() {
        if ($this->_view === null) {
            $this->_view = \Yii::$app->getView();
        }

        return $this->_view;
    }

    /**
     * @param $config
     */
    protected function prepareConfig( &$config ) {
        foreach( $this->mergeFields as $field )
            if( !isset( $config[ $field ] ) && !is_null( $this->$field ) )
                $config[ $field ] = $this->$field;

    }

    /**
     * @param $path
     * @param $check
     */
    protected function isDirective( $path, $check ) {
        return ( $check !== '.' && $check !== '..' && is_dir( "$path/$check" ) );
    }


}