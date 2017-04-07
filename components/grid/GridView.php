<?php

namespace app\components\grid;

/**
 * GridView extended.
 */
class GridView extends \yii\grid\GridView
{
    /**
     * @inheritdoc
     */
    public $dataColumnClass = DataColumn::class;

    /**
     * @inheritdoc
     */
    public $tableOptions = ['class' => 'table table-hover table-striped'];
    
    /**
     * @inheritdoc
     */
    public $pager = [
        'options'       => ['class' => 'pagination pagination-sm no-margin pull-right'],
        'nextPageLabel' => '<span class="fa fa-arrow-right"></span>',
        'prevPageLabel' => '<span class="fa fa-arrow-left"></span>',
    ];
    
    /**
     * @inheritdoc
     */
    public function renderItems()
    {
        return "<div class=\"table-responsive no-padding\">\n" . parent::renderItems() . "\n</div>";
    }
    
    /**
     * @inheritdoc
     */
    public function renderPager()
    {
        return "<div class=\"clearfix\">\n" . parent::renderPager() . "\n</div>";
    }
}
