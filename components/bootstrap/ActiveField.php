<?php

namespace app\components\bootstrap;

use kartik\select2\Select2;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * ActiveField extended.
 */
class ActiveField extends yii\bootstrap\ActiveField
{
    /**
     * Adds feedback icon to the input field.
     * Usage example: feedback('glyphicon glyphicon-envelope') for glyphicon-envelope icon.
     * @param string $icon
     * @return static
     */
    public function feedback($icon = null)
    {
        if ($icon) {
            $class = empty($this->options['class']) ? [] : explode(' ', $this->options['class']);
            if (!in_array('has-feedback', $class, true)) {
                $class[] = 'has-feedback';
            }
            $this->options['class'] = implode(' ', $class);
            $iconClass = [$icon, 'form-control-feedback'];
            $this->inputTemplate = '{input}' . Html::tag('span', '', ['class' => implode(' ', $iconClass)]);
        }
        return $this;
    }
    
    /**
     * Renders a drop-down list as Select2 widget.
     * You can pass 'classic' => true to render classic dropdown.
     * @return static|string
     */
    public function dropDownList($items, $options = [])
    {
        $classic = ArrayHelper::remove($options, 'classic', false);
        if ($classic) {
            return parent::dropDownList($items, $options);
        }
        
        return $this->widget(Select2::class, array_merge([
            'theme' => Select2::THEME_DEFAULT, 
            'data' => $items,
        ], $options));
    }
    
    /**
     * Renders a password input that can be revealed.
     * This method will generate the "name" and "value" tag attributes automatically for the model attribute
     * unless they are explicitly specified in `$options`.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. The values will be HTML-encoded using [[Html::encode()]].
     * If you set a custom `id` for the input element, you may need to adjust the [[$selectors]] accordingly.
     * @return static
     */
    public function revealPasswordInput($options = [])
    {
        $options = array_merge($this->inputOptions, [
                'autocomplete' => 'new-password',
                'class' => 'form-control password-revealed'
            ], $options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::beginTag('div', ['class' => 'input-group']) 
                . Html::activePasswordInput($this->model, $this->attribute, $options)
                . Html::beginTag('span', ['class' => 'input-group-btn'])
                . Html::button(Html::tag('i', '', ['class' => 'fa fa-eye']), [
                    'class' => 'btn btn-default btn-flat reveal-password',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'right',
                    'title' => Yii::t('app', 'Reveal/hide password')
                ])
                . Html::endTag('span')
                . Html::endTag('div');

        $this->form->view->registerJs(<<<JS
jQuery("body").on("click", ".reveal-password", function() {
    var field = jQuery(this).parent().parent().find(".password-revealed");
    if (field.attr("type") === "password") {
        field.attr("type", "text");
        jQuery(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash");
    } else if (field.attr("type") === "text") {
        field.attr("type", "password");
        jQuery(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye");
    }
});
JS
);
        return $this;
    }
}
