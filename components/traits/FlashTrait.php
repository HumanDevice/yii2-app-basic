<?php

namespace app\components\traits;

use Yii;

/**
 * Flash Messages.
 */
trait FlashTrait
{
    /**
     * Alias for warning().
     * @param string $message the flash message to be translated.
     */
    public function alert($message)
    {
        Yii::$app->session->addFlash('warning', $message);
    }

    /**
     * Adds flash message of 'danger' type.
     * @param string $message the flash message to be translated.
     */
    public function danger($message)
    {
        Yii::$app->session->addFlash('danger', $message);
    }

    /**
     * Alias for danger().
     * @param string $message the flash message to be translated.
     */
    public function err($message)
    {
        Yii::$app->session->addFlash('danger', $message);
    }
    
    /**
     * Alias for danger().
     * @param string $message the flash message to be translated.
     */
    public function error($message)
    {
        Yii::$app->session->addFlash('danger', $message);
    }

    /**
     * Adds flash message of given type.
     * @param string $type the type of flash message.
     * @param string $message the flash message to be translated.
     */
    public function goFlash($type, $message)
    {
        Yii::$app->session->addFlash($type, $message);
    }

    /**
     * Adds flash message of 'info' type.
     * @param string $message the flash message to be translated.
     */
    public function info($message)
    {
        Yii::$app->session->addFlash('info', $message);
    }

    /**
     * Alias for success().
     * @param string $message the flash message to be translated.
     */
    public function ok($message)
    {
        Yii::$app->session->addFlash('success', $message);
    }

    /**
     * Adds flash message of 'success' type.
     * @param string $message the flash message to be translated.
     */
    public function success($message)
    {
        Yii::$app->session->addFlash('success', $message);
    }

    /**
     * Alias for success().
     * @param string $message the flash message to be translated.
     */
    public function warn($message)
    {
        Yii::$app->session->addFlash('warning', $message);
    }
    
    /**
     * Adds flash message of 'warning' type.
     * @param string $message the flash message to be translated.
     */
    public function warning($message)
    {
        Yii::$app->session->addFlash('warning', $message);
    }
}
