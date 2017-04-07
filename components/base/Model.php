<?php

namespace app\components\base;

use Yii;

/**
 * Model extended.
 */
class Model extends yii\base\Model
{
    /**
     * Short for load() with POST data.
     * @param string|null $formName form name to use to load the data into the model. If not set, formName() is used.
     * @return bool
     */
    public function loadPost($formName = null)
    {
        return $this->load(Yii::$app->request->post(), $formName);
    }

    /**
     * Short for load() with GET data.
     * @param string|null $formName form name to use to load the data into the model. If not set, formName() is used.
     * @return bool
     */
    public function loadGet($formName = null)
    {
        return $this->load(Yii::$app->request->get(), $formName);
    }
}
