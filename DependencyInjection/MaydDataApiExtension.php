<?php declare(strict_types=1);

namespace Mayd\DataApiBundle\DependencyInjection;

use Mayd\DataApiBundle\Api\DataApi;
use Mayd\DataApiBundle\Encryption\DataApiEncryption;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class MaydDataApiExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load (array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new MaydDataApiConfiguration(), $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . "/../Resources/config")
        );
        $loader->load("services.yaml");

        $container->getDefinition(DataApiEncryption::class)
            ->setArgument('$secret', $config['secret']);

        $container->getDefinition(DataApi::class)
            ->setArgument('$project', $config['project'])
            ->setArgument('$endpointUrl', $config['endpoint']);
    }
}
