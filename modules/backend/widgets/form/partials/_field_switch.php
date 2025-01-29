<?php
$previewMode = false;
if ($this->previewMode || $field->readOnly) {
    $previewMode = true;
}

$on = isset($field->config['on']) ? $field->config['on'] : 'backend::lang.form.field_on';
$off = isset($field->config['off']) ? $field->config['off'] : 'backend::lang.form.field_off';
?>

<input
    type="hidden"
    name="<?= $field->getName() ?>"
    value="0"
    <?= $previewMode ? 'disabled="disabled"' : '' ?>
>

<!-- Switch -->
<div class="field-switch">

    <label class="custom-switch" <?= $previewMode ? 'onclick="return false"' : '' ?>>
        <input
            type="checkbox"
            id="<?= $field->getId() ?>"
            name="<?= $field->getName() ?>"
            value="1"
            <?= $previewMode ? 'readonly="readonly"' : '' ?>
            <?= ($field->value == 1 || ($field->value === null && $field->defaults == true)) ? 'checked="checked"' : '' ?>
            <?= $field->getAttributes() ?>>
        <span><span><?= e(trans($on)) ?></span><span><?= e(trans($off)) ?></span></span>
        <a class="slide-button"></a>
    </label>

</div>



