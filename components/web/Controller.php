<?php

namespace app\components\web;

use app\components\traits\FlashTrait;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Default controller with flash trait.
 */
class Controller extends \yii\web\Controller
{
    use FlashTrait;

    /**
     * @var array|null AccessControl behavior configuration
     */
    public $accessBehavior = [];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        if ($this->accessBehavior !== null) {
            return [
                'access' => ArrayHelper::merge(['class' => AccessControl::class], $this->accessBehavior)
            ];
        }
        return [];
    }
}
