<?php
namespace ZealByte\Bundle\CatalogBundle\DependencyInjection\CompilerPass
{
	use InvalidArgumentException;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
	use Symfony\Component\DependencyInjection\Reference;
	use ZealByte\Catalog\Inventory\CatalogIndexInterface;
	use ZealByte\Catalog\Data\Type\DataTypeInterface;
	use ZealByte\Catalog\Column\Type\ColumnTypeInterface;
	use ZealByte\Bundle\CatalogBundle\Zcat;

	/**
	 * CatalogTypesPass
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class CatalogTypesPass implements CompilerPassInterface
	{
		/*
		 * {@inheritdoc}
		 */
		public function process (ContainerBuilder $container)
		{
			$dataTypeTaggedServices = $container->findTaggedServiceIds(Zcat::TAG_DATA_TYPE);
			$columnTypeTaggedServices = $container->findTaggedServiceIds(Zcat::TAG_COLUMN_TYPE);

			foreach ($dataTypeTaggedServices as $id => $tags)
				$this->addDataType($container, $id, $tags);

			foreach ($columnTypeTaggedServices as $id => $tags)
				$this->addColumnType($container, $id, $tags);
		}

		protected function addDataType (ContainerBuilder $container, string $id, array $tags)
		{
			$definition = $container->getDefinition($id);
			$class = $container->getParameterBag()->resolveValue($definition->getClass());

			if (!is_subclass_of($class, DataTypeInterface::class))
				throw new InvalidArgumentException("Service \"$id\" must implement ".DataTypeInterface::class.".");

			$definition->setLazy(true);

			$services = [
				$this->getRegistryDefinition($container),
			];

			foreach ($tags as $attributes)
				$this->addDataTypeServices($id, $attributes, $services);
		}

		protected function addColumnType (ContainerBuilder $container, string $id, array $tags)
		{
			$definition = $container->getDefinition($id);
			$class = $container->getParameterBag()->resolveValue($definition->getClass());

			if (!is_subclass_of($class, ColumnTypeInterface::class))
				throw new InvalidArgumentException("Service \"$id\" must implement ".ColumnTypeInterface::class.".");

			$definition->setLazy(true);

			$services = [
				$this->getRegistryDefinition($container),
			];

			foreach ($tags as $attributes)
				$this->addColumnTypeServices($id, $attributes, $services);
		}

		private function addDataTypeServices (string $data_type_id, array $attributes, array $services)
		{
			foreach ($services as $service)
				if (!is_subclass_of($service, CatalogIndexInterface::class))
					$service->addMethodCall('addDataType', [$data_type_id, new Reference($data_type_id), $attributes]);
		}

		private function addColumnTypeServices (string $column_type_id, array $attributes, array $services)
		{
			foreach ($services as $service)
				if (!is_subclass_of($service, CatalogIndexInterface::class))
					$service->addMethodCall('addColumnType', [$column_type_id, new Reference($column_type_id), $attributes]);
		}

		private function getRegistryDefinition (ContainerBuilder $container)
		{
			if (!$container->hasDefinition(Zcat::SERVICE_CATALOG_INDEX))
				throw new InvalidArgumentException("ZealByte Catalog requires ".Zcat::SERVICE_CATALOG_INDEX);

			return $container->getDefinition(Zcat::SERVICE_CATALOG_INDEX);
		}

	}
}
