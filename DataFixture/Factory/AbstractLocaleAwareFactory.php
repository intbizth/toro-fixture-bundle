<?php

namespace Toro\Bundle\FixtureBundle\DataFixture\Factory;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractLocaleAwareFactory implements ExampleFactoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @param RepositoryInterface $localeRepository
     */
    public function setLocaleRepository(RepositoryInterface$localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param string $defaultLocale
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @return array
     */
    protected function getLocales()
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();

        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    /**
     * @param TranslatableInterface $object
     * @param array $properties
     * @param array $data
     */
    protected function setLocalizedData(TranslatableInterface $object, array $properties, array $data)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->getLocales() as $localeCode) {
            $object->setCurrentLocale($localeCode);
            $object->setFallbackLocale($localeCode);

            foreach ($properties as $property) {
                $value = $data[$property];
                $value = is_string($value)
                    ? $value
                    : isset($value[$localeCode])
                        ? $value[$localeCode]
                        : $value[$this->defaultLocale]
                ;

                $accessor->setValue($object, $property, $value);
            }
        }
    }
}
