<?php
namespace ZealByte\Bundle\CatalogBundle\Controller
{
	use Exception;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use ZealByte\Bundle\PlatformBundle\Controller\ContextControllerTrait;
	use ZealByte\Catalog\Component\DatatableComponent;
	use ZealByte\Bundle\CatalogBundle\Zcat;


	/**
	 * Class CatalogController.
	 *
	 * @author  Phil Martella <philmartella@live.com>
	 */
	class CatalogController extends Controller
	{
		use ContextControllerTrait;

		/**
		 *
		 * $catalogRegistry = $this->get(Zcat::SERVICE_SPEC_REGISTRY);
		 * $catalogFactory = $this->get(Zcat::SERVICE_CATALOG_FACTORY);
		 * $component = new DatatableComponent($catalogFactory, $catalogSpec, $request, $alias, $category);
		 */
		public function indexAction (Request $request, string $category, string $alias)
		{
			$title = $this->get('translator')->trans($alias);

			try {
				$component = new DatatableComponent($alias, $category);

				return $this->createContext($request, $component, [
					'title' => $title,
					'catalog_category' => $category,
					'catalog_alias' => $alias,
				]);
			}
			catch (\Exception $e) {
				throw new NotFoundHttpException("The $title catalog does not exist, or cannot be loaded!", $e);
			}
		}

		public function relatedAction (Request $request, string $category, string $alias, string $identifier, string $related_alias)
		{
			$title = $this->get('translator')->trans($related_alias);

			try {
				$component = (new DatatableComponent($related_alias, $category))
					->setRelationshipAlias($alias)
					->setRelationshipId($identifier)
					->setRequest($request);

				return $this->createContext($request, $component, [
					'title' => $title,
				]);
			}
			catch (Exception $e) {
				throw new NotFoundHttpException("The $title catalog does not exist, or cannot be loaded!", $e);
			}
		}

		public function autofillAction (Request $request, string $category, string $alias)
		{
			$title = $this->get('translator')->trans($alias);

			try {
				$component = new InventoryListComponent($alias, $category);

				return $this->createContext($request, $component, [
					'title' => $title,
					'catalog_category' => $category,
					'catalog_alias' => $alias,
				]);
			}
			catch (Exception $e) {
				throw new NotFoundHttpException("The $title catalog does not exist, or cannot be loaded!", $e);
			}
		}

	}
}
