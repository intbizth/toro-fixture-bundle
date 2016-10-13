<?php

namespace Toro\Bundle\FixtureBundle\DataFixture\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AdminUserExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var RepositoryInterface
     */
    private $roleRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $userFactory
     */
    public function __construct(FactoryInterface $userFactory)
    {
        $this->userFactory = $userFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('email', function (Options $options) {
                    return $this->faker->email;
                })
                ->setDefault('username', function (Options $options) {
                    return $this->faker->userName;
                })
                ->setDefault('first_name', function (Options $options) {
                    return $this->faker->firstName;
                })
                ->setDefault('last_name', function (Options $options) {
                    return $this->faker->lastName;
                })
                ->setDefault('enabled', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setAllowedTypes('enabled', 'bool')
                ->setDefault('password', 'password123')
                ->setDefault('api', false)
                ->setDefault('root', false)
                ->setDefault('locale_code', 'th_TH')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create($key, array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        $user = $this->userFactory->createNew();
        $user->setEmail($options['email']);
        $user->setUsername($options['username']);
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);
        $user->setFirstName($options['first_name']);
        $user->setLastName($options['last_name']);
        $user->setLocaleCode($options['locale_code']);

        $user->addRole('ROLE_ADMINISTRATION_ACCESS');

        if ($options['api']) {
            $user->addRole('ROLE_API_ACCESS');
        }

        if ($options['root']) {
            $user->addRole('ROLE_ROOT');
        }

        return $user;
    }
}
