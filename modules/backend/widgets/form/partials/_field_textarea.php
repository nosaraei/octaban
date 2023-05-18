<!-- Textarea -->
<textarea
    name="<?= $field->getName() ?>"
    id="<?= $field->getId() ?>"
    autocomplete="off"
    class="form-control field-textarea size-<?= $field->size ?>"
    placeholder="<?= e(trans($field->placeholder)) ?>"
    <?= $this->previewMode ? 'disabled' : '' ?>
    <?= $field->getAttributes() ?>
><?= e($field->value) ?></textarea>
