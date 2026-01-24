<?php
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Class srCertificateAdministrationGUI
 * @author            Stefan Wanzenried <sw@studer-raimann.ch>
 * @ilCtrl_IsCalledBy srCertificateAdministrationGUI : ilRouterGUI, ilUIPluginRouterGUI
 */
class srCertificateAdministrationGUI extends srCertificateGUI
{

    const CMD_CALL_BACK = 'callBack';
    const CMD_RETRY_GENERATION = 'retryGeneration';
    const CMD_UNDO_CALL_BACK = 'undoCallBack';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions
     */
    public function checkPermission()
    {
        $allowed_roles = ilCertificateConfig::getX('roles_administrate_certificates');

        return $this->rbac->isAssignedToAtLeastOneGivenRole($this->user->getId(), json_decode($allowed_roles, true));
    }

    protected function getTable($cmd)
    {
        $options = array('newest_version_only' => false);
        if (in_array($cmd, array(self::CMD_RESET_FILTER, self::CMD_APPLY_FILTER))) {
            $options = array_merge($options, array('build_data' => false));
        }

        return new srCertificateTableGUI($this, $cmd, $options);
    }

    protected function performCommand($cmd)
    {
        /** @var srCertificate $certificate */
        $certificate = srCertificate::find((int) $_GET['cert_id']);
        switch ($cmd) {
            case self::CMD_CALL_BACK:
                $this->callBack($certificate);
                break;
            case self::CMD_UNDO_CALL_BACK:
                $this->undoCallBack($certificate);
                break;
            case self::CMD_RETRY_GENERATION:
                $this->retryGeneration($certificate);
                break;
        }
    }

    /**
     * @param srCertificate $certificate
     */
    protected function callBack(srCertificate $certificate)
    {
        $certificate->setStatus(srCertificate::STATUS_CALLED_BACK);
        $certificate->update();
        $this->global_tpl->setOnScreenMessage($this->global_tpl::MESSAGE_TYPE_SUCCESS, $this->pl->txt('msg_called_back'), true);
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /**
     * @param srCertificate $certificate
     */
    protected function undoCallBack(srCertificate $certificate)
    {
        $certificate->setStatus(srCertificate::STATUS_PROCESSED);
        $certificate->update();
        $this->global_tpl->setOnScreenMessage($this->global_tpl::MESSAGE_TYPE_SUCCESS, $this->pl->txt('msg_undo_called_back'), true);
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /**
     * @param srCertificate $certificate
     */
    protected function retryGeneration(srCertificate $certificate)
    {
        $certificate->setStatus(srCertificate::STATUS_NEW);
        $certificate->update();
        $this->global_tpl->setOnScreenMessage($this->global_tpl::MESSAGE_TYPE_SUCCESS, $this->pl->txt('msg_retry_generation'), true);
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /**
     * Build action menu for a record asynchronous
     */
    protected function buildActions()
    {
        $ui_factory = self::dic()->ui()->factory();
        $ui_renderer = self::dic()->ui()->renderer();
        $this->ctrl->setParameter($this, 'cert_id', (int) $_GET['cert_id']);
        $buttons = [];

        switch ($_GET['status']) {
            case srCertificate::STATUS_CALLED_BACK:
                $buttons[] = $ui_factory->button()->shy(
                    $this->pl->txt('undo_callback'),
                    $this->ctrl->getLinkTarget($this, self::CMD_UNDO_CALL_BACK)
                );
                break;
            case srCertificate::STATUS_FAILED:
                $buttons[] = $ui_factory->button()->shy(
                    $this->pl->txt('retry'),
                    $this->ctrl->getLinkTarget($this, self::CMD_RETRY_GENERATION)
                );
                break;
            case srCertificate::STATUS_PROCESSED:
                $buttons[] = $ui_factory->button()->shy(
                    $this->pl->txt('download'),
                    $this->ctrl->getLinkTarget($this, self::CMD_DOWNLOAD_CERTIFICATE)
                );
                $buttons[] = $ui_factory->button()->shy(
                    $this->pl->txt('call_back'),
                    $this->ctrl->getLinkTarget($this, self::CMD_CALL_BACK)
                );
                break;
        }

        $dropdown = $ui_factory->dropdown()
            ->standard($buttons)
            ->withLabel($this->pl->txt('actions'));
        echo $ui_renderer->render($dropdown);
        exit;
    }


    /**
     * @inheritDoc
     */
    public function executeCommand():void
    {
        $this->global_tpl->setTitle($this->pl->txt('administrate_certificates'));

        parent::executeCommand();
    }
}
