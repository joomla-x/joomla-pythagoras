<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Header;

/**
 * Class QualifiedHeader
 *
 * @package  Joomla/HTTP
 *
 * @since    __DEPLOY_VERSION__
 */
class QualifiedHeader
{
    /** @var string The header content */
    private $header;

    /** @var string Subtype separator */
    private $separator;

    /** @var string Wildcard character */
    private $wildcard;

    /**
     * QualifiedHeader constructor.
     *
     * @param   string $header     The header content
     * @param   string $separator  Subtype separator
     * @param   string $wildcard   Wildcard character
     */
    public function __construct($header, $separator, $wildcard)
    {
        if (preg_match('~^[\w-]+:\s+(.*)$~i', $header, $match)) {
            $header = $match[1];
        }

        $this->header    = $header;
        $this->separator = '~' . preg_quote($separator) . '~';
        $this->wildcard  = $wildcard;
    }

    /**
     * @param   array $availableRanges Available ranges
     *
     * @return  mixed
     */
    public function getBestMatch($availableRanges)
    {
        $acceptedRanges = $this->parseHeader($this->header);

        $matching = ['q' => 0.0];

        foreach ($availableRanges as $range) {
            $available = $this->split($range);

            foreach ($acceptedRanges as $acceptedRange) {
                $accepted = $this->split($acceptedRange['token']);

                if (!$this->match($available[0], $accepted[0])) {
                    continue;
                }

                if (!$this->match($available[1], $accepted[1])) {
                    continue;
                }

                if ($matching['q'] < $acceptedRange['q']) {
                    $matching          = $acceptedRange;
                    $matching['token'] = $range;
                }
            }
        }

        return $matching;
    }

    /**
     * @param   string $header The header content
     *
     * @return  array
     */
    private function parseHeader($header)
    {
        $directives     = preg_split('~\s*,\s*~', $header);
        $acceptedRanges = [];

        foreach ($directives as $directive) {
            $parts = preg_split('~\s*;\s*~', $directive);
            $spec  = ['token' => array_shift($parts)];

            while (!empty($parts)) {
                $parts2 = preg_split('~\s*=\s*~', array_shift($parts));

                if (!isset($parts2[1])) {
                    $parts2[1] = true;
                }

                $spec[$parts2[0]] = $parts2[1];
            }

            if (!isset($spec['q'])) {
                $spec['q'] = 1.0;
            }

            $spec['q'] += count($spec) / 100;

            $acceptedRanges[] = $spec;
        }

        return $acceptedRanges;
    }

    /**
     * @param   string  $value  A token
     *
     * @return  array
     */
    private function split($value)
    {
        $result = preg_split($this->separator, $value, 2);

        if (!isset($result[1])) {
            $result[1] = $this->wildcard;
        }

        return $result;
    }

    /**
     * @param   string $var1 First string
     * @param   string $var2 Second string
     *
     * @return  boolean
     */
    private function match($var1, $var2)
    {
        return $var1 == $this->wildcard || $var2 == $this->wildcard || $var1 == $var2;
    }
}
