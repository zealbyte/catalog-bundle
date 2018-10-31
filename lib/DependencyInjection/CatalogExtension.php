<?php
namespace ZealByte\Bundle\CatalogBundle\DependencyInjection
{
	use ReflectionClass;
	use Symfony\Component\Config\Definition\Processor;
	use Symfony\Component\Config\FileLocator;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Definition;
	use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
	use Symfony\Component\DependencyInjection\Loader;
	use Symfony\Component\DependencyInjection\Reference;
	use Symfony\Component\HttpKernel\DependencyInjection\Extension;
	use ZealByte\Catalog\Catalog;

	/**
	 * CatalogExtension.
	 *
	 * @author  Phil Martella <philmartella@live.com>
	 */
	class CatalogExtension extends Extension implements PrependExtensionInterface
	{
		/**
		 * Loads the configuration.
		 *
		 * @param array            $configs   An array of configuration settings
		 * @param ContainerBuilder $container A ContainerBuilder instance
		 */
		public function load (array $configs, ContainerBuilder $container)
		{
			$processor = new Processor();
			$configuration = new Configuration();
			$loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

			$loader->load('catalog.xml');
			$loader->load('form.xml');
			$loader->load('twig.xml');

			$processor->processConfiguration($configuration, $configs);
		}

		/**
		 * {@inheritdoc}
		 */
		public function prepend (ContainerBuilder $container)
		{
			if ($container->hasExtension('platform'))
				$this->prependPlatform($container);

			if ($container->hasExtension('twig'))
				$this->prependTwig($container);
		}

		private function prependPlatform (ContainerBuilder $container) : void
		{
			$config = [
				'packages' => [
					[
						'name' => "zealbyte.catalog",
						'version' => "1.1-rc",
						'baseurl' => "/",
						'basedir' => "/bundles/catalog",
						'files' => [
							'js/jquery.zdo_datatable.js',
							'js/zdo_datatable.js'
						],
						'dependencies' => [
							'zealbyte.platform',
						],
					],
				],
			];

			$container->prependExtensionConfig('platform', $config);
		}

		private function prependTwig (ContainerBuilder $container) : void
		{
			$config = [
				'paths' => [
					dirname((new ReflectionClass(Catalog::class))->getFileName()).'/Resources/views' => 'Catalog',
				],
			];

			$container->prependExtensionConfig('twig', $config);
		}

	}
}
