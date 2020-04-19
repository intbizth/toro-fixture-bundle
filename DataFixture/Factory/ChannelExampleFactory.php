<?php

namespace Toro\Bundle\FixtureBundle\DataFixture\Factory;

use Toro\Bundle\FixtureBundle\DataFixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Toro\Bundle\AdminBundle\Sylius\Formatter\StringInflector;
use Toro\Bundle\AdminBundle\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChannelExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param ChannelFactoryInterface $channelFactory
     * @param RepositoryInterface $taxonRepository
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        ChannelFactoryInterface $channelFactory,
        RepositoryInterface $taxonRepository,
        RepositoryInterface $localeRepository
    ) {
        $this->channelFactory = $channelFactory;
        $this->faker = \Faker\Factory::create();

        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('name', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('code', function (Options $options) {
                    return StringInflector::nameToCode($options['name']);
                })
                ->setDefault('theme_name', function (Options $options) {
                    return $options['theme_name'];
                })
                ->setDefault('hostname', function (Options $options) {
                    return $options['code'] . '.localhost';
                })
                ->setDefault('color', function (Options $options) {
                    return $this->faker->colorName;
                })
                ->setDefault('enabled', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setAllowedTypes('enabled', 'bool')
                ->setDefault('defaultLocale', LazyOption::findOneBy($localeRepository, 'th_TH'))
                ->setNormalizer('defaultLocale', LazyOption::findOneBy($localeRepository, 'code'))


                ->setDefault('locales', LazyOption::all($localeRepository))
                ->setAllowedTypes('locales', 'array')
                ->setNormalizer('locales', LazyOption::findBy($localeRepository, 'code'))

                ->setDefault('taxons', LazyOption::all($taxonRepository))
                ->setAllowedTypes('taxons', 'array')
                ->setNormalizer('taxons', LazyOption::findBy($taxonRepository, 'code'))
            
                ->setDefault('settings', [])
                ->setAllowedTypes('settings', 'array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create($key, array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ChannelInterface $channel */
        $channel = $this->channelFactory->createNamed($options['name']);
        $channel->setCode($options['code']);
        $channel->setThemeName($options['theme_name']);
        $channel->setHostname($options['hostname']);
        $channel->setEnabled($options['enabled']);
        $channel->setColor($options['color']);
        $channel->setDefaultLocale($options['defaultLocale']);
        $channel->setSettings($options['settings']);

        foreach ($options['locales'] as $locale) {
            $channel->addLocale($locale);
        }

        foreach ($options['taxons'] as $taxon) {
            $taxon && $channel->addTaxon($taxon);
        }

        return $channel;
    }
}
