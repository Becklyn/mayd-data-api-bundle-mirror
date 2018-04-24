<?php declare(strict_types=1);

namespace Mayd\DataApiBundle;

use Mayd\DataApiBundle\DependencyInjection\MaydDataApiExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class MaydDataApiBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function getContainerExtension ()
    {
        return new MaydDataApiExtension();
    }

}
