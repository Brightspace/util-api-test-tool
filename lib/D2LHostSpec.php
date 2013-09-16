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

/** Provides a convenient way of storing and passing server connection information to the library. */
class D2LHostSpec {
	private $m_scheme;
	private $m_host;
	private $m_port;

	public function __construct($host, $port, $scheme) {
		$this->m_host = $host;
		$this->m_port = $port;
		$this->m_scheme = $scheme;
		}

	public function Host() {
		return $this->m_host;
	}

	public function Port() {
		return $this->m_port;
	}

	public function Scheme() {
		return $this->m_scheme;
	}
}
?>