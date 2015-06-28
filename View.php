<?php

namespace filsh\yii2\mpjax;

use filsh\yii2\mpjax\MpjaxBlock;

class View extends \yii\web\View
{
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
}