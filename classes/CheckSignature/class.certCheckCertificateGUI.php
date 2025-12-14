<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use srag\DIC\Certificate\DICTrait;

/**
 * GUI-Class certCheckCertificateGUI
 * Certificate verification page with ILIAS layout
 * @author            Certificate Plugin
 * @version           $Id:
 * @ilCtrl_IsCalledBy certCheckCertificateGUI: ilRouterGUI, ilUIPluginRouterGUI
 */
class certCheckCertificateGUI
{
    use DICTrait;
    const CMD_SHOW = 'show';

    /**
     * @var ilGlobalTemplateInterface
     */
    protected ilGlobalTemplateInterface $global_tpl;
    /**
     * @var ilCertificatePlugin
     */
    protected $pl;
    /**
     * @var ilCtrl
     */
    protected $ctrl;

    function __construct()
    {
        global $DIC;
        $this->global_tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilCertificatePlugin::getInstance();
        $this->ctrl = $DIC->ctrl();
    }

    /**
     * @return bool
     */
    public function executeCommand()
    {
        $cmd = $this->ctrl->getCmd(self::CMD_SHOW);
        
        // Load ILIAS standard template
        $this->global_tpl->loadStandardTemplate();
        
        // Set page title
        $this->global_tpl->setTitle($this->pl->txt('check_certificate'));
        
        switch ($cmd) {
            case self::CMD_SHOW:
            default:
                $this->show();
                break;
        }
        
        $this->global_tpl->printToStdout();
    }

    /**
     * Show certificate verification page
     */
    public function show()
    {
        // Get signature from GET parameter (raw, as it comes from URL)
        $signature = isset($_GET['signature']) ? $_GET['signature'] : '';
        
        // If signature is provided, check it immediately
        if ($signature) {
            $this->checkSignature($signature);
        } else {
            // Show form to enter signature
            $this->showForm();
        }
    }

    /**
     * Check signature and display result
     * @param string $signature
     */
    protected function checkSignature($signature)
    {
        require_once __DIR__ . '/../DigitalSignature/class.srCertificateDigitalSignature.php';
        
        $signature = trim($signature);
        
        // Handle trailing commas (sometimes signatures end with ,,)
        // Remove trailing commas but keep internal commas that are part of base64
        $signature = rtrim($signature, ',');
        
        // Decode the signature (handle URL-safe base64: convert -_, to +/=)
        // The signature is stored with URL-safe base64 encoding for QR codes
        // Original base64 uses: + / =
        // URL-safe base64 uses: - _ ,
        // Note: $_GET parameters are automatically URL-decoded by PHP
        $decoded_signature = strtr($signature, '-_,', '+/=');
        
        $decrypted = srCertificateDigitalSignature::decryptSignature($decoded_signature);
        
        // Check if decryption actually succeeded (openssl_public_decrypt returns false on failure)
        // If $decrypted is false or empty, the decryption failed
        if ($decrypted === false || $decrypted === '') {
            $decrypted = false;
        }

        global $DIC;
        $factory  = $DIC->ui()->factory();
        $renderer = $DIC->ui()->renderer();

        if ($decrypted) {
            $box = $factory->messageBox()->success(
                $this->pl->txt('decrypt_successful')
            );

            $pre = '<pre style="white-space: pre-wrap; word-wrap: break-word; font-family: monospace;">'
                . htmlspecialchars($decrypted)
                . '</pre>';

            $panel = $factory->panel()->standard(
                $this->pl->txt('certificate_valid'),
                $factory->legacy($pre)
            );

            $content = $renderer->render($box) . $renderer->render($panel);

        } else {
            $box = $factory->messageBox()->failure(
                $this->pl->txt('decrypt_failed')
            );

            $panel = $factory->panel()->standard(
                $this->pl->txt('certificate_invalid'),
                $factory->legacy('<p>' . htmlspecialchars($this->pl->txt('certificate_invalid_message')) . '</p>')
            );

            $content = $renderer->render($box) . $renderer->render($panel);
        }

        $this->global_tpl->setContent($content);
    }
}
