<?php
/**
 * Default config header created during install
 */

return new oat\tao\model\entryPoint\EntryPointService(array(
    'existing' => array(
        'passwordreset' => new oat\tao\model\entryPoint\PasswordReset(),
        'deliveryServer' => new oat\taoProctoring\model\entrypoint\ProctoringDeliveryServer(),
        'backoffice' => new oat\taoCe\model\entryPoint\TaoCeEntrypoint(),
        'proctoring' => new oat\taoProctoring\model\entrypoint\ProctoringEntryPoint(),
        'guestaccess' => new oat\taoDeliveryRdf\model\guest\GuestAccess(),
        'clientDiagGuestAccess' => new oat\taoClientDiagnostic\model\guest\GuestAccess()
    ),
    'postlogin' => array(
        'deliveryServer',
        'backoffice',
        'proctoring'
    ),
    'prelogin' => array(
        'guestaccess',
        'clientDiagGuestAccess'
    )
));