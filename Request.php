<?php

namespace filsh\yii2\mpjax;

class Request extends \yii\web\Request
{
    public $pjaxHeader = 'X-PJAX';
    
    public function getIsPjax()
    {
        return $this->getHeaders()->get(strtolower($this->pjaxHeader));
    }
}