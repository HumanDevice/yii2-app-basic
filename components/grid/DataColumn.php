<?php

namespace app\components\grid;

use Yii;
use yii\helpers\Html;

/**
 * DataColumn extended.
 */
class DataColumn extends yii\grid\DataColumn
{
    /**
     * Adds sorter arrows.
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || ($this->label === null && $this->attribute === null)) {
            return parent::renderHeaderCellContent();
        }
        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }
        if ($this->attribute !== null && $this->enableSorting && ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
            $link = $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label]));
            $order = $sort->getAttributeOrder($this->attribute);
            $icon = $order 
                ? ' ' 
                    . Html::tag(
                        'span', 
                        Html::tag(
                            'span', 
                            '', 
                            ['class' => 'fa fa-arrow-circle-' . ($order == SORT_DESC ? 'down' : 'up')]
                        ), 
                        [
                            'class'       => 'text-' . ($order == SORT_DESC ? 'orange' : 'green'),
                            'data-toggle' => 'tooltip',
                            'data-title'  => $order == SORT_DESC 
                                                ? Yii::t('app', 'Descending order') 
                                                : Yii::t('app', 'Ascending order')
                        ]
                    )
                : '';
            return $link . $icon;
        }
        return $label;
    }
    
    /**
     * Returns column label.
     * @return string
     */
    protected function getColumnLabel()
    {
        if ($this->header !== null || ($this->label === null && $this->attribute === null)) {
            return parent::renderHeaderCellContent();
        }
        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            return Html::encode($label);
        }
        return $label;
    }
}
