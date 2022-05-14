<?php
use oat\tao\helpers\Layout;
use oat\tao\model\theme\Theme;
?>
<main id="login-box" class="entry-point entry-point-container">
    <?=Layout::renderThemeTemplate(Theme::CONTEXT_BACKOFFICE, 'login-message')?>
    <div id="login-box-inner-container"></div>
     <p style="
     -moz-border-radius: 6px;
     -webkit-border-radius: 6px;
     background-color: #f0f7fb;
     border: solid 1px #3498db;
     border-radius: 6px;
     line-height: 18px;
     mc-auto-number-format: '{b}Note: {/b}';
     overflow: hidden;
     padding: 12px;"><strong>Informasi Penting:</strong><br/>
     Untuk <i>Login</i>, silakan gunakan NIM/NISN Anda. Jika NIM/NISN Anda mengandung angka 0 di depan, 
     misalnya 0011186693 maka <i>Login</i> Anda adalah : 11186693
     </p>
    <?php foreach(get_data('entryPoints') as $entrypoint): ?>
    <div>
        <a class="entry-point-link" href="<?= $entrypoint->getUrl() ?>" role="button"><?= $entrypoint->getTitle() ?></a>
    </div>
    <?php endforeach;?>
</main>
<?php if(get_data('show_gdpr')): ?>
<?=Layout::renderThemeTemplate(Theme::CONTEXT_BACKOFFICE, 'gdpr')?>
<?php endif; ?>
<script>
    requirejs.config({
        config: {
            'controller/login': {
                'message' : {
                    'info': <?=json_encode(get_data('msg'))?>,
                    'error': <?=json_encode(urldecode(get_data('errorMessage')))?>
                },
                'disableAutocomplete' : <?=get_data('autocompleteDisabled')?>,
                'enablePasswordReveal' : <?=get_data('passwordRevealEnabled')?>,
                'disableAutofocus': <?=get_data('autofocusDisabled')?>,
                'fieldMessages': {
                    'login': <?=json_encode(get_data('fieldMessages_password'))?>,
                    'password': <?=json_encode(get_data('fieldMessages_password'))?>
                }
            }
        }
    });
</script>
