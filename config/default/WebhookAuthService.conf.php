<?php
/**
 * Default config header created during install
 */

return new oat\tao\model\webhooks\WebhookAuthService(array(
    'type' => new oat\tao\model\auth\BasicAuthType(),
    'types' => array(
        new oat\tao\model\auth\BasicAuthType()
    )
));
