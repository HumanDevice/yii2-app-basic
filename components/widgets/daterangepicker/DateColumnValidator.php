<?php

namespace app\components\widgets\daterangepicker;

use Yii;
use yii\validators\Validator;

/**
 * Date Column Validator.
 */
class DateColumnValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (!preg_match('/^[1-2][0-9]{3}\/[0-1][0-9]\/[0-3][0-9] ?\- ?[1-2][0-9]{3}\/[0-1][0-9]\/[0-3][0-9]$/', $model->$attribute)) {
            if (!preg_match('/^[1-2][0-9]{3}\/[0-1][0-9]\/[0-3][0-9] [0-2][0-9]:[0-5][0-9] ?\- ?[1-2][0-9]{3}\/[0-1][0-9]\/[0-3][0-9] [0-2][0-9]:[0-5][0-9]$/', $model->$attribute)) {
                $this->addError($model, $attribute, Yii::t('app', 'Wrong dates format.'));
            }
        }
    }
}
