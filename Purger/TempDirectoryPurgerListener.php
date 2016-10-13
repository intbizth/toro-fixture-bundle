<?php

namespace Toro\Bundle\FixtureBundle\Purger;

use Sylius\Bundle\FixturesBundle\Listener\AbstractListener;
use Sylius\Bundle\FixturesBundle\Listener\AfterSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Symfony\Component\Filesystem\Filesystem;

final class TempDirectoryPurgerListener extends AbstractListener implements AfterSuiteListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'temp_directory_purger';
    }

    /**
     * {@inheritdoc}
     */
    public function afterSuite(SuiteEvent $suiteEvent, array $options)
    {
        $dir = '/tmp/toro-fixtures';

        if (is_dir($dir)) {
            (new Filesystem())->remove($dir);
        }
    }
}
