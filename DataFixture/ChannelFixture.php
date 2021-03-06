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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'channel';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('theme_name')->end()
                ->scalarNode('hostname')->cannotBeEmpty()->end()
                ->scalarNode('color')->cannotBeEmpty()->end()
                ->scalarNode('defaultLocale')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->arrayNode('locales')->prototype('scalar')->end()->end()
                ->arrayNode('taxons')->prototype('scalar')->end()->end()
        ;
    }
}
