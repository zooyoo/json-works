<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Schema\Constraints;

interface MatcherInterface
{
    public function getDetails(&$type, &$matchFirst);
    public function getResult($matches, $schemaCount, &$error);
}
