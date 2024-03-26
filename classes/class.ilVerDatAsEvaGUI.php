<?php

/**
 * Evaluation component ILIAS plugin for the assistance system developed as part of the VerDatAs project
 * Copyright (C) 2023-2024 TU Dresden (Samuel Wilk, Tommy Kubica)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class ilVerDatAsEvaGUI
 *
 * @author TU Dresden <samuel.wilk@mailbox.tu-dresden.de, tommy.kubica@tu-dresden.de>
 * @ilCtrl_IsCalledBy ilVerDatAsEvaGUI: ilUIPluginRouterGUI
 */
class ilVerDatAsEvaGUI
{
    const CMD_EVALUATION = "statistics";
    protected \ILIAS\DI\Container $dic;
    protected ilCtrl $ctrl;
    protected ilGlobalTemplateInterface $tpl;
    protected ilTabsGUI $tabs;
    protected ilLocatorGUI $ilLocator;
    protected ilObjCourse $crsObj;
    protected bool $hasReadAccess;
    protected bool $hasWriteAccess;
    protected ilVerDatAsEvaPlugin $pl;

    /**
     * The constructor of ilVerDatAsEvaGUI that retrieves several parameters and the instance of the ilVerDatAsEvaPlugin.
     */
    public function __construct()
    {
        global $ilCtrl, $ilTabs, $ilLocator, $DIC;
        $this->dic = $DIC;
        $this->tabs = $ilTabs;
        $this->ctrl = $ilCtrl;
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->ilLocator = $ilLocator;
        $this->pl = ilVerDatAsEvaPlugin::getInstance();
        $this->crsObj = new ilObjCourse($this->pl->getCurrentRefId());
        $this->hasReadAccess = $DIC->access()->checkAccess('read', '', $this->crsObj->getRefId());
        $this->hasWriteAccess = $DIC->access()->checkAccess('write', '', $this->crsObj->getRefId());
    }

    /**
     * Execute a specific command by defining the order to function calls.
     */
    public function executeCommand(): void
    {
        $this->checkAccess();
        $this->prepareOutput();
        $this->tpl->printToStdout();
    }

    /**
     * Check, whether the current user has either read or write access on the course.
     */
    protected function checkAccess(): void
    {
        $hasAccess = $this->hasReadAccess || $this->hasWriteAccess;

        if (!$hasAccess) {
            $this->tpl->setOnScreenMessage('failure', $this->pl->txt('no_access_message'), true);
            $this->ctrl->redirectByClass(ilDashboardGUI::class, 'jumpToSelectedItems');
        }
    }

    /**
     * Prepare the output to include the evaluation component for the assistance system.
     */
    protected function prepareOutput()
    {
        // Retrieve the settings of the plugin
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        $lrsTypeId = $settings->get('lrs_type_id', 0);
        $backendURL = $settings->get('backend_url', 0);
        $xapiVersion = $settings->get('xapi_version', 0);
        $storeId = $settings->get('store_id', '');

        // Early return to avoid further unnecessary actions
        if (!$lrsTypeId || !$backendURL || !$xapiVersion || $storeId == '') {
            return '';
        }

        $alwaysShowLecturerView = $settings->get('show_lecturer_view', 0) == 1;
        // Note: The event tracked by XapiProgress does include "&obj_id_lrs" as a query parameter.
        // Thus, add this parameter for compatibility.
        $courseId = ilLink::_getLink($this->crsObj->getRefId(), 'crs') . '&obj_id_lrs=' . $this->crsObj->getId();

        // BEGIN: Retrieve token

        // Retrieve the selected LRS type
        $lrsType = new ilCmiXapiLrsType($lrsTypeId);
        // Retrieve the name mode defined within the LRS type
        $nameMode = isset(array_flip(get_class_methods($lrsType))['getPrivacyName']) ? $lrsType->getPrivacyIdent() : $lrsType->getUserIdent();
        // Retrieve the user ident for this name mode
        $userIdent = ilCmiXapiUser::getIdent($nameMode, $this->dic->user());

        // Check, whether an expireDate has been set and, if so, whether it has been exceeded
        if (!empty($_SESSION['expireDate'])) {
            if ($_SERVER['REQUEST_TIME'] > $_SESSION['expireDate']) {
                $_SESSION['userIdent'] = null;
                $_SESSION['jwt'] = null;
                $_SESSION['expireDate'] = null;
            }
        }

        // Check, whether at least one session variable is not set
        // Note: As the session terminates on logout, it is not required to check the userIdent for a new logged-in user
        if (empty($_SESSION['userIdent']) || empty($_SESSION['jwt'])) {
            // Prevent a crash, if the TAS-Backend cannot be reached
            try {
                // Make a request to the TAS-Backend to retrieve the user token, as we need the user ID
                $verDatAsBackendRequest = new ilVerDatAsEvaHttpRequest(
                    $backendURL
                );
                $responseBody = $verDatAsBackendRequest->sendPost('/api/v1/auth/login', ['actorAccountName' => $userIdent]);

                // Decode JWT Token
                // https://www.converticacommerce.com/support-maintenance/security/php-one-liner-decode-jwt-json-web-tokens/
                $arr_body = json_decode($responseBody);
                $token = $arr_body->token;
                $parsedToken = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
                $ident = $parsedToken->sub;
                $expireDate = $parsedToken->exp;

                // Define session variables
                $_SESSION['jwt'] = $token;
                $_SESSION['userIdent'] = $ident;
                $_SESSION['expireDate'] = $expireDate;
            } catch (Exception $e) {
                file_put_contents('console.log', $e->getMessage() . "\n", FILE_APPEND);
            }
        } else {
            // Reuse the existing token
            $token = $_SESSION['jwt'];
        }

        // Do not show the evaluation component, when the TAS-Backend cannot be accessed
        if (!($token ?? false)) {
            return '';
        }
        // END: Retrieve token

        // Prepare the output by setting the locator, title and description
        $this->ilLocator->addRepositoryItems($this->crsObj->getRefId());
        $this->tpl->loadStandardTemplate();
        $this->tpl->setLocator();
        $this->tpl->setTitleIcon(ilUtil::getImagePath('icon_crs.svg'));
        $this->tpl->setTitle($this->crsObj->getPresentationTitle());
        $this->tpl->setDescription($this->crsObj->getLongDescription());
        // Reuse the tabs that were saved earlier
        if (isset($_SESSION[ilVerDatAsEvaGUI::class]['TabTarget'])) {
            $this->dic->tabs()->target = $_SESSION[ilVerDatAsEvaGUI::class]['TabTarget'];
            $this->dic->tabs()->activateTab(ilVerDatAsEvaUIHookGUI::TAB_EVALUATION);
        }
        // Load the custom template
        $tabTemplate = new ilTemplate($this->pl->getDirectory() . '/templates/tpl.content.html', true, true);
        $tabTemplate->setVariable("TEMPLATE_PATH", "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/VerDatAsEva/templates");
        $this->tpl->setContent($tabTemplate->get());

        // Retrieve the LRS endpoint
        $lrsEndpoint = $lrsType->getLrsEndpoint();
        // Remove the suffix, as it is not used for querying
        $lrsEndpointWithoutSuffix = str_replace('/data/xAPI', '', $lrsEndpoint);
        // Create a Basic auth token from the key and secret
        $lrsToken = base64_encode($lrsType->getLrsKey() . ':' . $lrsType->getLrsSecret());

        // Define the environment data for initialization
        $initEnvironmentData = '{
          "XAPI_VERSION": ' . json_encode($xapiVersion) . ',
          "STORE_URL": ' . json_encode($lrsEndpointWithoutSuffix) . ',
          "STORE_AUTHORIZATION": ' . json_encode($lrsToken) . ',
          "LRS_STORE_ID": ' . json_encode($storeId) . ',
          "BACKEND_TOKEN": ' . json_encode($token) .',
          "BACKEND_URL": ' . json_encode($backendURL) .'
        }';

        // Depending on the role, add different runtime environment data and call the initialization function
        if ($this->hasWriteAccess || $alwaysShowLecturerView) {
            $initRuntimeEnvironmentData = '{
              "COURSE_ID": ' . json_encode($courseId) . '
            }';
            $this->tpl->addOnLoadCode("VerDatAsEvaluation.init(" . $initEnvironmentData . ", " . $initRuntimeEnvironmentData . ")");
        } else if ($this->hasReadAccess) {
            // For users without write access (i.e., students) and when the setting to always show the lecturer view is disabled, also add the USER_ID to limit the results displayed
            $initRuntimeEnvironmentData = '{
              "COURSE_ID": ' . json_encode($courseId) . ',
              "USER_ID": ' . json_encode($userIdent) . '
            }';
            $this->tpl->addOnLoadCode("VerDatAsEvaluation.init(" . $initEnvironmentData . ", " . $initRuntimeEnvironmentData . ")");
        }
    }
}