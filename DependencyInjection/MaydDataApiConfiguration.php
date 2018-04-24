<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class MaydDataApiConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder ()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("mayd_data_api");

        $rootNode
            ->children()
                ->scalarNode("project")
                    ->isRequired()
                ->end()
                ->scalarNode("secret")
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }

}
