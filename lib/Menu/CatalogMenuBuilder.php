<?php
namespace ZealByte\Bundle\CatalogBundle\Menu
{
	use InvalidArgumentException;
	use Symfony\Component\Translation\TranslatorInterface;
	use Knp\Menu\FactoryInterface;
	use Knp\Menu\MenuItem;
	use ZealByte\Bundle\MenuBundle\Builder\MenuBuilderInterface;

	class CatalogMenuBuilder implements MenuBuilderInterface
	{
		private $menuName;

		private $translator;

		private $catalogs = [];

		private $children = [];

		/**
		 * @param TranslatorInterface $translator
		 */
		public function __construct (TranslatorInterface $translator = null)
		{
			if ($translator)
				$this->setTranslator($translator);
		}

		/**
		 * {@inheritdoc}
		 */
		public function build (FactoryInterface $factory, string $name, array $options = []) : MenuItem
		{
			$category = $this->discoverBasename($name);
			$menuItem = $this->statMenu($factory, $category, $options);

			$this->buildCatalogMenus($menuItem, $category, $options);

			return $menuItem;
		}

		/**
		 * {@inheritdoc}
		 */
		public function has (string $name) : bool
		{
			$name = $this->discoverBasename($name);

			return array_key_exists($name, $this->catalogs);
		}

		/**
		 * {@inheritdoc}
		 */
		public function setMenuName (string $name) : MenuBuilderInterface
		{
			$this->menuName = $name;

			return $this;
		}

		public function setTranslator (TranslatorInterface $translator) : self
		{
			$this->translator = $translator;

			return $this;
		}

		public function addCatalogMenu (string $alias, string $category = null, string $menu_path = null) : self
		{
			if (empty($menu_path))
				$menu_path = $alias;

			if (!$category)
				$category = 0;

			if (!array_key_exists($category, $this->catalogs))
				$this->catalogs[$category] = [];

			$this->catalogs[$category][$alias] = [
				'alias' => $alias,
				'menu_path' => $menu_path,
			];

			return $this;
		}

		private function statMenu (FactoryInterface $factory, string $category, array $options) : MenuItem
		{
			if (!array_key_exists($category, $this->children))
				$this->children[$category] = $factory->createItem($category, array_merge([
					'label' => $this->findLabel($category)
				], $options));

			return $this->children[$category];
		}

		private function buildCatalogMenus (MenuItem $menu_item, string $category, array $options) : void
		{
			foreach ($this->catalogs[$category] as $catalog) {
				$alias = $catalog['alias'];
				$menuPath = $catalog['menu_path'];

				if (!array_key_exists('translation_domain', $options))
					$options['translation_domain'] = null;

				$this->buildCatalogMenusItem($menu_item, $alias, $category, $menuPath, $options);
			}
		}

		private function buildCatalogMenusItem (MenuItem $menu_item, string $alias, string $category, string $menu_path, array $options = []) : void
		{
			$hierarchy = explode('/', $menu_path);
			$child = array_pop($hierarchy);
			$parent = $this->buildCatalogMenuItemParents($category, $hierarchy, $options);

			$this->buildCatalogMenuItemChild($parent, $child, $alias, $category, $options);
		}

		private function buildCatalogMenuItemParents (string $category, array $hierarchy, array $options = [])
		{
			$parent = $category;

			foreach ($hierarchy as $hierarch) {
				$child = "$parent.$hierarch";

				if (empty($this->children[$child])) {
					$options = array_merge([
						'label' => $this->findLabel($hierarch),
					], $options);

					$this->children[$child] = $this->children[$parent]->addChild($hierarch, $options);
				}

				$parent = $child;
			}

			return $parent;
		}

		private function buildCatalogMenuItemChild (string $parent, string $child, string $alias, string $category, array $options = [])
		{
			$hierarch = $child;
			$child = "$parent.$child";

			if (array_key_exists($child, $this->children))
				throw new InvalidArgumentException("An catalog with alias \"$alias\" already defined in the \"$category\" category.");

			$options = array_merge([
				'label' => $this->findLabel($hierarch, $options['translation_domain']),
				'route' => 'catalog.inventory',
				'routeParameters' => [
					'category' => $category,
					'alias' => $alias
				],
			], $options);

			$this->children[$child] = $this->children[$parent]->addChild($hierarch, $options);
		}

		private function discoverBasename (string $name) : string
		{
			$names = explode('.', $name);

			if ($names > 1) {
				$menuName = array_shift($names);

				if ($menuName == $this->menuName)
					$name = implode('.', $names);
			}

			return $name;
		}

		private function findLabel (string $name, string $translation_domain = null) : string
		{
			return $this->translator ? $this->translator->trans("$this->menuName.$name", [], $translation_domain) : $name;
		}

	}
}
