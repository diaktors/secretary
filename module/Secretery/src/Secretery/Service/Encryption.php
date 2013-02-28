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
 * Encryption Service
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Encryption
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
     * Encrypt string with multiple public keys
     *
     * @param  string $content
     * @param  array  $keys
     * @return array
     * @throws \InvalidArgumentException If key is empty
     * @throws \LogicException           If key is not readable as key
     * @throws \LogicException           If encryption errors
     */
    public function encryptForMultipleKeys($content, array $keys)
    {
        if (empty($keys)) {
            throw new \InvalidArgumentException('Keys array canot be empty');
        }
        $pubKeys = array();
        foreach ($keys as $userId => $key) {
            $pk = openssl_pkey_get_public($key);
            if (false === $pk) {
                throw new \LogicException('Key is not readable');
            }
            $pubKey    = openssl_pkey_get_details($pk);
            $pubKeys[] = $pubKey['key'];
            openssl_free_key($pk);
            unset($pubKey);
        }
        $sealCheck = openssl_seal(serialize($content), $sealedContent, $eKeys, $pubKeys);
        unset($pubKeys);
        if (false === $sealCheck) {
            throw new \LogicException('An error occurred while encrypting');
        }
        $eKeysEncoded = array();
        foreach ($eKeys as $eKey) {
            $eKeysEncoded[] = base64_encode($eKey);
        }
        return array(
            'ekeys'   => $eKeysEncoded,
            'content' => base64_encode($sealedContent)
        );
    }

    /**
     * Encrypt string with public key
     *
     * @param  string $content
     * @param  string $key
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
        unset($pubKey);
        if (false === $sealCheck) {
            throw new \LogicException('An error occurred while encrypting');
        }
        return array(
            'ekey'    => base64_encode($eKeys[0]),
            'content' => base64_encode($sealedContent)
        );
    }

    /**
     * Encrypt string with public key
     *
     * @param  string $content
     * @param  string $eKey
     * @param  string $key
     * @param  string $passphrase
     * @return string
     * @throws \InvalidArgumentException If content, ekey, key or passphrase is empty
     * @throws \LogicException           If key is not readable as key
     * @throws \LogicException           If encryption errors
     */
    public function decrypt($content, $eKey, $key, $passphrase)
    {
        if (empty($content)) {
            throw new \InvalidArgumentException('Content canot be empty');
        }
        if (empty($eKey)) {
            throw new \InvalidArgumentException('eKey canot be empty');
        }
        if (empty($key)) {
            throw new \InvalidArgumentException('Key canot be empty');
        }
        if (empty($passphrase)) {
            throw new \InvalidArgumentException('Passphrase canot be empty');
        }
        $pk = openssl_pkey_get_private($key, $passphrase);
        if (false === $pk) {
            throw new \LogicException('Key is not readable');
        }
        $content = base64_decode($content);
        $eKey    = base64_decode($eKey);
        $check   = openssl_open($content, $contentDecrypted, $eKey, $pk);
        openssl_free_key($pk);
        if (false === $check) {
            throw new \LogicException('An error occurred while decrypting');
        }
        return unserialize($contentDecrypted);
    }

    /**
     * Validate (private) key
     *
     * @param  string $key
     * @param  string $passphrase
     * @return true
     * @throws \InvalidArgumentException If key is empty
     * @throws \LogicException           If key is not readable as key
     */
    public function validateKey($key, $passphrase)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key canot be empty');
        }
        $pk = openssl_pkey_get_private($key, $passphrase);
        if (false === $pk) {
            throw new \LogicException('Key is not readable');
        }
        openssl_free_key($pk);
        return true;
    }
}
