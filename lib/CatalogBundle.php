<?php
namespace ZealByte\Bundle\CatalogBundle
{
	use Symfony\Component\HttpKernel\Bundle\Bundle;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Compiler\PassConfig;
	use ZealByte\Bundle\CatalogBundle\DependencyInjection\CompilerPass\CatalogMenuBuilderPass;
	use ZealByte\Bundle\CatalogBundle\DependencyInjection\CompilerPass\SpecRegistryPass;
	use ZealByte\Bundle\CatalogBundle\DependencyInjection\CompilerPass\CatalogTypesPass;

	/**
	 * Class ZealByteDataBundle.
	 */
	class CatalogBundle extends Bundle
	{
    /**
     * Boots the Bundle.
     */
    public function boot ()
    {
    }

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown ()
    {
    }

    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     */
		public function build (ContainerBuilder $container)
		{
			parent::build($container);

			$container->addCompilerPass(new CatalogTypesPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 6);
			$container->addCompilerPass(new SpecRegistryPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 6);
			$container->addCompilerPass(new CatalogMenuBuilderPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 3);
		}

	}
}
