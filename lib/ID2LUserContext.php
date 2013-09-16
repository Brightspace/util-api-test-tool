<?php
/*
 * Copyright (c) 2012 Desire2Learn Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the license at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

interface ID2LUserContext {

    /** Retrieve the back-end service's host name for this calling user context. */
    public function getHostName();

    /** Retrieve the port the back-end service exposes for API calls. */
    public function getPort();

    /** Retrieve the service-provided User ID for this calling user context. */
    public function getUserId();

    /** Retrieve the service-provided User Key for this calling user context. */
    public function getUserKey();

    /** Retrieve the currently known time-skew between the local and back-end service clocks. */
    public function getServerSkewSeconds();

    /** Set the currently known time-skew between the local and back-end service clocks. */
    public function setServerSkewSeconds($seconds);


    /**
     * Retreive an appropriately decorated URI for invoking a REST route on the
     * back-end service.
     *
     * @param string $path REST route for the action to invoke.
     * @param string $httpMethod HTTP method to use with the route for the action
     * (DELETE, GET, POST, or PUT).
     *
     * @return string Decorated URI that the client application can use to invoke the
     * REST API action for the method and path.
     */
    public function createAuthenticatedUri($path, $httpMethod );


    /**
     * Calculates the clock skew between the local clock and the back-end service
     * clock, based on an HTTP response body sent by the service.
     *
     * If the back-end service sends back a 403 with the timestamp out of range
     * message, the client application use this method to re-calculate the user
     * context's cached server skew value.
     *
     * @param string $responseBody HTTP response body for the timestamp out of
     * range messaged response.
     *
     * @return boolean True if the time skew is successfully re-calculated; otherwise, false.
     */
    public function calculateServerSkewFromResponse($responseBody);


    /**
     * Interpret an HTTP response from the back-end service in response to an API
     * call.
     *
     * This method does two things:
     *
     * - Given a reponse from the back-end service, it can do a simple parse and
     *   return a numeric value (one of the RESULT_* constant values in the
     *   D2LUserContext default implementation) indicating what the caller might
     *   need to do next (re-authenticate the user, verify that the user has
     *   permissions to attempt the action taken, and so forth).
     *
     *   Most commonly, applications will minimally want to check for RESULT_OKAY
     *   in order to proceed with interpreting result data sent back by the API call.
     *
     * - If the response from the back-end service indicates that the service's
     *   clock offset is outside an allowable range, this method re-calculates the
     *   clock offset and saves it in the user context's state.
     *
     * @param string $response Response body sent by the back-end service in
     * response to an Learning Framework API call.
     * @param string $httpCode HTTP code associated with the reponse (i.e. 200,
     * 403, etc).
     * @param string $contentType Response content-type.
     *
     * @return int A numeric value that the caller can use to determine what the next
     * action might need to be.
     */
    public function handleResult($response, $httpCode, $contentType);

}
?>