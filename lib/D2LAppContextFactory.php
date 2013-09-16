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

require_once 'D2LAppContext.php';

/**
 * Factory class for creating a default D2L application context implementation.
 */
class D2LAppContextFactory {

    /**
     * Build a new D2L LMS application security context.
     *
     * @param string $appId LMS application key (as provided to the application vendor by D2L's KeyTool).
     * @param string $appKey LMS application ID (as provided to the application vendor by D2L's KeyTool).
     *
     * @return ID2LAppContext Application context object that implements ID2LAppContext, usable
     * for creating calling user context objects.
     */
    public function createSecurityContext($appId, $appKey ) {
        return new D2LAppContext($appId, $appKey );
    }

}
?>