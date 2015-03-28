<?php

namespace filsh\yii2\mpjax;

use Yii;
use yii\web\Response;
use filsh\yii2\mpjax\MpjaxBlock;

class View extends \yii\web\View
{
    public $pjaxHeader = 'X-PJAX';
    
    public $pjaxContainerHeader = 'X-PJAX-CONTAINER';
    
    public $mpjaxBlocks = [];
    
    protected $_containers;
    
    public function init()
    {
        parent::init();
        
        $headers = Yii::$app->getRequest()->getHeaders();
        foreach($headers as $hame => $value) {
            if(strpos($hame, strtolower($this->pjaxContainerHeader)) === 0) {
                $index = substr($hame, strlen($this->pjaxContainerHeader) + 1);
                $this->_containers[$index] = reset($value);
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
    
    /**
     * @return boolean whether the current request requires pjax response from this widget
     */
    public function requiresPjax($id)
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        if(!$headers->get(strtolower($this->pjaxHeader))) {
            return false;
        }
        return in_array($id, $this->_containers);
    }
    
    public function afterRender($viewFile, $params, &$output)
    {
        if(!empty($this->mpjaxBlocks)) {
            $response = Yii::$app->getResponse();
            $response->clearOutputBuffers();
            $response->setStatusCode(200);
            $response->format = Response::FORMAT_JSON;
            $response->data = $this->mpjaxBlocks;
            $response->send();
            
            Yii::$app->end();
        } else {
            parent::afterRender($viewFile, $params, $output);
        }
    }
}