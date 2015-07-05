<?php

namespace filsh\yii2\mpjax;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\Response;

class MpjaxBlock extends Widget
{
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    
    protected static $blocks = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if(!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        
        if($this->requiresPjaxContainer($this->options['id'])) {
            ob_start();
            ob_implicit_flush(false);
            
            $view = $this->getView();
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
        if($this->requiresPjaxContainer($this->options['id'])) {
            $view = $this->getView();
            $view->endBody();
            $view->cssFiles = null;
            $view->jsFiles = null;
            $view->endPage(true);

            $content = ob_get_clean();
            self::$blocks[$this->options['id']] = $content;
            
            $containers = Yii::$app->getRequest()->getPjaxContainers();
            if(count(self::$blocks) === count($containers)) {
                $response = Yii::$app->getResponse();
                $response->clearOutputBuffers();
                $response->format = Response::FORMAT_JSON;
                $response->data = [
                    'title' => Html::encode($view->title),
                    'blocks' => self::$blocks
                ];
                $response->send();
                Yii::$app->end();
            }
        } else {
            echo Html::endTag('div');
        }
    }
    
    public function requiresPjaxContainer($id)
    {
        if(($request = Yii::$app->getRequest()) && $request instanceof Request) {
            return $request->getIsPjax() && in_array($id, $request->getPjaxContainers());
        }
        return false;
    }
}