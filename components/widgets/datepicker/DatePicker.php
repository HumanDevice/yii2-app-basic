<?php

namespace app\components\widgets\datepicker;

use IntlDateFormatter;
use Yii;
use yii\bootstrap\InputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Date Picker widget
 *
 * Special options key: addon - set false to not display addon icon.
 */
class DatePicker extends InputWidget
{
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'form-control'];
    
    /**
     * https://bootstrap-datepicker.readthedocs.io/en/latest/
     * @var array
     */
    public $jsOptions = ['autoclose' => true];
    
    /**
     * ICU short formats.
     * @var array
     */
    private static $_icuShortFormats = [
        'short'  => 3, // IntlDateFormatter::SHORT,
        'medium' => 2, // IntlDateFormatter::MEDIUM,
        'long'   => 1, // IntlDateFormatter::LONG,
        'full'   => 0, // IntlDateFormatter::FULL,
    ];
    
    /**
     * Converts dates format from ICU.
     * @return array
     */
    public function convertDates()
    {
        $format = isset($this->jsOptions['format'])
                    ? $this->jsOptions['format']
                    : Yii::$app->formatter->dateFormat;
        if (strncmp($format, 'php:', 4) === 0) {
            $format = FormatConverter::convertDatePhpToIcu(substr($format, 4));
        }
        if (array_key_exists($format, self::$_icuShortFormats)) {
            $format = (new IntlDateFormatter(Yii::$app->language, self::$_icuShortFormats[$format], IntlDateFormatter::NONE))->getPattern();
        }
        $this->jsOptions['format'] = static::convertDateFromIcu($format);
        return $this->jsOptions;
    }
    
    /**
     * Converts ICU date format.
     * @param string $pattern
     * @return string
     */
    public static function convertDateFromIcu($pattern)
    {
        return strtr(
            $pattern, 
            [
                'dd'   => 'dd',
                'd'    => 'd',
                'eeee' => 'DD',
                'eee'  => 'D',
                'MMMM' => 'MM',
                'MMM'  => 'M',
                'MM'   => 'mm',
                'M'    => 'm',
                'yyyy' => 'yyyy',
                'yy'   => 'yy',
            ]
        );
    }
    
    /**
     * Registers scripts.
     */
    public function registerScripts()
    {
        $view = $this->getView();
        DatePickerAsset::register($view);
        $jsOptions = Json::encode($this->convertDates());
        $view->registerJs("jQuery('[data-toggle=\"datepicker\"]').datepicker($jsOptions);");
    }
    
    /**
     * Renders widget.
     * @return string
     */
    public function run()
    {
        $this->registerScripts();
        $input = '';
        $addon = ArrayHelper::remove($this->options, 'addon');
        if ($addon !== false) {
            $input .= Html::beginTag('div', ['class' => 'input-group']);
            $input .= Html::beginTag('div', ['class' => 'input-group-addon']);
            $input .= Html::tag('i', '', ['class' => 'fa fa-calendar']);
            $input .= Html::endTag('div');
        }
        $options = array_merge(
            ['data-toggle' => 'datepicker'],
            $this->options
        );
        if ($this->hasModel()) {
            $input .= Html::activeTextInput($this->model, $this->attribute, $options);
        } else {
            $input .= Html::textInput($this->name, $this->value, $options);
        }
        if ($addon !== false) {
            $input .= Html::endTag('div');
        }
        return $input;
    }
}
