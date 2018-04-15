<?php
namespace sablerom\vue\assets;

/**
 * Class VueAsset
 * @package sablerom\vue\assets
 */
class VueAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@bower/vue/dist';

    public function init() {
        parent::init();
        $this->prepareJs();
    }

    protected function prepareJs() {
        $this->js = ( \Yii::$app->vueManager->isDev )? [ 'vue.js' ] : [ 'vue.min.js' ];
    }

}
