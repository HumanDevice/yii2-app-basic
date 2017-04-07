<?php

namespace app\components\grid;

use Yii;
use yii\grid\ActionColumn as YiiActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * ActionColumn extended.
 *
 * @property array $icons
 */
class ActionColumn extends YiiActionColumn
{
    /**
     * @var bool Whether to use default glyphicons.
     */
    public $glyphicons = false;
    
    /**
     * @var array CSS classes used for actions.
     * Array will be merged with default icons.
     */
    public $iconClasses = [];
    
    /**
     * @inheritdoc
     */
    public $contentOptions = ['class' => 'text-right'];
    
    /**
     * @inheritdoc
     */
    public $headerOptions = ['class' => 'text-right'];
    
    /**
     * Returns list of icons' classes.
     * @return array
     */
    public function getIcons()
    {
        $default = $this->glyphicons
            ? [
                'view'   => 'glyphicon glyphicon-eye-open',
                'update' => 'glyphicon glyphicon-pencil',
                'delete' => 'glyphicon glyphicon-trash',
            ]
            : [
                'view'   => 'fa fa-eye',
                'update' => 'fa fa-pencil-square-o',
                'delete' => 'fa fa-trash-o text-danger',
            ];
        return ArrayHelper::merge($default, $this->iconClasses);
    }    
    
    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url) {
                $options = array_merge([
                    'title'       => Yii::t('yii', 'View'),
                    'aria-label'  => Yii::t('yii', 'View'),
                    'data-pjax'   => '0',
                    'data-toggle' => 'tooltip',
                ], $this->buttonOptions);
                return Html::a('<span class="' . $this->icons['view'] . '"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url) {
                $options = array_merge([
                    'title'       => Yii::t('yii', 'Update'),
                    'aria-label'  => Yii::t('yii', 'Update'),
                    'data-pjax'   => '0',
                    'data-toggle' => 'tooltip',
                ], $this->buttonOptions);
                return Html::a('<span class="' . $this->icons['update'] . '"></span>', $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url) {
                $options = array_merge([
                    'title'        => Yii::t('yii', 'Delete'),
                    'aria-label'   => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method'  => 'post',
                    'data-pjax'    => '0',
                    'data-toggle'  => 'tooltip',
                ], $this->buttonOptions);
                return Html::a('<span class="' . $this->icons['delete'] . '"></span>', $url, $options);
            };
        }
    }
}
