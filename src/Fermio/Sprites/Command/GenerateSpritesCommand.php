<?php

/*
 * This file is part of the Fermio Sprites package.
 *
 * (c) Pierre Minnieur <pierre@ferm.io>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Fermio\Sprites\Command;

use Fermio\Sprites\Configuration;
use Imagine\Gd;
use Imagine\Gmagick;
use Imagine\Imagick;
use Imagine\Image\Color;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

abstract class GenerateSpritesCommand extends Command
{
    /**
     * Returns a Configuration instance.
     *
     * @param  InputInterface $input
     * @return Configuration
     */
    protected function getConfiguration(InputInterface $input)
    {
        $configuration = new Configuration();

        $configuration->setImagine($this->getImagine($input->getOption('driver')));
        $configuration->setOptions($input->getOption('options'));

        $configuration->getFinder()
               ->name($input->getArgument('pattern'))
               ->in($input->getArgument('source'));

        $configuration->setImage($input->getArgument('image'));
        $configuration->setColor(new Color($input->getOption('color'), $input->getOption('alpha')));
        $configuration->setStylesheet($input->getArgument('stylesheet'));
        $configuration->setSelector($input->getArgument('selector'));

        return $configuration;
    }

    /**
     * Returns an ImagineInterface instance.
     *
     * @param  string            $driver (optional)
     * @return ImagineInterface
     * @throws \RuntimeException
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
     * Returns the Imagine class name.
     *
     * @param  string            $driver (optional)
     * @return string Imagine class
     * @throws \RuntimeException
     */
    public static function getImagineClass($driver = null)
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
				return 'Imagine\Gd\Imagine';
            case 'gmagick':
                return 'Imagine\Gmagick\Imagine';
            case 'imagick':
                return 'Imagine\Imagick\Imagine';
        }
    }
}
