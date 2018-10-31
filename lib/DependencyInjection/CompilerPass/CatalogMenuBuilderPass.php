<?php
namespace ZealByte\Bundle\CatalogBundle\DependencyInjection\CompilerPass
{
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
	use Symfony\Component\DependencyInjection\Definition;
	use Symfony\Component\DependencyInjection\Reference;
	use ZealByte\Bundle\CatalogBundle\Zcat;

	/**
	 * CatalogComplerPass
	 *
	 * @author  Phil Martella <philmartella@live.com>
	 */
	class CatalogMenuBuilderPass implements CompilerPassInterface
	{
		/**
		 * {@inheritdoc}
		 */
		public function process (ContainerBuilder $container)
		{
			if ($container->hasDefinition(Zcat::SERVICE_MENU_BUILDER))
				$this->processBuilder($container);
		}

		private function processBuilder (ContainerBuilder $container)
		{
			$taggedServices = $container->findTaggedServiceIds(Zcat::TAG_SPEC);

			foreach ($taggedServices as $id => $tags)
				$this->addCatalogMenuBuilder($container, $id, $tags);
		}

		private function addCatalogMenuBuilder (ContainerBuilder $container, string $id, array $tags)
		{
			$menuBuilderDefinition = $container->getDefinition(Zcat::SERVICE_MENU_BUILDER);

			foreach ($tags as $attributes)
				$this->addCatalogMenuBuilderItem($id, $attributes, $menuBuilderDefinition);
		}

		private function addCatalogMenuBuilderItem (string $spec_id, array $attributes, Definition $menu_builder_definition)
		{
			$attributes = $this->saneAttributes($attributes, $spec_id);

			$alias = $attributes['alias'];
			$category = $attributes['category'];
			$menu = $attributes['menu'];

			if ($menu)
				$menu_builder_definition->addMethodCall('addCatalogMenu', [$alias, $category, $menu]);
		}

		private function saneAttributes (array $attributes, string $spec_id)
		{
			if (empty($attributes['alias']))
				throw new InvalidArgumentException("Catalog spec $spec_id is missing an \"alias\" tag attribute.");

			if (empty($attributes['category']))
				throw new InvalidArgumentException("Catalog spec $spec_id is missing a \"category\" tag attribute.");

			if (empty($attributes['menu']))
				$attributes['menu'] = null;
				//$attributes['menu'] = $attributes['alias'];

			return $attributes;
		}

	}
}
