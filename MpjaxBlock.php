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
        
        $view = $this->getView();
        if($view->requiresPjaxContainer($this->options['id'])) {
            ob_start();
            ob_implicit_flush(false);
            $view->clear();
            $view->beginPage();
            $view->head();
            $view->beginBody();
        } else {
            echo Html::beginTag('div', $this->options);
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        if($view->requiresPjaxContainer($this->options['id'])) {
            $view->endBody();
            $view->cssFiles = null;
            $view->jsFiles = null;
            $view->endPage(true);

            $content = ob_get_clean();
            $view->mpjaxBlocks[$this->options['id']] = $content;
        } else {
            echo Html::endTag('div');
        }
    }
}