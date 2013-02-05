<?php
/**
 * Wesrc Copyright 2013
 * Modifying, copying, of code contained herein that is not specifically
 * authorized by Wesrc UG ("Company") is strictly prohibited.
 * Violators will be prosecuted.
 *
 * This restriction applies to proprietary code developed by WsSrc. Code from
 * third-parties or open source projects may be subject to other licensing
 * restrictions by their respective owners.
 *
 * Additional terms can be found at http://www.wesrc.com/company/terms
 *
 * PHP Version 5
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Service;

use Doctrine\ORM\EntityManager;
use \Doctrine\Common\Persistence\PersistentObject;

/**
 * Key Service
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Key
{
    /**
     * Create a private key and sign it with passphrase
     *
     * @param  string $passphrase
     * @return array
     * @throws \InvalidArgumentException If passphrase is empty
     */
    public function createPrivateKey($passphrase)
    {
        if (empty($passphrase)) {
            throw new \InvalidArgumentException('Passphrase cannot be empty');
        }
        $keyConfig = array('private_key_bits' => 2048);
        $keyRes    = openssl_pkey_new($keyConfig);
        $pubKey    = openssl_pkey_get_details($keyRes);
        openssl_pkey_export($keyRes, $privKey, $passphrase);
        openssl_free_key($keyRes);
        return array(
            'pub'  => $pubKey['key'],
            'priv' => $privKey
        );
    }

    /**
     * Encrypt string with public key
     *
     * @param  string $passphrase
     * @param  string $passphrase
     * @return array
     * @throws \InvalidArgumentException If key is empty
     * @throws \LogicException           If key is not readable as key
     * @throws \LogicException           If encryption errors
     */
    public function encryptForSingleKey($content, $key)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key canot be empty');
        }
        $pk = openssl_pkey_get_public($key);
        if (false === $pk) {
            throw new \LogicException('Key is not readable');
        }
        $pubKey    = openssl_pkey_get_details($pk);
        $sealCheck = openssl_seal(serialize($content), $sealedContent, $eKeys, array($pubKey['key']));
        openssl_free_key($pk);
        if (false === $sealCheck) {
            throw new \LogicException('An error occurred while encrypting');
        }
        return array(
            'ekey'    => base64_encode($eKeys[0]),
            'content' => base64_encode($sealedContent)
        );
    }
}
