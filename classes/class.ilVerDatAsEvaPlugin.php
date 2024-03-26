<?php

/**
 * Evaluation component ILIAS plugin for the assistance system developed as part of the VerDatAs project
 * Copyright (C) 2023-2024 TU Dresden (Tommy Kubica)
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
 * Class ilVerDatAsEvaPlugin
 *
 * @author TU Dresden <tommy.kubica@tu-dresden.de>
 */
class ilVerDatAsEvaPlugin extends ilUserInterfaceHookPlugin
{
    const PLUGIN_ID = "veva";
    const PLUGIN_NAME = "VerDatAsEva";
    protected static ilVerDatAsEvaPlugin $instance;

    /**
     * Retrieve the name of the plugin.
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    /**
     * Retrieve the instance of the ilVerDatAsEvaPlugin.
     *
     * @return ilVerDatAsEvaPlugin
     */
    public static function getInstance(): ilVerDatAsEvaPlugin
    {
        if (!isset(self::$instance)) {
            global $DIC;
            $componentRepository = $DIC['component.repository'];
            $pluginInfo = $componentRepository->getComponentByTypeAndName('Services', 'UIComponent')->getPluginSlotById('uihk')->getPluginByName(self::PLUGIN_NAME);
            $componentFactory = $DIC['component.factory'];
            self::$instance = $componentFactory->getPlugin($pluginInfo->getId());
        }
        return self::$instance;
    }

    /**
     * Retrieve the current ref ID defined in the query parameters of the URL.
     *
     * @return int
     */
    public static function getCurrentRefId(): int
    {
        $refId = filter_input(INPUT_GET, 'ref_id');

        if ($refId === null) {
            $param_target = filter_input(INPUT_GET, 'target');
            // As the component is included on different pages (and thus, the refId is not always included), check for null
            if (!is_null($param_target)) {
                $refId = explode('_', $param_target)[1];
            }
        }

        return intval($refId);
    }
}
