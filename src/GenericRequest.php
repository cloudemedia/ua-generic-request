<?php
/**
 * Copyright (c) 2015 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING.txt file distributed with this package.
 *
 *
 * @category   WURFL
 *
 * @copyright  ScientiaMobile, Inc.
 * @license    GNU Affero General Public License
 */

namespace Wurfl\Request;

/**
 * Generic WURFL Request object containing User Agent, UAProf and xhtml device data; its id
 * property is the SHA512 hash of the user agent
 */
class GenericRequest implements \Serializable
{
    const MAX_HTTP_HEADER_LENGTH = 512;

    /**
     * @var array
     */
    private $request;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var string
     */
    private $browserUserAgent;

    /**
     * @var string
     */
    private $deviceUserAgent;

    /**
     * @var null|string
     */
    private $userAgentProfile;

    /**
     * @var bool
     */
    private $xhtmlDevice;

    /**
     * @var string
     */
    private $id;

    /**
     * @param array       $request          Original HTTP headers
     * @param string      $userAgent
     * @param string|null $userAgentProfile
     * @param bool        $xhtmlDevice
     * @param string|null $browserUserAgent
     * @param string|null $deviceUserAgent
     */
    public function __construct(
        array $request,
        $userAgent,
        $userAgentProfile = null,
        $xhtmlDevice = false,
        $browserUserAgent = null,
        $deviceUserAgent = null
    ) {
        $this->request                = $this->sanitizeHeaders($request);
        $this->userAgent              = $this->sanitizeHeaders($userAgent);
        $this->userAgentProfile       = $this->sanitizeHeaders($userAgentProfile);
        $this->xhtmlDevice            = $xhtmlDevice;
        $this->id                     = hash('sha512', $userAgent);

        if (null === $browserUserAgent) {
            $this->browserUserAgent = $this->userAgent;
        } else {
            $this->browserUserAgent = $browserUserAgent;
        }

        if (null === $deviceUserAgent) {
            $this->deviceUserAgent = $this->userAgent;
        } else {
            $this->deviceUserAgent = $deviceUserAgent;
        }
    }

    /**
     * @param array|string $headers
     *
     * @return array|string
     */
    private function sanitizeHeaders($headers)
    {
        if (!is_array($headers)) {
            return $this->truncateHeader($headers);
        }

        foreach ($headers as $header => $value) {
            $headers[$header] = $this->truncateHeader($value);
        }

        return $headers;
    }

    /**
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getBrowserUserAgent()
    {
        return $this->browserUserAgent;
    }

    /**
     * @return string
     */
    public function getDeviceUserAgent()
    {
        return $this->deviceUserAgent;
    }

    /**
     * @return string
     */
    public function getUserAgentProfile()
    {
        return $this->userAgentProfile;
    }

    /**
     * @return bool
     */
    public function isXhtmlDevice()
    {
        return $this->xhtmlDevice;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $header
     *
     * @return string
     */
    private function truncateHeader($header)
    {
        if (strpos($header, 'HTTP_') !== 0 || strlen($header) <= self::MAX_HTTP_HEADER_LENGTH) {
            return $header;
        }

        return substr($header, 0, self::MAX_HTTP_HEADER_LENGTH);
    }

    /**
     * Get the original HTTP header value from the request
     *
     * @param string $name
     *
     * @return string
     */
    public function getOriginalHeader($name)
    {
        if ($this->originalHeaderExists($name)) {
            return $this->request[$name];
        }

        return null;
    }

    /**
     * Checks if the original HTTP header is set in the request
     *
     * @param string $name
     *
     * @return bool
     */
    public function originalHeaderExists($name)
    {
        return array_key_exists($name, $this->request);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(
            [
                'request'                => $this->request,
                'userAgent'              => $this->userAgent,
                'browserUserAgent'       => $this->browserUserAgent,
                'deviceUserAgent'        => $this->deviceUserAgent,
                'userAgentProfile'       => $this->userAgentProfile,
                'xhtmlDevice'            => $this->xhtmlDevice,
                'id'                     => $this->id,
            ]
        );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     */
    public function unserialize($serialized)
    {
        $unseriliazedData = unserialize($serialized);

        $this->request                = $unseriliazedData['request'];
        $this->userAgent              = $unseriliazedData['userAgent'];
        $this->browserUserAgent       = $unseriliazedData['browserUserAgent'];
        $this->deviceUserAgent        = $unseriliazedData['deviceUserAgent'];
        $this->userAgentProfile       = $unseriliazedData['userAgentProfile'];
        $this->xhtmlDevice            = $unseriliazedData['xhtmlDevice'];
        $this->id                     = $unseriliazedData['id'];
    }
}
