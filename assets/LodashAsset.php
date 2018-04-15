<?php
namespace sablerom\vue\assets;

/**
 * Class LodashAsset
 * @package sablerom\vue\assets
 */
class LodashAsset extends \yii\web\AssetBundle {

    public $sourcePath = '@bower/lodash';

    public function init() {
        parent::init();
        $this->prepareJs();
    }

    protected function prepareJs() {
        $this->js = ( \Yii::$app->vueManager->isDev )? [ 'lodash.js' ] : [ 'lodash.min.js' ];
    }

}
