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
 * Class ilVerDatAsEvaUIHookGUI
 *
 * @author TU Dresden <tommy.kubica@tu-dresden.de>
 * @ilCtrl_isCalledBy ilVerDatAsEvaUIHookGUI: ilPCPluggedGUI
 */
class ilVerDatAsEvaUIHookGUI extends ilUIHookPluginGUI
{
    const TAB_EVALUATION = "statistics";
    protected ilCtrl $ctrl;
    protected ilVerDatAsEvaPlugin $pl;

    public function __construct()
    {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->pl = ilVerDatAsEvaPlugin::getInstance();
    }

    /**
     * Modify the course tab GUI objects, before they generate output.
     *
     * @param string $a_comp string that identifies the component
     * @param string $a_part string that identifies the part of the UI that is handled
     * @param array $a_par   array of parameters (depends on $a_comp and $a_part), e.g. name of the template used
     */
    public function modifyGUI(string $a_comp, string $a_part, array $a_par = array()): void
    {
        global $ilTabs;

        $refId = $this->pl->getCurrentRefId();
        $refIdExistsAndIsCourseObject = $refId && $this->ctrl->getContextObjType() == 'crs';

        // This should only be executed for course objects
        if ($a_part == 'tabs' && $refIdExistsAndIsCourseObject) {
            $this->ctrl->saveParameterByClass(ilVerDatAsEvaGUI::class, 'ref_id');

            $uri = $this->ctrl->getLinkTargetByClass([ilUIPluginRouterGUI::class, ilVerDatAsEvaGUI::class], ilVerDatAsEvaGUI::CMD_EVALUATION);
            $ilTabsGUI = $a_par['tabs'];
            $ilTabsGUI->addTab(self::TAB_EVALUATION, $this->pl->txt('statistics'), $uri);
            // Save the tabs to be reused on the statistics tab
            $_SESSION[ilVerDatAsEvaGUI::class]['TabTarget'] = $ilTabs->target;
        }
    }
}
