<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 12.04.18
 * Time: 18:39
 */

namespace sablerom\vue;

use yii\base\BaseObject;

/**
 * Class VueManager
 *
 * @package sablerom\vue
 *
 */
class VueManager extends BaseObject {

    public $delimiters;

    protected $mergeFields = [
      'delimiters'
    ];

    public $isDev = false;

    /**
     * @param array $config
     */
    public function register( $config = [] ) {
        $this->prepareConfig( $config );
        try {
            Vue::widget( $config );
        } catch (\Exception $e ) {
            // todo
        }
    }

    /**
     * @param $config
     */
    protected function prepareConfig( &$config ) {
        foreach( $this->mergeFields as $field )
            if( !isset( $config[ $field ] ) && !is_null( $this->$field ) )
                $config[ $field ] = $this->$field;

    }


}