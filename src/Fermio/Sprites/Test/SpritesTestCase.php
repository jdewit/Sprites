<?php

/*
 * This file is part of the Fermio Sprites package.
 *
 * (c) Pierre Minnieur <pierre@ferm.io>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Fermio\Sprites\Test;

use Fermio\Sprites\Test\Constraint\IsImageEqual;
use Imagine\Gd;
use Imagine\Gmagick;
use Imagine\Imagick;
use Imagine\Image\Color;

class SpritesTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->path = sys_get_temp_dir().'/falsep/sprites';

        $this->createDirectory($this->path);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->clearDirectory($this->path);
    }

    /**
     * Creates the given directory.
     *
     * @param  string $directory
     * @return void
     *
     * @throws \RuntimeException
     */
    protected function createDirectory($directory)
    {
        if (!is_dir($directory = dirname($directory)) && false === @mkdir($directory, 0777, true)) {
            throw new \RuntimeException(sprintf('Unable to create directory "%s".', $directory));
        }
    }

    /**
     * Clears the given directory.
     *
     * @param  string $directory
     * @return void
     */
    protected function clearDirectory($directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        $fp = opendir($directory);
        while (false !== $file = readdir($fp)) {
            if (!in_array($file, array('.', '..'))) {
                if (is_link($directory.'/'.$file)) {
                    unlink($directory.'/'.$file);
                } elseif (is_dir($directory.'/'.$file)) {
                    $this->clearDirectory($directory.'/'.$file);
                    rmdir($directory.'/'.$file);
                } else {
                    unlink($directory.'/'.$file);
                }
            }
        }

        closedir($fp);
    }

    /**
     * Returns an ImagineInterface instance.
     *
     * @param  string           $driver (optional)
     * @return ImagineInterface
     */
    protected function getImagine($driver = null)
    {
        if (null === $driver) {
            switch (true) {
                case function_exists('gd_info'):
                    $driver = 'gd';
                    break;
                case class_exists('Gmagick'):
                    $driver = 'gmagick';
                    break;
                case class_exists('Imagick'):
                    $driver = 'imagick';
                    break;
            }
        }

        if (!in_array($driver, array('gd', 'gmagick', 'imagick'))) {
            throw new \RuntimeException(sprintf('Driver "%s" does not exist.', $driver));
        }

        switch (strtolower($driver)) {
            case 'gd':
                return new Gd\Imagine();
            case 'gmagick':
                return new Gmagick\Imagine();
            case 'imagick':
                return new Imagick\Imagine();
        }
    }

    /**
     * Returns a Color instance.
     *
     * @param  array|string $color (optional)
     * @param  integer      $alpha (optional)
     * @return Color
     */
    protected function getColor($color = array(255, 255, 255), $alpha = 100)
    {
        return new Color($color, $alpha);
    }

    /**
     * Asserts that two images are equal using color histogram comparison method
     *
     * @param ImageInterface $expected
     * @param ImageInterface $actual
     * @param string         $message
     * @param float          $delta
     * @param integer        $buckets
     */
    public static function assertImageEquals($expected, $actual, $message = '', $delta = 0.1, $buckets = 4)
    {
        $constraint = new IsImageEqual($expected, $delta, $buckets);

        self::assertThat($actual, $constraint, $message);
    }
}
