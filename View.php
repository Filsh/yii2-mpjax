<?php

namespace filsh\yii2\mpjax;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use filsh\yii2\mpjax\MpjaxBlock;

class View extends \yii\web\View
{
    public $pjaxHeader = 'X-PJAX';
    
    public $pjaxContainerHeader = 'X-PJAX-CONTAINER';
    
    public $mpjaxBlocks = [];
    
    protected $_containers = [];
    
    public function init()
    {
        parent::init();
        
        $request = Yii::$app->getRequest();
        if($request->getIsPjax()) {
            foreach($request->getHeaders() as $hame => $value) {
                if(strpos($hame, strtolower($this->pjaxContainerHeader)) === 0) {
                    $index = substr($hame, strlen($this->pjaxContainerHeader) + 1);
                    $this->_containers[$index] = reset($value);
                }
            }
        }
    }
    
    public function beginMpjax($id, array $options = [])
    {
        $options['id'] = $id;
        return MpjaxBlock::begin([
            'id' => $id,
            'options' => $options,
            'view' => $this,
        ]);
    }
    
    public function endMpjax()
    {
        MpjaxBlock::end();
    }
    
    public function requiresPjaxContainer($id)
    {
        return in_array($id, $this->_containers);
    }
    
    public function afterRender($viewFile, $params, &$output)
    {
        $request = Yii::$app->getRequest();
        if($request->getIsPjax()) {
            $response = Yii::$app->getResponse();
            $response->clearOutputBuffers();
            $response->format = Response::FORMAT_JSON;
            $response->data = [
                'title' => Html::encode($this->title),
                'blocks' => $this->mpjaxBlocks
            ];
            $response->send();
            Yii::$app->end();
        } else {
            parent::afterRender($viewFile, $params, $output);
        }
    }
}