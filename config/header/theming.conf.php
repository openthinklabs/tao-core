<?php
/**
 * The theming service is used to determine which theme should
 * be used by the php backend
 */

return new oat\tao\model\theme\ThemeService(array(
    'available' => array(
        'default' => new oat\tao\model\theme\DefaultTheme(),
        'unand' => new oat\tao\model\theme\UNANDTheme(),
    ),
    'current' => 'unand'
));
