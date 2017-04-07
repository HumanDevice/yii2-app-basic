<?php

namespace app\components\widgets\daterangepicker;

/**
 * DateRangePicker asset.
 */
class DateRangePickerAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/daterangepicker/';
    public $js = [
        'moment.min.js',
        'daterangepicker.js',
    ];
    public $css = ['daterangepicker.css'];
    public $depends = [\yii\web\JqueryAsset::class];
}
