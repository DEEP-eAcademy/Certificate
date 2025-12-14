<?php
/**
 * Legacy certificate check redirect
 *
 * @deprecated Use:
 * /ilias.php?baseClass=ilUIPluginRouterGUI&cmdClass=certCheckCertificateGUI&cmd=show&signature=...
 */
declare(strict_types=1);


chdir('../../../../../../../../'); 
include_once "Services/Context/classes/class.ilContext.php"; 
ilContext::init(ilContext::CONTEXT_WEB); 
require_once("Services/Init/classes/class.ilInitialisation.php"); 
ilInitialisation::initILIAS();


// Read parameters
$signature = (string) ($_GET['signature'] ?? '');
$client_id = (string) ($_GET['client_id'] ?? '');

$signature = trim($signature);
$client_id = trim($client_id);

// Allow only expected characters (URL-safe base64 + =)
if ($signature !== '' && !preg_match('/^[A-Za-z0-9\-\_,=]+$/', $signature)) {
    $signature = '';
}

// ILIAS client id: alnum, underscore, dash
if ($client_id !== '' && !preg_match('/^[A-Za-z0-9_\-]+$/', $client_id)) {
    $client_id = '';
}

// Build target URL
$base_url = rtrim(ILIAS_HTTP_PATH, '/');

// Strip plugin path, keep ILIAS root
// ILIAS_HTTP_PATH example:
// https://example.com/ilias/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/Certificate/classes/
$ilias_root = preg_replace('~/Customizing/.*$~', '', $base_url);

// Build redirect parameters
$params = [
    'baseClass' => 'ilUIPluginRouterGUI',
    'cmdClass'  => 'certCheckCertificateGUI',
    'cmd'       => 'show',
];

if ($signature !== '') {
    $params['signature'] = $signature;
}
if ($client_id !== '') {
    $params['client_id'] = $client_id;
}

// Final redirect URL
$redirect_url = $ilias_root . '/ilias.php?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

// Redirect
header('Location: ' . $redirect_url, true, 303);
exit;
