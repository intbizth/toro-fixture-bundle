<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Toro\Bundle\FixtureBundle\DataFixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleFixture extends AbstractFixture
{
    /**
     * @var FactoryInterface
     */
    private $localeFactory;

    /**
     * @var ObjectManager
     */
    private $localeManager;

    /**
     * @var string
     */
    private $baseLocaleCode;

    /**
     * @param FactoryInterface $localeFactory
     * @param ObjectManager $localeManager
     * @param string $baseLocaleCode
     */
    public function __construct(FactoryInterface $localeFactory, ObjectManager $localeManager, $baseLocaleCode)
    {
        $this->localeFactory = $localeFactory;
        $this->localeManager = $localeManager;
        $this->baseLocaleCode = $baseLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $localesCodes = array_merge([$this->baseLocaleCode => true], $options['locales']);

        foreach ($localesCodes as $localeCode => $enabled) {
            if (!$enabled) {
                continue;
            }

            /** @var LocaleInterface $locale */
            $locale = $this->localeFactory->createNew();

            $locale->setCode($localeCode);

            $this->localeManager->persist($locale);
        }

        $this->localeManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'locale';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->arrayNode('locales')
                    ->prototype('scalar')
        ;
    }
}
