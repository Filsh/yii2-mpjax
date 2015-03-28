<?php

namespace filsh\yii2\mpjax;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use filsh\yii2\mpjax\View;

class MpjaxBlock extends Widget
{
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if(!($this->getView() instanceof View)) {
            throw new \yii\base\InvalidConfigException('View must be instance of ' . View::className() . '.');
        }
        if(!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        
        if($this->getView()->requiresPjaxContainer($this->options['id'])) {
            ob_start();
            ob_implicit_flush(false);
        } else {
            echo Html::beginTag('div', $this->options);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if($this->getView()->requiresPjaxContainer($this->options['id'])) {
            $content = ob_get_clean();
            $this->getView()->mpjaxBlocks[$this->options['id']] = $content;
        } else {
            echo Html::endTag('div');
        }
    }
}