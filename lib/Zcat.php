<?php
namespace ZealByte\Bundle\CatalogBundle
{
	class Zcat
	{
		const TAG_DATA_TYPE = 'catalog.data_type';

		const TAG_COLUMN_TYPE = 'catalog.column_type';

		const TAG_SPEC = 'catalog.spec';

		const ROUTE_CATALOG = 'catalog';

		const SERVICE_CONTEXT_TAGS = 'context_tags';

		const SERVICE_CATALOG_FACTORY = 'catalog.factory';

		const SERVICE_DATATABLE_PROCESSOR = 'catalog.datatable_processor';

		const SERVICE_TRANSLATOR = 'translator';

		const SERVICE_SPEC_REGISTRY = 'catalog.registry';

		const SERVICE_CATALOG_INDEX = 'ZealByte\Catalog\Inventory\CatalogIndex';

		const SERVICE_CATALOG_REGISTRY = 'ZealByte\Catalog\Inventory\SpecRegistry';

		const SERVICE_MENU_BUILDER = 'ZealByte\Bundle\CatalogBundle\Menu\CatalogMenuBuilder';
	}
}
