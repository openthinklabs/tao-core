<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

namespace oat\tao\model\security\xsrf;

use JsonSerializable;
use oat\tao\model\security\TokenGenerator;

/**
 * Class that provides the Token model
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class Token implements JsonSerializable
{
    use TokenGenerator;

    public const TOKEN_KEY = 'token';
    public const TIMESTAMP_KEY = 'ts';

    /**
     * @var string
     */
    private $token;

    /**
     * @var float
     */
    private $tokenTimeStamp;

    /**
     * Token constructor.
     * @param array $data
     * @throws \common_Exception
     */
    public function __construct($data = [])
    {
        if (empty($data)) {
            $this->token = $this->generate();
            $this->tokenTimeStamp = microtime(true);
        } elseif (isset($data[self::TOKEN_KEY], $data[self::TIMESTAMP_KEY])) {
            $this->setValue($data[self::TOKEN_KEY]);
            $this->setCreatedAt($data[self::TIMESTAMP_KEY]);
        }
    }

    /**
     * Set the value of the token.
     *
     * @param string $token
     */
    public function setValue($token)
    {
        $this->token = $token;
    }

    /**
     * Set the microtime at which the token was created.
     * @param float $timestamp
     */
    public function setCreatedAt($timestamp)
    {
        $this->tokenTimeStamp = $timestamp;
    }

    /**
     * Get the value of the token.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->token;
    }

    public function isExpired(int $timeLimit): bool
    {
        $actualTime = microtime(true);

        return $timeLimit > 0 && ($this->getCreatedAt() + $timeLimit) < $actualTime;
    }

    /**
     * Get the microtime at which the token was created.
     *
     * @return float
     */
    public function getCreatedAt()
    {
        return $this->tokenTimeStamp;
    }

    public function jsonSerialize(): array
    {
        return [
            self::TOKEN_KEY     => $this->getValue(),
            self::TIMESTAMP_KEY => $this->getCreatedAt(),
        ];
    }
}
