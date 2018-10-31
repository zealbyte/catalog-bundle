<?php
namespace ZealByte\Bundle\CatalogBundle\DependencyInjection\CompilerPass
{
	use InvalidArgumentException;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
	use Symfony\Component\DependencyInjection\Reference;
	use ZealByte\Catalog\SpecInterface;
	use ZealByte\Bundle\CatalogBundle\Zcat;

	/**
	 * CatalogComplerPass
	 *
	 * @author  Phil Martella <philmartella@live.com>
	 */
	class SpecRegistryPass implements CompilerPassInterface
	{
		/*
		 * {@inheritdoc}
		 */
		public function process (ContainerBuilder $container)
		{
			$taggedServices = $container->findTaggedServiceIds(Zcat::TAG_SPEC);

			foreach ($taggedServices as $id => $tags)
				$this->addSpec($container, $id, $tags);
		}

		protected function addSpec (ContainerBuilder $container, string $id, array $tags)
		{
			$definition = $container->getDefinition($id);
			$class = $container->getParameterBag()->resolveValue($definition->getClass());

			if (!is_subclass_of($class, SpecInterface::class))
				throw new InvalidArgumentException("Service \"$id\" must implement ".SpecInterface::class.".");

			$definition->setLazy(true);

			$services = [
				$this->getRegistryDefinition($container),
			];

			foreach ($tags as $attributes)
				$this->addSpecServices($id, $attributes, $services);
		}


		private function addSpecServices (string $spec_id, array $attributes, array $services)
		{
			if (empty($attributes['alias']))
				throw new InvalidArgumentException("Catalog spec $spec_id is missing an \"alias\" tag attribute.");

			if (empty($attributes['category']))
				throw new InvalidArgumentException("Catalog spec $spec_id is missing a \"category\" tag attribute.");

			$alias = $attributes['alias'];
			$category = $attributes['category'];

			// @todo Check if service inherits spec container interface
			foreach ($services as $service)
				if ($service)
					$service->addMethodCall('addSpec', [new Reference($spec_id), $alias, $category, $attributes]);
		}

		private function getRegistryDefinition (ContainerBuilder $container)
		{
			if (!$container->hasDefinition(Zcat::SERVICE_CATALOG_REGISTRY))
				throw new InvalidArgumentException("ZealByte Catalog requires ".Zcat::SERVICE_CATALOG_REGISTRY);

			return $container->getDefinition(Zcat::SERVICE_CATALOG_REGISTRY);
		}

	}
}
