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

require_once 'ID2LUserContext.php';
require_once 'UserOpSecurityParameters.php';

class D2LUserContext implements ID2LUserContext {

    const APP_ID_PARAMETER = "x_a";
    const USER_ID_PARAMETER = "x_b";
    const SIGNATURE_BY_APP_KEY_PARAMETER = "x_c";
    const SIGNATURE_BY_USER_KEY_PARAMETER = "x_d";
    const TIMESTAMP_PARAMETER = "x_t";

    /** Returned when no result can be identified or as an unitialized value. */
    const RESULT_UNKNOWN = 0x00;

    /** Returned when result 200 okay. */
    const RESULT_OKAY = 0x01;

    /**
     * Returned when the signature or id was invalid, typically this should
     * trigger a reauthentication.
     */
    const RESULT_INVALID_SIG = 0x02;

    /**
     * Returned if the timestamp was outside of the validity window, this
     * indicates clocks are skewed. The handleResult message automatically
     * corrects the clock so on receiving this message callers typically should
     * retry the same operation.
     */
    const RESULT_INVALID_TIMESTAMP = 0x03;

    /**
     * Returned if the requested operation is not allowed, typically user should
     * be prompted that they need to request different permissions from the
     * administrator.
     */
    const RESULT_NO_PERMISSION = 0x04;

    private $_userId;
    private $_userKey;
    private $_appId;
    private $_appKey;
    private $_hostName;
    private $_port;
    private $_encryptOperations;
    private $_serverSkewSeconds = 0;

    /* Build a new user context with provided user context state object. */
    public function __construct(UserOpSecurityParameters $parameters) {
        $this->_userId = $parameters->userId;
        $this->_userKey = $parameters->userKey;
        $this->_appId = $parameters->appId;
        $this->_appKey = $parameters->appKey;
        $this->_hostName = $parameters->hostName;
        $this->_port = $parameters->port;
        $this->_encryptOperations = $parameters->encryptOperations;
    }

    /* Implements ID2LUserContext.getHostName() */
    public function getHostName() {
        return $this->_hostName;
    }

    /* Implements ID2LUserContext.getPort() */
    public function getPort() {
        return $this->_port;
    }

    /* Implements ID2LUserContext.getUserId() */
    public function getUserId() {
        return $this->_userId;
    }

    /* Implements ID2LUserContext.getUserKey() */
    public function getUserKey() {
        return $this->_userKey;
    }

    /* Implements ID2LUserContext.getServerSkewSeconds() */
    public function getServerSkewSeconds() {
        return $this->_serverSkewSeconds;
    }

    /* Implements ID2LUserContext.setServerSkewSeconds(seconds) */
    public function setServerSkewSeconds($seconds) {
        $this->_serverSkewSeconds = $seconds;
    }

    /* Implements ID2LUserContext.CreateAuthenticatedUri(path,httpMethod) */
    public function createAuthenticatedUri($path, $httpMethod) {
		$parsed_Url = parse_url ($path);
        $uriScheme = $this->GetUriScheme();
        $queryString = $this->getQueryString($parsed_Url['path'], $httpMethod);
        $uri = $uriScheme . '://' . $this->_hostName . ':' . $this->_port . $parsed_Url ['path'] . $queryString;
        if (isset ($parsed_Url ['query']))
        {
            $uri .= '&' . $parsed_Url ['query'];
        }
        return $uri;
    }

    /* Implements ID2LUserContext.CalculateServerSkewFromResponse(responseBody) */
    public function calculateServerSkewFromResponse($responseBody) {
        preg_match("/Timestamp out of range\s*(\d+)/", $responseBody, $matches);
        if ($matches) {
            $this->_serverSkewSeconds = $matches[1] - time();
            return true;
        } else {
            return false;
        }
    }

    /* Implements ID2LUserContext.handleResult(response,httpCode,contentType) */
    public function handleResult($response, $httpCode, $contentType) {
        if ($response === false) {
            return D2LUserContext::RESULT_UNKNOWN;
        } else {
            switch ($httpCode) {
                case "200":
                    return D2LUserContext::RESULT_OKAY;
                    break;
                case "401":
                    return D2LUserContext::RESULT_INVALID_SIG;
                    break;
                case "403":
                    // see if it is a time skew problem
                    if ($this->calculateServerSkewFromResponse($response)) {
                        if (!$this->calculateServerSkewFromResponse($response)) {
                            die("Could not calculate the server time skew");
                        }
                        return D2LUserContext::RESULT_INVALID_TIMESTAMP;
                    }

                    // Invalid signature
                    if (preg_match('/Invalid token/', $response)) {
                        return D2LUserContext::RESULT_INVALID_SIG;
                    }

                    // Not authorized
                    if (strstr($contentType, 'application/json')) {
                        $jsonResponse = json_decode($response);
                        if ($jsonResponse->Errors && $jsonResponse->Errors[0]->Message == "Not Authorized") {
                            return D2LUserContext::RESULT_NO_PERMISSION;
                        }
                    }
                    break;
                default:
                    return D2LUserContext::RESULT_UNKNOWN;
                    break;
            }
        }

        return D2LUserContext::RESULT_UNKNOWN;
    }

    private function getQueryString($path, $httpMethod) {
        $adjustedTimestampSeconds = $this->getAdjustedTimestampInSeconds();
        $signature = $this->formatSignature( $path, $httpMethod, $adjustedTimestampSeconds );
        $queryString = $this->buildAuthenticatedUriQueryString( $signature, $adjustedTimestampSeconds );
        return $queryString;
    }

    private function getAdjustedTimestampInSeconds() {
        return time() + $this->_serverSkewSeconds;
    }

    private function formatSignature($path, $httpMethod, $timestampSeconds) {
        return strtoupper($httpMethod) . '&' . urldecode(strtolower($path)) . '&' . $timestampSeconds;
    }

    private function buildAuthenticatedUriQueryString( $signature, $timestamp ) {
        $queryString  = '?' . D2LUserContext::APP_ID_PARAMETER . '=' . $this->_appId;
        $queryString .= '&' . D2LUserContext::USER_ID_PARAMETER . '=' . $this->_userId;
        $queryString .= '&' . D2LUserContext::SIGNATURE_BY_APP_KEY_PARAMETER;
        $queryString .= '=' . D2LSigner::getBase64HashString($this->_appKey, $signature);
        $queryString .= '&' . D2LUserContext::SIGNATURE_BY_USER_KEY_PARAMETER;
        $queryString .= '=' . D2LSigner::getBase64HashString($this->_userKey, $signature);
        $queryString .= '&' . D2LUserContext::TIMESTAMP_PARAMETER . '=' . $timestamp;

        return $queryString;
    }

    private function GetUriScheme() {
        if( $this->_encryptOperations ) {
            return D2LConstants::URI_SECURE_SCHEME;
        } else {
            return D2LConstants::URI_UNSECURE_SCHEME;
        }
    }

}
?>
