<?php

namespace app\components\widgets\daterangepicker;

use IntlDateFormatter;
use Yii;
use yii\bootstrap\InputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Date Range Picker widget
 *
 * Special options key: addon - set false to not display addon icon.
 */
class DateRangePicker extends InputWidget
{
    /**
     * Date Range Picker options:
     * http://www.daterangepicker.com/#options
     * - startDate (Date object, moment object or string) 
     *      The start of the initially selected date range
     * - endDate: (Date object, moment object or string) 
     *      The end of the initially selected date range
     * - minDate: (Date object, moment object or string) 
     *      The earliest date a user may select
     * - maxDate: (Date object, moment object or string) 
     *      The latest date a user may select
     * - dateLimit: (object) 
     *      The maximum span between the selected start and end dates. Can have 
     *      any property you can add to a moment object (i.e. days, months)
     * - showDropdowns: (boolean) 
     *      Show year and month select boxes above calendars to jump to a 
     *      specific month and year
     * - showWeekNumbers: (boolean) 
     *      Show localized week numbers at the start of each week on the 
     *      calendars
     * - showISOWeekNumbers: (boolean) 
     *      Show ISO week numbers at the start of each week on the calendars
     * - timePicker: (boolean) 
     *      Allow selection of dates with times, not just dates
     * - timePickerIncrement: (number) 
     *      Increment of the minutes selection list for times (i.e. 30 to allow 
     *      only selection of times ending in 0 or 30)
     * - timePicker24Hour: (boolean) 
     *      Use 24-hour instead of 12-hour times, removing the AM/PM selection
     * - timePickerSeconds: (boolean) 
     *      Show seconds in the timePicker
     * - ranges: (object) 
     *      Set predefined date ranges the user can select from. Each key is the 
     *      label for the range, and its value an array with two dates 
     *      representing the bounds of the range
     * - opens: (string: 'left'/'right'/'center') Whether the picker appears 
     *      aligned to the left, to the right, or centered under the HTML 
     *      element it's attached to
     * - drops: (string: 'down' or 'up') 
     *      Whether the picker appears below (default) or above the HTML element 
     *      it's attached to
     * - buttonClasses: (array) 
     *      CSS class names that will be added to all buttons in the picker
     * - applyClass: (string) 
     *      CSS class string that will be added to the apply button
     * - cancelClass: (string) 
     *      CSS class string that will be added to the cancel button
     * - locale: (object) 
     *      Allows you to provide localized strings for buttons and labels, 
     *      customize the date display format, and change the first day of week 
     *      for the calendars
     * - singleDatePicker: (boolean) 
     *      Show only a single calendar to choose one date, instead of a range 
     *      picker with two calendars; the start and end dates provided to your 
     *      callback will be the same single date chosen
     * - autoApply: (boolean) 
     *      Hide the apply and cancel buttons, and automatically apply a new 
     *      date range as soon as two dates or a predefined range is selected
     * - linkedCalendars: (boolean) 
     *      When enabled, the two calendars displayed will always be for two 
     *      sequential months (i.e. January and February), and both will be 
     *      advanced when clicking the left or right arrows above the calendars. 
     *      When disabled, the two calendars can be individually advanced and 
     *      display any month/year.
     * - parentEl: (string) 
     *      jQuery selector of the parent element that the date range picker 
     *      will be added to, if not provided this will be 'body'
     * - isInvalidDate: (function) 
     *      A function that is passed each date in the two calendars before 
     *      they are displayed, and may return true or false to indicate whether 
     *      that date should be available for selection or not.
     * - isCustomDate: (function) 
     *      A function that is passed each date in the two calendars before they 
     *      are displayed, and may return a string or array of CSS class names 
     *      to apply to that date's calendar cell.
     * - autoUpdateInput: (boolean) 
     *      Indicates whether the date range picker should automatically update 
     *      the value of an <input> element it's attached to at initialization 
     *      and when the selected dates change.
     * - alwaysShowCalendars: (boolean) 
     *      Normally, if you use the ranges option to specify pre-defined date 
     *      ranges, calendars for choosing a custom date range are not shown 
     *      until the user clicks "Custom Range". When this option is set to 
     *      true, the calendars for choosing a custom date range are always 
     *      shown instead.
     * @var array 
     */
    public $jsOptions = [];
    
    /**
     * 
     * @var boolean
     */
    public $range = true;
    
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
     * Converts dates format from ICU to moment.
     * @return string
     */
    public function convertDates($format = null)
    {
        if ($format === null) {
            $format = Yii::$app->formatter->dateFormat;
        }
        if (strncmp($format, 'php:', 4) === 0) {
            $format = FormatConverter::convertDatePhpToIcu(substr($format, 4));
        }
        if (array_key_exists($format, self::$_icuShortFormats)) {
            $format = (new IntlDateFormatter(Yii::$app->language, self::$_icuShortFormats[$format], IntlDateFormatter::NONE))->getPattern();
        }
        return static::convertDateIcuToMoment($format);
    }
    
    /**
     * Converts few popular ICU date formats to moment.
     * @param string $pattern
     * @return string
     */
    public static function convertDateIcuToMoment($pattern)
    {
        return strtr(
            $pattern, 
            [
                "yyyy-MM-dd'T'HH:mm:ssZZZZZ" => 'YYYY-MM-DDTHH:mm:ssZZ', // 2014-05-14T13:55:01+02:00
                "yyyy-MM-dd"                 => 'YYYY-MM-DD',            // 2014-05-14
                "yyyy/MM/dd"                 => 'YYYY/MM/DD',            // 2014/05/14
                "yyyy.MM.dd"                 => 'YYYY.MM.DD',            // 2014.05.14
                "MM-dd-yyyy"                 => 'MM-DD-YYYY',            // 05-14-2014
                "dd.MM.yyyy, HH:mm"          => 'DD.MM.YYYY, HH:mm',     // 14.05.2014, 13:55
                "MM.dd.yyyy, HH:mm"          => 'MM.DD.YYYY, HH:mm',     // 05.14.2014, 13:55
                "dd.MM.yyyy, HH:mm:ss"       => 'DD.MM.YYYY, HH:mm:ss',  // 14.05.2014, 13:55:01
                "MM.dd.yyyy, HH:mm:ss"       => 'MM.DD.YYYY, HH:mm:ss',  // 05.14.2014, 13:55:01
                "dd.MM.yyyy"                 => 'DD.MM.YYYY',            // 14.05.2014
                "MM.dd.yyyy"                 => 'MM.DD.YYYY',            // 05.14.2014
                "dd/MM/yyyy"                 => 'DD/MM/YYYY',            // 14/05/2014
                "MM/dd/yyyy"                 => 'MM/DD/YYYY',            // 05/14/2014
                "dd/MM/yyyy HH:mm"           => 'DD/MM/YYYY HH:mm',      // 14/05/2014 13:55
                "MM/dd/yyyy HH:mm"           => 'MM/DD/YYYY HH:mm',      // 05/14/2014 13:55
                "yyyy/MM/dd HH:mm"           => 'YYYY/MM/DD HH:mm',      // 2014/05/14 13:55
                "EE, dd/MM/yyyy HH:mm"       => 'ddd, DD/MM/YYYY HH:mm', // Wed, 14/05/2014 13:55
                "EE, MM/dd/yyyy HH:mm"       => 'ddd, MM/DD/YYYY HH:mm', // Wed, 05/14/2014 13:55
                "dd-MM-yyyy"                 => 'DD-MM-YYYY',            // 14-05-2014
                "MM-dd-yyyy"                 => 'MM-DD-YYYY',            // 05-14-2014
                "dd-MM-yyyy HH:mm"           => 'DD-MM-YYYY HH:mm',      // 14-05-2014 13:55
                "MM-dd-yyyy HH:mm"           => 'MM-DD-YYYY HH:mm',      // 05-14-2014 13:55
                "dd-MM-yyyy HH:mm:ss"        => 'DD-MM-YYYY HH:mm:ss',   // 14-05-2014 13:55:01
                "MM-dd-yyyy HH:mm:ss"        => 'MM-DD-YYYY HH:mm:ss',   // 05-14-2014 13:55:01
                "MMMM dd, yyyy"              => 'MMMM DD, YYYY',         // May 14, 2014
                "MMM d, y"                   => 'MMM D, YYYY',           // May 14, 2014
                "yyyy-MM-dd HH:mm"           => 'YYYY-MM-DD HH:mm'
            ]
        );
    }
    
    /**
     * Registers scripts.
     */
    public function registerScripts()
    {
        $view = $this->getView();
        DateRangePickerAsset::register($view);
        $format = null;
        if (!empty($this->jsOptions['locale']['format'])) {
            $this->jsOptions['locale']['format'] = $this->convertDates($this->jsOptions['locale']['format']);
            $format = $this->jsOptions['locale']['format'];
        }
        if ($this->range) {
            $startDate = $format 
                ? "picker.startDate.format('$format')"
                : "picker.startDate";
            $endDate = $format 
                ? "picker.endDate.format('$format')"
                : "picker.endDate";
            $view->registerJs("jQuery('body').on('apply.daterangepicker', '[data-toggle=\"daterangepicker\"]', function(ev, picker) {
                jQuery(this).val($startDate + ' - ' + $endDate);
                jQuery(this).closest('.grid-view').yiiGridView('applyFilter');
            });");
            $view->registerJs("jQuery('body').on('cancel.daterangepicker', '[data-toggle=\"daterangepicker\"]', function() {
                jQuery(this).val('');
                jQuery(this).closest('.grid-view').yiiGridView('applyFilter');
            });");
        }
        $view->registerJs("jQuery('body').on('focus', '[data-toggle=\"daterangepicker\"]', function() {
            jQuery(this).daterangepicker(" . Json::encode($this->jsOptions) . ");
        });");
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
            ['data-toggle' => 'daterangepicker'],
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
