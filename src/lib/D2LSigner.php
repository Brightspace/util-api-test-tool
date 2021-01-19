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

/** Signer class used to create token signatures for use in Valence API calls. */
class D2LSigner {

    /**
     * Produce a hash signature for a base string data, using a key.
     *
     * Both the key and data parameters should be UTF-8 strings.
     *
     * This method first generates a SHA-256-based HMAC of the provided base
     * string data. Then, it renders the result URL-safe by Base64-encoding it,
     * and removing all equal-sign characters, replacing all plus-sign characters
     * with hyphens, and replacing all forward-slash charaters with underbars.
     *
     * @param string $key Key to use for signing.
     * @param string $data Signature base string to sign.
     *
     * @return string Hash signature.
     */
    static function getBase64HashString($key, $data) {
        $return = hash_hmac('sha256', utf8_encode($data), utf8_encode($key), true);
        $return = base64_encode($return);

        $return = str_replace('=', '', $return);
        $return = str_replace('+', '-', $return);
        $return = str_replace('/', '_', $return);

        return $return;
    }

}
?>