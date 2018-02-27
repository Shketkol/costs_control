<?php

use frontend\models\CallHistoryCommentForm;
use frontend\models\SendToDeveloperForm;
use \frontend\models\UsersForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $formModel \frontend\models\CallTrackingSiteForm */
/* @var $this yii\web\View */

$sendToDeveloperFormModel = new SendToDeveloperForm();
$callHistoryCommentModel = new CallHistoryCommentForm();

// Register JS for this page
$js = <<<JS

    //modal modal_add_new_user select add
    
    $('#modal_add_new_user').on('shown.bs.modal', function () {
        $('select').selectpicker();
    });
    

    // Clear alerts
    
    // Send mail to developer (AJAX)
    $('#send-to-developer-form').on('submit', function() {
        var form = $(this).closest('form');
       
        $.ajax({
            method: 'post',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                var data = JSON.parse(response);

                // Check errors
                if (data.status === 'success') {
                    // Show message
                    form.find('.error-summary').text(data.message);
                    form.find('.error-summary').removeClass('alert-danger');
                    form.find('.error-summary').addClass('alert-success');
                    form.find('.error-summary').show();
                   
                    // Clear form
                    form.reset();
                } else {
                    if (data.message) {
                        // If set a custom error message
                        form.find('.error-summary').text(data.message);
                        form.find('.error-summary').removeClass('alert-success');
                        form.find('.error-summary').addClass('alert-danger');
                        form.find('.error-summary').show();                  
                    } else {
                        // If we have form error messages
                        var helpBlock = $('input[type="email"]').next('.help-block');
                   
                        // Clear content
                        helpBlock.text('');
                       
                        // Add error messages
                        if (data.email) {
                            for (var i = 0; i < data.email.length; i++) {
                                helpBlock.text(helpBlock.text() + data.email[i]); 
                            }
                        }       
                    }
                }
            },
            fail: function(data) {
                form.find('.error-summary').text('Ошибка при отправке сообщения');
            }
        });
       
        return false;
    });
    
    $('.reset-form').on('hidden.bs.modal', function (e) {
        // Clear form
        var form = $(this).find('form');
        form[0].reset();
    });
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>

<div class="modal reset-form fade js_modal_dev" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close clear-alerts" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="mySmallModalLabel">Отправить разработчику</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success fade in" style="display: none"></div>

                <?php $form = ActiveForm::begin([
                    'action' => Url::toRoute(['sites/send-to-developer']),
                    'id' => 'send-to-developer-form',
                    'enableClientValidation' => true,
                    'fieldConfig' => [
                        'options' => [
                            'tag' => false,
                        ],
                    ],
                ]); ?>

                    <?= $form->errorSummary($sendToDeveloperFormModel, [
                        'header' => '',
                        'class' => 'alert alert-danger fade in',
                    ]); ?>

                    <?= $form->field($sendToDeveloperFormModel, 'siteId')
                        ->hiddenInput([
                            'value' => $formModel->id ?? '',
                        ])
                        ->label(false)
                    ?>

                    <?= $form->field($sendToDeveloperFormModel, 'email')
                        ->input('email', [
                            'class' => 'form-control m-b-10',
                            'placeholder' => 'E-mail',
                        ])
                        ->label(false)
                    ?>

                    <?= $form->field($sendToDeveloperFormModel, 'body')
                        ->textarea([
                            'class' => 'form-control m-b-10',
                            'placeholder' => 'Комментарий',
                            'rows' => '10'
                        ])
                        ->label(false)
                    ?>

                    <?= Html::button('Отправить', [
                        'id' => 'send-to-developer-btn',
                        'class' => 'btn btn btn-success',
                        'type' => 'submit'
                    ])
                    ?>

                    <?= Html::button('Отменить', [
                        'class' => 'btn btn btn-default',
                        'data-dismiss' => 'modal'
                    ])
                    ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="dismiss btn btn-default" data-dismiss="modal">Отменить</button>
                <a id="delete-confirm-btn" class="btn btn-danger btn-ok">Удалить</a>
            </div>
        </div>
    </div>
</div>

<div class="modal reset-form text-left fade bs-example-modal-sm1" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-call-history-comment" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="mySmallModalLabel">Комментарий</h4> </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'action' => Url::toRoute(['call-history/update-comment']),
                    'id' => 'call-history-comment-form',
                    'enableClientValidation' => true,
                    'fieldConfig' => [
                        'options' => [
                            'tag' => false,
                        ],
                    ],
                ]); ?>
                    <?= $form->errorSummary($callHistoryCommentModel, [
                        'header' => '',
                        'class' => 'alert alert-danger fade in',
                    ]); ?>

                    <?= $form->field($callHistoryCommentModel, 'id')
                        ->hiddenInput([
                            'id' => 'call-comment-id',
                            'value' => $formModel->id ?? '',
                        ])
                        ->label(false)
                    ?>

                    <?= $form->field($callHistoryCommentModel, 'text')
                        ->textarea([
                            'id' => 'call-comment-text',
                            'class' => 'form-control m-b-10',
                            'rows' => '10',
                            'maxlength' => 100,
                        ])
                        ->label(false)
                    ?>

                    <?= Html::button('Сохранить', [
                        'id' => 'update-call-history-comment',
                        'class' => 'btn btn-success',
                        'type' => 'submit'
                    ])
                    ?>

                    <?= Html::button('Отменить', [
                        'class' => 'btn btn btn-default',
                        'data-dismiss' => 'modal'
                    ])
                    ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div id="modal-list-of-available-records" class="modal modal_senario fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; padding-right: 9px;">
    <div class="modal-dialog">

        <button class="modal_close" type="button" data-dismiss="modal" aria-hidden="true">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.9 21.9" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 21.9 21.9">
                <path d="M14.1,11.3c-0.2-0.2-0.2-0.5,0-0.7l7.5-7.5c0.2-0.2,0.3-0.5,0.3-0.7s-0.1-0.5-0.3-0.7l-1.4-1.4C20,0.1,19.7,0,19.5,0  c-0.3,0-0.5,0.1-0.7,0.3l-7.5,7.5c-0.2,0.2-0.5,0.2-0.7,0L3.1,0.3C2.9,0.1,2.6,0,2.4,0S1.9,0.1,1.7,0.3L0.3,1.7C0.1,1.9,0,2.2,0,2.4  s0.1,0.5,0.3,0.7l7.5,7.5c0.2,0.2,0.2,0.5,0,0.7l-7.5,7.5C0.1,19,0,19.3,0,19.5s0.1,0.5,0.3,0.7l1.4,1.4c0.2,0.2,0.5,0.3,0.7,0.3  s0.5-0.1,0.7-0.3l7.5-7.5c0.2-0.2,0.5-0.2,0.7,0l7.5,7.5c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3l1.4-1.4c0.2-0.2,0.3-0.5,0.3-0.7  s-0.1-0.5-0.3-0.7L14.1,11.3z"/>
            </svg>
        </button>

        <h4 class="modal-title" id="myModalLabel">Загруженные записи</h4>

        <div class="table-responsive">
            <table class="table table-bordered table_default table_default_scenarios">
                <thead>
                    <tr>
                        <th class="text-center">Название</th>
                        <th class="text-center">Прослушать</th>
                        <th class="text-nowrap text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>

                    <tr class="row-to-clone hidden">
                        <td class="audio-name">
                            <div class="name"></div>
                        </td>
                        <td class="audio-content text-center">
                            <div class="audio_flex">
                                <audio controls="" preload="none"
                                       data-src="<?= Yii::$app->params['rootApiLink'].'audio/getFile/'.Yii::$app->user->getId() . '/' ?>"
                                >
                                    <source src="" class="js_audio_file" type="audio/ogg; codecs=vorbis" controls="controls">
                                    <source src="" class="js_audio_file" type="audio/mpeg">
                                    Тег audio не поддерживается вашим браузером.
                                    <a href="" class="">Скачайте запись</a>.
                                </audio>

                                <a href="#" class="download_audio">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 26 26">
                                        <g>
                                            <path d="m25,17h-2c-0.6,0-1,0.4-1,1v2.5c0,0.3-0.2,0.5-0.5,0.5h-17c-0.3,0-0.5-0.2-0.5-0.5v-2.5c0-0.6-0.4-1-1-1h-2c-0.6,0-1,0.4-1,1v6c0,0.6 0.4,1 1,1h24c0.6,0 1-0.4 1-1v-6c0-0.6-0.4-1-1-1z"/>
                                            <path d="m12.3,16.7c0.2,0.2 0.5,0.3 0.7,0.3s0.5-0.1 0.7-0.3l6-6c0.2-0.2 0.3-0.4 0.3-0.7s-0.1-0.5-0.3-0.7l-1.4-1.4c-0.2-0.2-0.4-0.3-0.7-0.3-0.3,0-0.5,0.1-0.7,0.3l-1,1c-0.3,0.3-0.9,0.1-0.9-0.4v-6.5c0-0.6-0.4-1-1-1h-2c-0.6,0-1,0.4-1,1v6.6c0,0.4-0.5,0.7-0.9,0.4l-1-1c-0.2-0.2-0.4-0.3-0.7-0.3-0.3,0-0.5,0.1-0.7,0.3l-1.4,1.4c-0.2,0.2-0.3,0.4-0.3,0.7s0.1,0.5 0.3,0.7l6,5.9z"/>
                                        </g>
                                    </svg>
                                </a>
                            </div>

                        </td>

                        <td class="text-nowrap audio-actions text-center" style="width: 256px;">
                            <div class="modal_link_wrapp">
                                <a href="#"
                                   data-original-title="Выбрать" title="Выбрать"
                                   data-id="" data-name="" class="apply-btn modal_link"
                                >
                                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 330 330" width="16px" height="16px" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                                    <g>
                                        <path fill="#3cc400" d="M165,0C74.019,0,0,74.019,0,165s74.019,165,165,165s165-74.019,165-165S255.981,0,165,0z M165,300
                                            c-74.44,0-135-60.561-135-135S90.56,30,165,30s135,60.561,135,135S239.439,300,165,300z"/>
                                        <path fill="#3cc400" d="M226.872,106.664l-84.854,84.853l-38.89-38.891c-5.857-5.857-15.355-5.858-21.213-0.001
                                            c-5.858,5.858-5.858,15.355,0,21.213l49.496,49.498c2.813,2.813,6.628,4.394,10.606,4.394c0.001,0,0,0,0.001,0
                                            c3.978,0,7.793-1.581,10.606-4.393l95.461-95.459c5.858-5.858,5.858-15.355,0-21.213
                                            C242.227,100.807,232.73,100.806,226.872,106.664z"/>
                                    </g>
                                </svg>

                                    Выбрать
                                </a>

                                <a href="#"
                                   data-original-title="Удалить" title="Удалить"
                                   data-id="" data-name="" class="delete-btn modal_link"
                                >
                                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 16 16"
                                            width="16px" height="16px">
                                        <path fill-rule="evenodd"  fill="rgb(240, 63, 60)"
                                              d="M15.000,5.000 L15.000,15.000 C15.000,15.552 14.552,16.000 14.000,16.000 L2.000,16.000 C1.448,16.000 1.000,15.552 1.000,15.000 L1.000,5.000 L-0.000,5.000 L-0.000,4.000 C-0.000,3.448 0.448,3.000 1.000,3.000 L2.000,3.000 L3.000,3.000 L5.000,3.000 L5.000,1.000 C5.000,0.448 5.448,-0.000 6.000,-0.000 L10.000,-0.000 C10.552,-0.000 11.000,0.448 11.000,1.000 L11.000,3.000 L13.000,3.000 L14.000,3.000 L15.000,3.000 C15.552,3.000 16.000,3.448 16.000,4.000 L16.000,5.000 L15.000,5.000 ZM9.000,2.000 L7.000,2.000 L7.000,3.000 L9.000,3.000 L9.000,2.000 ZM13.000,5.000 L3.000,5.000 L3.000,14.000 L13.000,14.000 L13.000,5.000 ZM7.000,12.000 L5.000,12.000 L5.000,7.000 L7.000,7.000 L7.000,12.000 ZM11.000,12.000 L9.000,12.000 L9.000,7.000 L11.000,7.000 L11.000,12.000 Z"/>
                                    </svg>
                                    Удалить
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <!-- /.modal-dialog -->
</div>


<div id="modal-audio-record" class="modal modal_senario fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; padding-right: 9px;">
    <div class="modal-dialog">

        <button class="modal_close" type="button" data-dismiss="modal" aria-hidden="true">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.9 21.9" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 21.9 21.9">
                <path d="M14.1,11.3c-0.2-0.2-0.2-0.5,0-0.7l7.5-7.5c0.2-0.2,0.3-0.5,0.3-0.7s-0.1-0.5-0.3-0.7l-1.4-1.4C20,0.1,19.7,0,19.5,0  c-0.3,0-0.5,0.1-0.7,0.3l-7.5,7.5c-0.2,0.2-0.5,0.2-0.7,0L3.1,0.3C2.9,0.1,2.6,0,2.4,0S1.9,0.1,1.7,0.3L0.3,1.7C0.1,1.9,0,2.2,0,2.4  s0.1,0.5,0.3,0.7l7.5,7.5c0.2,0.2,0.2,0.5,0,0.7l-7.5,7.5C0.1,19,0,19.3,0,19.5s0.1,0.5,0.3,0.7l1.4,1.4c0.2,0.2,0.5,0.3,0.7,0.3  s0.5-0.1,0.7-0.3l7.5-7.5c0.2-0.2,0.5-0.2,0.7,0l7.5,7.5c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3l1.4-1.4c0.2-0.2,0.3-0.5,0.3-0.7  s-0.1-0.5-0.3-0.7L14.1,11.3z"/>
            </svg>
        </button>

        <h4 class="modal-title" id="myModalLabel">Прослушать запись</h4>

        <div class="table-responsive">
            <table class="table table-bordered table_default table_default_scenarios">
                <thead>
                <tr>
                    <th class="text-center">Прослушать</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="audio-content text-center">
                            <div class="audio_flex audio_flex_modal">
                                <audio controls="" preload="none" data-src="">
                                    <source src="" class="js_audio_file" type="audio/ogg; codecs=vorbis" controls="controls">
                                    <source src="" class="js_audio_file" type="audio/mpeg">
                                        Тег audio не поддерживается вашим браузером.
                                    <a href="" class="">Скачайте запись</a>.
                                </audio>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <!-- /.modal-dialog -->
</div>

