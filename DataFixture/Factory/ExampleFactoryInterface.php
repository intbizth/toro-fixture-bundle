<?php

namespace Toro\Bundle\FixtureBundle\DataFixture\Factory;

interface ExampleFactoryInterface
{
    /**
     * @param string $key
     * @param array $options
     *
     * @return object
     */
    public function create($key, array $options = []);
}
