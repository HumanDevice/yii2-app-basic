<?php

namespace app\components\bootstrap;

/**
 * ActiveForm extended.
 *
 * @method ActiveField field
 */
class ActiveForm extends \yii\bootstrap\ActiveForm
{
    /**
     * @var string the default field class name when calling field() to create a new field.
     */
    public $fieldClass = ActiveField::class;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->layout === 'horizontal') {
            $this->fieldConfig['horizontalCssClasses'] = [
                'offset' => 'col-md-offset-3',
                'label' => 'col-md-3',
                'wrapper' => 'col-md-6',
                'error' => '',
                'hint' => 'col-md-3',
            ];
        }
    }
}
