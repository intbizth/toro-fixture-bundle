<?php

namespace Toro\Bundle\FixtureBundle\DataFixture\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Toro\Bundle\FixtureBundle\StringInflector;

final class TaxonExampleFactory extends AbstractLocaleAwareFactory
{
    /**
     * @var FactoryInterface
     */
    private $taxonFactory;

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $taxonFactory
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(
        FactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $this->taxonFactory = $taxonFactory;
        $this->taxonRepository = $taxonRepository;
        $this->faker = \Faker\Factory::create();

        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('name', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('slug', function (Options $options) {
                    return $options['name'];
                })
                ->setDefault('code', function (Options $options) {
                    return StringInflector::nameToCode($options['name']);
                })
                ->setDefault('description', function (Options $options) {
                    return $this->faker->paragraph;
                })
                ->setDefault('children', [])
                ->setAllowedTypes('children', ['array'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create($key, array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneBy(['code' => $options['code']]);

        if (null === $taxon) {
            $taxon = $this->taxonFactory->createNew();
        }

        $taxon->setCode($options['code']);

        $this->setLocalizedData($taxon, ['name', 'slug', 'description'], $options);

        foreach ($options['children'] as $key => $childOptions) {
            $taxon->addChild($this->create($key, $childOptions));
        }

        return $taxon;
    }
}
