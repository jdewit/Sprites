<?php

/*
 * This file is part of the Fermio Sprites package.
 *
 * (c) Pierre Minnieur <pierre@ferm.io>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Fermio\Sprites\Tests;

use Fermio\Sprites\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessorIsGuessedButCouldBeOverwritten()
    {
        $config = new Configuration;
        $this->assertEquals(Configuration::PROCESSOR_DYNAMIC, $config->getProcessor());

        $config->setWidth(16);
        $this->assertEquals(Configuration::PROCESSOR_FIXED, $config->getProcessor());

        $config->setProcessor('test');
        $this->assertEquals('test', $config->getProcessor());
    }
}
