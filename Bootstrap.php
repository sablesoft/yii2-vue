<?php
/**
 * Created by PhpStorm.
 * User: roan
 * Date: 15.04.18
 * Time: 9:32
 */

namespace sablerom\vue;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package sablerom\vue
 *
 */
class Bootstrap implements BootstrapInterface {

    /**
     * @param Application $app
     * @throws \yii\base\InvalidConfigException
     */
    public function bootstrap( Application $app ) {
        if( !$app->has('vueManager') )
            $app->set( 'vueManager', [ 'class' => 'sablerom\vue\VueManager' ] );
    }

}