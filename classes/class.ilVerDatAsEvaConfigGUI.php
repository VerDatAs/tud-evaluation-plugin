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
 * Class ilVerDatAsEvaConfigGUI
 *
 * @author TU Dresden <samuel.wilk@mailbox.tu-dresden.de, tommy.kubica@tu-dresden.de>
 * @ilCtrl_IsCalledBy ilVerDatAsEvaConfigGUI: ilObjComponentSettingsGUI
 *
 */
class ilVerDatAsEvaConfigGUI extends ilPluginConfigGUI
{
    private \ILIAS\DI\Container $dic;
    protected ilVerDatAsEvaPlugin $pl;

    /**
     * The constructor of ilVerDatAsEvaConfigGUI that defines the container variable and retrieves the instance of the ilVerDatAsEvaPlugin.
     */
    public function __construct()
    {
        global $DIC;
        $this->dic = $DIC;
        $this->pl = ilVerDatAsEvaPlugin::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function performCommand(string $cmd): void
    {
        $this->{$cmd}();
    }

    /**
     * @inheritDoc
     */
    protected function configure(ilPropertyFormGUI $form = null): void
    {
        if (!count(ilCmiXapiLrsTypeList::getTypesData(false))) {
            $this->dic->ui()->mainTemplate()->setOnScreenMessage('info', $this->pl->txt('missing_lrs_type'), true);
            return;
        }

        if ($form === null) {
            $form = $this->buildForm();
        }

        $this->dic->ui()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * @inheritDoc
     */
    protected function save()
    {
        $form = $this->buildForm();

        if (!$form->checkInput()) {
            return $this->configure($form);
        }

        $this->writeBackendURL($form->getInput('backend_url'));
        $this->writeLrsTypeId($form->getInput('lrs_type_id'));
        $this->writeShowLecturerView($form->getInput('show_lecturer_view'));
        $this->writeXapiVersion($form->getInput('xapi_version'));
        $this->writeStoreId($form->getInput('store_id'));

        $this->dic->ctrl()->redirect($this, 'configure');
    }

    /**
     * Build the form for configuring the plugin.
     *
     * @return ilPropertyFormGUI
     */
    protected function buildForm(): ilPropertyFormGUI
    {
        $form = new ilPropertyFormGUI();
        $form->setFormAction($this->dic->ctrl()->getFormAction($this));
        $form->addCommandButton('save', $this->dic->language()->txt('save'));
        $form->setTitle($this->pl->txt('configuration'));

        // TAS-Backend URL
        $backendURLItem = new ilTextInputGUI($this->pl->txt('backend_url'), 'backend_url');
        $backendURLItem->setRequired(true);

        $backendURLItem->setValue($this->readBackendURL());

        $form->addItem($backendURLItem);

        // LRS type
        $lrsTypeItem = new ilRadioGroupInputGUI($this->pl->txt('lrs_type'), 'lrs_type_id');
        $lrsTypeItem->setRequired(true);

        $types = ilCmiXapiLrsTypeList::getTypesData(false);

        foreach ($types as $type) {
            $option = new ilRadioOption($type['title'], $type['type_id'], $type['description']);
            $lrsTypeItem->addOption($option);
        }

        $lrsTypeItem->setValue($this->readLrsTypeId());

        $form->addItem($lrsTypeItem);

        // Show lecturer view to all users
        $showLecturerViewItem = new ilRadioGroupInputGUI($this->pl->txt('show_lecturer_view'), 'show_lecturer_view');
        $showLecturerViewItem->setRequired(true);

        $lecturerViewOptionTrue = new ilRadioOption($this->pl->txt('true'), 1, '');
        $lecturerViewOptionFalse = new ilRadioOption($this->pl->txt('false'), 0, '');
        $showLecturerViewItem->addOption($lecturerViewOptionTrue);
        $showLecturerViewItem->addOption($lecturerViewOptionFalse);

        $showLecturerViewItem->setValue($this->readShowLecturerView());

        $form->addItem($showLecturerViewItem);

        // xAPI version
        $xapiVersionItem = new ilTextInputGUI($this->pl->txt('xapi_version'), 'xapi_version');
        $xapiVersionItem->setInfo($this->pl->txt('xapi_version_placeholder'));
        $xapiVersionItem->setRequired(true);

        $xapiVersionItem->setValue($this->readXapiVersion());

        $form->addItem($xapiVersionItem);

        // LRS Store ID
        $storeIdItem = new ilTextInputGUI($this->pl->txt('store_id'), 'store_id');
        $storeIdItem->setInfo($this->pl->txt('store_id_placeholder'));
        $storeIdItem->setRequired(true);

        $storeIdItem->setValue($this->readStoreId());

        $form->addItem($storeIdItem);

        return $form;
    }

    /**
     * Read the defined TAS-Backend URL from the settings.
     *
     * @return string
     */
    protected function readBackendURL(): string
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        return $settings->get('backend_url');
    }

    /**
     * Write the defined TAS-Backend URL into the settings.
     *
     * @var string $backendURL
     */
    protected function writeBackendURL(string $backendURL): void
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        $settings->set('backend_url', $backendURL);
    }

    /**
     * Read the selected LRS type from the settings.
     *
     * @return int
     */
    protected function readLrsTypeId(): int
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        return $settings->get('lrs_type_id', 0);
    }

    /**
     * Write the selected LRS type into the settings.
     *
     * @var int $lrsTypeId
     */
    protected function writeLrsTypeId(int $lrsTypeId): void
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        $settings->set('lrs_type_id', $lrsTypeId);
    }

    /**
     * Read the selection whether all users should be shown the lecturer view from the settings.
     *
     * @return int
     */
    protected function readShowLecturerView(): int
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        return $settings->get('show_lecturer_view', 0);
    }

    /**
     * Write the selection whether all users should be shown the lecturer view into the settings.
     *
     * @var int $showLecturerView
     */
    protected function writeShowLecturerView(int $showLecturerView): void
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        $settings->set('show_lecturer_view', $showLecturerView);
    }

    /**
     * Read the defined xAPI version from the settings.
     *
     * @return string
     */
    protected function readXapiVersion(): string
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        return $settings->get('xapi_version');
    }

    /**
     * Write the defined xAPI version into the settings.
     *
     * @var string $xapiVersion
     */
    protected function writeXapiVersion(string $xapiVersion): void
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        $settings->set('xapi_version', $xapiVersion);
    }

    /**
     * Read the defined LRS store ID from the settings.
     *
     * @return string
     */
    protected function readStoreId(): string
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        return $settings->get('store_id');
    }

    /**
     * Write the defined LRS store ID into the settings.
     *
     * @var string $storeId
     */
    protected function writeStoreId(string $storeId): void
    {
        $settings = new ilSetting(ilVerDatAsEvaPlugin::PLUGIN_ID);
        $settings->set('store_id', $storeId);
    }
}
