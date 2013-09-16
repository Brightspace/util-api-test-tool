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


/**
 * Interface for an application context object that a client application use to
 * create an application-specific calling context for use with a back-end LMS
 * service.
 *
 * To retrieve the default library-provided implementation of an application
 * context, the client should invoke CreateSecurityContext(appId,appKey) on the
 * D2LAppContextFactory factory class.
 */
interface ID2LAppContext {


    /**
     * Retrieve a URL the client can use to initiate the authentication process
     * with an LMS.
     *
     * The client application will use the URL retrieved from this method by
     * passing it to a web control or browser to let the user authenticate with
     * the back-end service. The resultUri provided by this call is a callback
     * URI that the client application will handle: when the LMS finishes the
     * user-auth process, it will redirect back to this URI, including the
     * user's ID and key tokens as quoted parameters.
     *
     * @param string $host Host name for the back-end service.
     * @param string $port Port the service uses for API requests.
     * @param string $resultURI Client-application-handled URI that the back-end
     * service should redirect the user back to after user-authentication.
     * @param boolean $encryptOperations True for HTTPS, false for HTTP
     *
     * @return string URL that the client application should pass to a web control or
     * browser to let the user authenticate with the back-end service.
     */
    public function createUrlForAuthentication($host, $port, $resultUri, $encryptOperations = true);

    /**
     * Wrapper for createUrlForAuthentication that takes a D2LHostSpec structure.
     *
     * @param D2LHostSpec $hostSpec HostSpec structure containing the D2L servers host, port and URI scheme
     * @param string $resultURI Client-application-handled URI that the back-end
     * service should redirect the user back to after user-authentication.
     *
     * @return string URL that the client application should pass to a web control or
     * browser to let the user authenticate with the back-end service.
     */
    public function createUrlForAuthenticationFromHostSpec($hostSpec, $resultUri);

    /**
     * Build a new authenticated-user context the client application can use to
     * create decorated URLs for invoking routes on the back-end service.
     *
     * An application context instance can create a user context in several
     * modes:
     *
     * - If the client application already knows an authenticated user context's
     *   state (host, port, user ID, user Key) then it rebuild a user context
     *   for making authenticated calls.
     *
     * - If this application context has just initiated the auth process with
     *   the back-end service, and now has a result URI to handle, the caller
     *   can provide that result URI in and this method can parse the needed
     *   user context state out of it to build a new user context.
     *
     * - If the caller wants to use one of the API routes that doesn't require
     *   an authenticated calling user context, it can leave userId, userKey,
     *   and callbackUri parameters as null to build a "default user" context.
     *
     * @param string $hostName Host name for the back-end service.
     * @param string $port Port the service uses for API requests.
     * @param boolean $encryptOperations If true, the user context will build
     * API-request URLs to use HTTPS; otherwise, it will build API-request URLs
     * to use HTTP.
     * @param string $userId LMS User ID identifying the appropriate user (as
     * provided by the back-end service); optional, null by default.
     * @param string $userKey LMS User Key to use for creating signatures (as
     * provided by the back-end service); optional, null by default.
     * @param string $callbackUri Entire result URI, including quoted
     * parameters, that the back-end service redirected the user to after
     * user-authentication; optional, null by default.
     *
     * @return ID2LUserContext User context object that implements ID2LUserContext, usable for
     * creating properly decorated API requests.
     */
    public function createUserContext($hostName, $port, $encryptOperations, $userId = null, $userKey = null, $callbackUri = null);

    /**
     * Wrapper for createUserContext that takes a D2LHostSpec structure.
     *
     * @param D2LHostSpec $hostSpec HostSpec structure containing the D2L servers host, port and URI scheme
     * @param string $callbackUri Entire result URI, including quoted
     * parameters, that the back-end service redirected the user to after
     * user-authentication; optional, null by default.
     *
     * @return ID2LUserContext User conext object that implements ID2LUserContext, usable for
     * creating properly decorated API requests.
     */
    public function createUserContextFromHostSpec($hostSpec, $userId = null, $userKey = null, $callbackUri = null);
}
?>