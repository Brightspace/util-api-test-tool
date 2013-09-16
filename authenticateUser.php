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
require_once('config.php');
require_once $config['libpath'] . '/D2LAppContextFactory.php';

$host = $_GET['hostField'];
$port = $_GET['portField'];
$appId = $_GET['appIDField'];
$appKey = $_GET['appKeyField'];
$scheme = isset($_GET['schemeField']) ? 'https' : 'http';
$redirectPage = $_SERVER["HTTP_REFERER"];

$authContextFactory = new D2LAppContextFactory();
$authContext = $authContextFactory->createSecurityContext($appId, $appKey);
$hostSpec = new D2LHostSpec($host, $port, $scheme);
$url = $authContext->createUrlForAuthenticationFromHostSpec($hostSpec, $redirectPage);

header("Location: $url");

?>
