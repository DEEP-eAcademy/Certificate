<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * User interface hook class for CourseCertificate-Plugin
 * @author  Alex Killing <alex.killing@gmx.de>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilCertificateUIHookGUI extends ilUIHookPluginGUI
{

    const TAB_CERTIFICATE = 'certificate';
    const TAB_MY_CERTIFICATE = 'my_certificate';
    /**
     * @var ilCtrl
     */
    public $ctrl;
    /**
     * @var ilCertificatePlugin
     */
    protected $pl;
    /**
     * @var ilObjUser
     */
    protected $user;
    /**
     * @var ilAccessHandler
     */
    protected $access;

    public function __construct()
    {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->pl = ilCertificatePlugin::getInstance();
        $this->user = $DIC->user();
        $this->access = $DIC->access();
    }

    /**
     * @param       $a_comp
     * @param       $a_part
     * @param array $a_par
     */
    public function modifyGUI($a_comp, $a_part, $a_par = array()): void
    {
        /**
         * @var $ilTabsGUI ilTabsGUI
         */
        // ATM only display certificate tab in courses
        if ($a_part == 'tabs' && !empty($a_par['tabs']->target) && $this->ctrl->getContextObjType() == 'crs'
            && ((isset($_GET['ref_id'])
                    && isset($_GET['baseClass']) && strtolower($_GET['baseClass']) === strtolower(ilRepositoryGUI::class))
                || (isset($_GET['target']) && strpos($_GET['target'], 'crs') === 0))) {
            $target_parts = isset($_GET['target']) ? explode('_', $_GET['target']) : array();
            $ref_id = isset($_GET['ref_id']) && $_GET['ref_id'] ? $_GET['ref_id'] : array_pop($target_parts);
            $ilTabsGUI = $a_par['tabs'];
            // User needs write access to course to see the tab 'certificate'
            if ($this->access->checkAccess('write', '', (int) $ref_id)) {
                if (isset($_GET['ref_id'])) {
                    $this->ctrl->setParameterByClass(srCertificateDefinitionGUI::class, 'ref_id', $_GET['ref_id']);
                }
                $ilTabsGUI->addTab(self::TAB_CERTIFICATE, $this->pl->txt('certificate'),
                    $this->ctrl->getLinkTargetByClass(array(
                        ilUIPluginRouterGUI::class,
                        srCertificateDefinitionGUI::class
                    )));
            }
            if (isset($_GET['ref_id'])) {
                $this->ctrl->setParameterByClass(srCertificateUserGUI::class, 'ref_id', $_GET['ref_id']);
            }
            $ilTabsGUI->addTab(self::TAB_MY_CERTIFICATE, $this->pl->txt('my_certificates'),
                $this->ctrl->getLinkTargetByClass(array(
                    ilUIPluginRouterGUI::class,
                    srCertificateUserGUI::class
                )));
        }
    }
}
