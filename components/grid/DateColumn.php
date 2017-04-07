<?php

namespace app\components\grid;

use app\components\widgets\daterangepicker\DateRangePicker;
use DateTime;
use DateTimeZone;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * DateColumn.
 */
class DateColumn extends DataColumn
{
    /**
     * @inheritdoc
     */
    public $format = 'relativetime';
    
    /**
     * @var string Tooltip format.
     * Set boolean false to disable tooltip.
     */
    public $tooltip = 'datetime';
    
    /**
     * @var bool Whether to allow filtering by date and hours or just date.
     */
    public $filterTime = false;
    
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = parent::renderDataCellContent($model, $key, $index);
        $timestamp = $this->getDataCellValue($model, $key, $index);
        if ($this->tooltip && is_numeric($timestamp)) {
            $value = Html::tag('span', $value, [
                'data-toggle' => 'tooltip',
                'title' => $this->grid->formatter->format($timestamp, $this->tooltip),
            ]);
        }
        return $value;
    }
    
    /**
     * @inheritdoc
     */
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }
            if (is_array($this->filter)) {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, $this->filter, $options) . $error;
            }
            return DateRangePicker::widget([
                'model'     => $model,
                'attribute' => $this->attribute,
                'options'   => array_merge(
                    ['addon' => false],
                    $this->filterInputOptions
                ),
                'jsOptions' => [
                    'opens'            => 'left',
                    'timePicker'       => $this->filterTime,
                    'timePicker24Hour' => true,
                    'locale'           => [
                        'format' => 'yyyy/MM/dd' . ($this->filterTime ? ' HH:mm' : ''),
                    ],
                    'showDropdowns'    => true,
                    'autoUpdateInput'  => false,
                ],
            ]) . $error;
        }
        return parent::renderFilterCellContent();
    }
    
    /**
     * Return timestamp for start of dates range.
     * @param string $dates
     * @return integer
     */
    public static function rangeStart($dates = null)
    {
        return static::range($dates);
    }
    
    /**
     * Return timestamp for end of dates range.
     * @param string $dates
     * @return integer
     */
    public static function rangeEnd($dates = null)
    {
        return static::range($dates, 'end');
    }
    
    /**
     * Return timestamp from the dates range.
     * @param string $dates
     * @param string $which start or end
     * @return integer|null
     */
    public static function range($dates = null, $which = 'start')
    {
        if ($dates) {
            if (preg_match_all('/([1-2][0-9]{3}\/[0-1][0-9]\/[0-3][0-9] [0-2][0-9]:[0-5][0-9])/', $dates, $matches)) {
                if ($which === 'start' && isset($matches[1][0])) {
                    return (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y/m/d H:i:s', $matches[1][0] . ':00', new DateTimeZone(Yii::$app->user->identity->timezone))
                    );
                }
                if ($which === 'end' && isset($matches[1][1])) {
                    return (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y/m/d H:i:s', $matches[1][1] . ':59', new DateTimeZone(Yii::$app->user->identity->timezone))
                    );
                }
            } elseif (preg_match_all('/([1-2][0-9]{3}\/[0-1][0-9]\/[0-3][0-9])/', $dates, $matches)) {
                if ($which === 'start' && isset($matches[1][0])) {
                    return (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y/m/d H:i:s', $matches[1][0] . ' 00:00:00', new DateTimeZone(Yii::$app->user->identity->timezone))
                    );
                }
                if ($which === 'end' && isset($matches[1][1])) {
                    return (int)Yii::$app->formatter->asTimestamp(
                        DateTime::createFromFormat('Y/m/d H:i:s', $matches[1][1] . '23:59:59', new DateTimeZone(Yii::$app->user->identity->timezone))
                    );
                }
            }
        }
        return null;
    }
}
