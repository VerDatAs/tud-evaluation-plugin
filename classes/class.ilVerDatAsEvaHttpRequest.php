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

use GuzzleHttp\Client as HttpClient;

/**
 * Class ilVerDatAsEvaHttpRequest
 *
 * @author TU Dresden <tommy.kubica@tu-dresden.de>
 * @ilCtrl_isCalledBy ilVerDatAsEvaHttpRequest: ilVerDatAsEvaPluginGUI
 */
class ilVerDatAsEvaHttpRequest
{
    protected string $base_uri;

    /**
     * The constructor of ilVerDatAsEvaHttpRequest that defines the base URI for all following requests.
     *
     * @param string $base_uri
     */
    public function __construct(string $base_uri)
    {
        $this->base_uri = $base_uri;
    }

    /**
     * Send an HTTP POST request to a given request URL with a given body in JSON format.
     *
     * @param string $request_url
     * @param array  $request_body
     * @return mixed
     */
    public function sendPost(string $request_url, array $request_body): mixed
    {
        $client = new HttpClient([
            'base_uri' => $this->base_uri,
            'headers' => ['Content-Type' => 'application/json']
        ]);

        $response = $client->post($request_url, [
            GuzzleHttp\RequestOptions::JSON => $request_body
        ]);

        return $response->getBody();
    }
}
