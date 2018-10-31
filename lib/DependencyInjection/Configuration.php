<?php
namespace ZealByte\Bundle\CatalogBundle\DependencyInjection
{
	use Symfony\Component\Config\Definition\Builder\TreeBuilder;
	use Symfony\Component\Config\Definition\ConfigurationInterface;

	/**
	 * This class contains the configuration information for the bundle.
	 *
	 * This information is solely responsible for how the different configuration
	 * sections are normalized, and merged.
	 *
	 * @author  Phil Martella <philmartella@live.com>
	 */
	class Configuration implements ConfigurationInterface
	{
		/**
		 * Generates the configuration tree.
		 *
		 * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
		 */
		public function getConfigTreeBuilder ()
		{
			$treeBuilder = new TreeBuilder();
			$rootNode = $treeBuilder->root('catalog');

			return $treeBuilder;
		}

	}
}
