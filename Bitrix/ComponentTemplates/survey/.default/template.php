<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

foreach ($arResult['SURVEY']['GROUPS'] as $group) {
    $groupName = $group['NAME'];
    break;
}

?>
<div class="modal-form-survey__head">
    <div class="bem-text bem-text_grey"><span data-type="currentGroupIndex">1</span>  из <?= count($arResult['SURVEY']['GROUPS']) ?> вопросов</div>
    <a href="javascript:void(0)" class="bem-modal__close modal-form-survey__close"></a>
</div>
<div class="modal-form-survey__body">
    <form id="surveyForm__js">
        <h2 class="h2" data-type="group-name"><?= $groupName; ?></h2>
        <div class="bem-form bem-form_text-above">
            <input name="csrf_token" type="hidden" data-type="csrf_token" value="<?= $arResult['CSRF_TOKEN'] ?>" />
            <?php
            $first = true;
            foreach ($arResult['SURVEY']['GROUPS'] as $groupId => $group):
            ?>
                <div data-type="group" data-group-id="<?= $groupId; ?>" <?= $first ? '' : 'style="display:none"'; ?>>
                    <?php
                    foreach ($group['QUESTIONS'] as $questId => $quest):
                        $quest = $arResult['SURVEY']['QUESTIONS'][$questId];
                    ?>
                    <div class="bem-form__left"><?= $quest['NAME'] ?></div>
                    <?php

                    if ($quest['TYPE'] == 'text') {?>
                        <label class="bem-form__row">
                            <div class="bem-form__right">
                                <input
                                        type="<?= $quest['TYPE'] ?>"
                                        name="<?= $quest['INPUT_NAME']; ?>"
                                        class="bem-input_text bem-input_text"
                                        required
                                >
                            </div>
                        </label>
                        <?php
                    } else {?>
                        <?php
                        foreach ($quest['VARS']  as $answerId => $answer) {?>
                        <label class="quiz__answer-label bem-<?= $quest['TYPE'] ?> bem-<?= $quest['TYPE'] ?>_fluid">
                            <input class="bem-<?= $quest['TYPE'] ?>__input" type="<?= $quest['TYPE'] ?>"
                                name="<?= $quest['INPUT_NAME']; ?>"
                                value="<?= $answerId; ?>"
                                required
                            />
                            <span class="bem-<?= $quest['TYPE'] ?>__fake"></span>
                            <div class="quiz__answer-box">
                                <span class="quiz__answer-text input-<?= $quest['TYPE'] ?>__text"><?= $answer ?></span>
                            </div>
                        </label>
                        <?php
                        }
                    }
                    endforeach;
                    ?>
                </div>
                <?php
                $first = false;
            endforeach;
            ?>

            <div class="bem-form__row">
                <div class="bem-form__right bem-container bem-form__error" style="display: none" data-type="error"></div>
            </div>
            <div class="bem-form__row">
                <div class="bem-form__right">
                    <button type="submit" class="bem-button bem-button_red bem-button_h50 modal-form-survey__button mr-19 surveyBtnNextSubmit__js">Продолжить</button>
                    <div class="modal-form-survey__wrap-link">
                        <a href="javascript:;" class="bem-pseudo modal-form-survey__link js-prev-group";">Вернуться назад</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        let survey = new JSSurveySwitcher(
            'surveyForm__js',
            '.surveyBtnNextSubmit__js',
            '.js-prev-group',
            <?= json_encode($arResult['SURVEY']['GROUPS']); ?>,
            <?= json_encode(array_keys($arResult['SURVEY']['GROUPS'])); ?>,
            <?= json_encode($component->GetName()); ?>,
            <?= json_encode($arResult['AJAX_REQUEST_HANDLER_NAME']); ?>
        );
        survey.init(<?= json_encode($arParams); ?>);
    });
</script>
