<?php

namespace app\components\web;

/**
 * Default controller with flash trait.
 */
class Controller extends \yii\web\Controller
{
    use \app\components\traits\FlashTrait;
}
