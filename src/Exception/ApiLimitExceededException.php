<?php

declare(strict_types=1);

/*
 * This file is part of the DigitalOcean API library.
 *
 * (c) Antoine Kirk <contact@sbin.dk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalOceanV2\Exception;

use DigitalOceanV2\Entity\RateLimit;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ApiLimitExceededException extends RuntimeException
{
    public ?RateLimit $rateLimit;

    public function __construct(string $message, int $code, ?RateLimit $rateLimit = null)
    {
        parent::__construct($message, $code);

        $this->rateLimit = $rateLimit;
    }
}
