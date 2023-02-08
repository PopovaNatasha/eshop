<?php

namespace Eshop\src\Service;


use Eshop\src\Repositories\ProductRepository;
use Eshop\src\Repositories\TagRepository;

class MainService
{
	public static function getProductList(int $tag = null, string $search = "", int $page = null): array
	{
		return (new ProductRepository())->getList($tag, $search, $page);
	}


	public static function getTagsList(): array
	{
		return (new TagRepository())->getList();
	}
}