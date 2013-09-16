<?php
/**
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

/** Class to encapsulate the useful state for a calling user context. */
class UserOpSecurityParameters {

    public $userId;
    public $userKey;
    public $appId;
    public $appKey;
    public $hostName;
    public $port;
    public $encryptOperations;

    public function __construct ($userId, $userKey, $appId, $appKey, $hostName,
                                 $port, $encryptOperations) {
        $this->userId = $userId;
        $this->userKey = $userKey;
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->hostName = $hostName;
        $this->port = $port;
        $this->encryptOperations = $encryptOperations;
    }

}
?>