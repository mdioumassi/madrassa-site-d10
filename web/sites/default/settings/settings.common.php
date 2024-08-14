<?php

/**
 * @file
 * Common settings.
 */

$config['system.logging']['error_level'] = 'hide';

/**
 * Performance
 */
$config['system.performance']['css']['preprocess'] = TRUE;
$config['system.performance']['js']['preprocess'] = TRUE;
$config['system.performance']['cache']['page']['max-age'] = 300;
$settings['rebuild_access'] = FALSE;

/**
 * Security
 */
$settings['skip_permissions_hardening'] = FALSE;

/**
 * Translations settings
 */
// $settings['custom_translations_directory'] = '../translations/common';
// $config['locale.settings']['translation']['path'] = '../translations/common/contrib';

/**
 * Trusted host patterns
 */
// $settings['trusted_host_patterns'] = [
//   '^drupal\.mcipo\.local$',
//   '^drupal\.int\.mcipoweb\.dev\.jv-its$',
//   '^drupal\.demo\.mcipoweb\.dev\.jv-its$',
//   '^mcipoweb\.luminess\.eu$',
//   '^mcipo-pre\.gouv\.mc$',
//   '^mcipo-prod\.gouv\.mc$',
//   '^mcipo\.gouv\.mc$',
// ];

/**
 * openid_connect.settings.okta
 */
// $config['openid_connect.settings.okta']['settings']['client_id'] = '0oaam43nt0mKeYkKb417';
// $config['openid_connect.settings.okta']['settings']['client_secret'] = 'ACBJivrLqyWV1lONtjnxhAlQyRBfZGJmU92SwDDdeJB1Mt52fAqKcrO7L24SMgqi';
// $config['openid_connect.settings.okta']['settings']['okta_domain'] = 'gvtmonaco.okta.com';
