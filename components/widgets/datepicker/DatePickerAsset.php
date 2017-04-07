<?php

namespace app\components\widgets\datepicker;

/**
 * DatePicker asset.
 */
class DatePickerAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/datepicker/dist/';
    public $js = ['js/bootstrap-datepicker.min.js'];
    public $css = ['css/datepicker3.min.css'];
    public $depends = [\yii\web\JqueryAsset::class];
}
