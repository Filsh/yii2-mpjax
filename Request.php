<?php

namespace filsh\yii2\mpjax;

class Request extends \yii\web\Request
{
    public $pjaxHeader = 'X-PJAX';
    
    public $pjaxContainerHeader = 'X-PJAX-CONTAINER';
    
    protected $_containers;
    
    public function getIsPjax()
    {
        return $this->getHeaders()->get(strtolower($this->pjaxHeader));
    }
    
    public function getPjaxContainers()
    {
        if($this->_containers === null) {
            $this->_containers = [];
            foreach($this->getHeaders() as $hame => $value) {
                if(strpos($hame, strtolower($this->pjaxContainerHeader)) === 0) {
                    $index = substr($hame, strlen($this->pjaxContainerHeader) + 1);
                    $this->_containers[$index] = reset($value);
                }
            }
        }
        return $this->_containers;
    }
}