<?php

namespace Eshop\src\Repositories;

use Eshop\Core\DB\Connection;
use Eshop\src\Models\TableField;
use Exception;

class AdminRepository
{

	public function getProdsByAdmin(): array
	{
		$List = [];
		$connection = Connection::getInstance()->getConnection();
		$Query = mysqli_query($connection, "
			SELECT p.ID,p.NAME, p.PRICE, p.RELEASE_DATE, a.NAME as ARTIST, p.COVER_STATUS, p.TRACKS, p.IS_ACTIVE, p.VINYL_STATUS_ID
			FROM product p
	        JOIN artist a on p.ARTIST_ID = a.ID
			ORDER BY p.ID;
		");
		if (!$Query)
		{
			throw new Exception(mysqli_error($connection));
		}

		while ($row = mysqli_fetch_assoc($Query))
		{
			$List[] = [
				(int)$row['ID'],
				$row['NAME'],
				$row['ARTIST'],
				$row['RELEASE_DATE'],
				(float)$row['PRICE'],
				$row['VINYL_STATUS_ID'],
				$row['COVER_STATUS'],
				$row['TRACKS'],
				"",
				(bool)$row['IS_ACTIVE'],
			];
		}

		$tableField = [
			new TableField('ID', 'id', 'ID'),
			new TableField('Название', 'text', 'NAME'),
			new TableField('Исполнитель', 'text', 'ARTIST'),
			new TableField('Дата релиза', 'number', 'RELEASE_DATE'),
			new TableField('Цена', 'number', 'PRICE'),
			new TableField('Качество винила', 'select', 'VINIL_STATUS'),
			new TableField('Качество конверта', 'text', 'COVER_STATUS'),
			new TableField('Треки', 'text', 'TRACKS'),
			new TableField('Тэги', 'checkboxes', 'TAGS'),
			new TableField('Активен', 'bool', 'IS_ACTIVE'),
		];

		return [(array)$tableField, (array)$List];
	}

	public function addEmptyProduct(): array
	{
		$connection = Connection::getInstance()->getConnection();

		$queryProduct = "INSERT INTO product
			(NAME, ARTIST_ID, RELEASE_DATE, PRICE, VINYL_STATUS_ID, COVER_STATUS, 
			 TRACKS, IS_ACTIVE)
					VALUES ('Новый продукт',  1, '2000', 0,'VG+','Без недостатков','Нет', 0);";


		mysqli_begin_transaction($connection);
		$test = mysqli_query($connection, $queryProduct);
		if ($test)
		{
			$id = mysqli_insert_id($connection);
			$queryTag = "INSERT INTO product_tag
			(PRODUCT_ID, TAG_ID)
					VALUES ({$id}, 1);";
			$test = mysqli_query($connection, $queryTag);
			if(!$test)
			{
				mysqli_rollback($connection);
				return [];
			}
		}

		mysqli_commit($connection);
		return [$id, 'Новый продукт', 'Без исполнителя', '2000', 0, 'VG+', 'Без недостатков', 'Нет', 1, false];
		//ID, name, artist_id, release_date, price, vinyl_status, cover_status, tracks, tag, is_active
	}

	public function addEmptyTag(): array
	{
		$connection = Connection::getInstance()->getConnection();

		$queryTag = "INSERT INTO tag
		(NAME) 
				VALUES ('Новый тег');
		";

		mysqli_begin_transaction($connection);
		$Query = mysqli_query($connection, $queryTag);
		$id = mysqli_insert_id($connection);
		mysqli_commit($connection);

		return [$id, 'Новый тег'];
	}

	public function addEmptyOrder(): array
	{
		$connection = Connection::getInstance()->getConnection();
		$currentDate = date("Y-m-d H:i:s");

		$queryOrder = "INSERT INTO `order`
		(DATE, CUSTOMER_NAME, CUSTOMER_EMAIL, CUSTOMER_PHONE, STATUS) 
				VALUES ('$currentDate', 'Клиент', 'email@gmail.com', 88005553535, 'В обработке');
		";

		mysqli_begin_transaction($connection);
		$Query = mysqli_query($connection, $queryOrder);
		$id = mysqli_insert_id($connection);
		mysqli_commit($connection);
		return [$id, $currentDate, 'Клиент', 'email@gmail.com', 88005553535, '', 'В обработке'];
	}

	public function updateProduct($product): bool
	{
		$connection = Connection::getInstance()->getConnection();

		$productId = $product->getId();
		$productName = mysqli_real_escape_string($connection, $product->getName());
		$productArtist = mysqli_real_escape_string($connection, $product->getArtist());
		$productReleaseDate = $product->getReleaseDate();
		$productPrice = $product->getPrice();
		$productVinylStatus = mysqli_real_escape_string($connection, $product->getVinylStatus());
		$productCoverStatus = mysqli_real_escape_string($connection, $product->getCoverStatus());
		$productTracks = mysqli_real_escape_string($connection, implode(';', $product->getTracks()));
		$productIsActive = $product->getIsActive() ? 1 : 0;

		$queryArtist = "SELECT a.ID FROM artist a
			INNER JOIN product p on a.ID = p.ARTIST_ID
			WHERE a.NAME = '$productArtist';
		";
		$query = mysqli_query($connection, $queryArtist);

		$artistId = null;
		while ($row = mysqli_fetch_assoc($query))
		{
			$artistId = $row['ID'];
		}

		mysqli_begin_transaction($connection);
		if ($artistId === null)
		{
			$queryArtist = "INSERT INTO artist (NAME) 
			VALUES ('$productArtist');
            ";

			mysqli_query($connection, $queryArtist);
			$artistId = mysqli_insert_id($connection);
		}

		$queryProduct = "UPDATE product
			SET NAME = '$productName', 
			    ARTIST_ID = {$artistId}, 
			    RELEASE_DATE = '$productReleaseDate',
			    PRICE = {$productPrice}, 
			    VINYL_STATUS_ID = '$productVinylStatus', 
			    COVER_STATUS = '$productCoverStatus', 
			    TRACKS = '$productTracks', 
			    IS_ACTIVE = $productIsActive
			WHERE ID = {$productId}
			;";

		$test = mysqli_query($connection, $queryProduct);
		if (!$test)
		{
			mysqli_rollback($connection);
			return $test;
		}

		mysqli_commit($connection);
		return $test;
	}

	public function updateTag($tag): bool
	{
		$connection = Connection::getInstance()->getConnection();

		$tagId = $tag->getId();
		$tagName = $tag->getTitle();

		$queryTag = "UPDATE tag SET NAME = '$tagName' WHERE ID = {$tagId}";
		$test = mysqli_query($connection, $queryTag);

		return $test;
	}

	public function updateOrder($order): bool
	{
		$connection = Connection::getInstance()->getConnection();

		$orderId = (int)($order->getOrderId());
		$date = mysqli_real_escape_string($connection, $order->getCreatedAt()->format('Y-m-d H:i:s'));
		$orderCustomerName = mysqli_real_escape_string($connection, $order->getCustomerName());
		$orderCustomerEmail = mysqli_real_escape_string($connection, $order->getCustomerEmail());
		$orderCustomerPhone = mysqli_real_escape_string($connection, $order->getCustomerPhone());
		$orderComment = mysqli_real_escape_string($connection, $order->getComment());
		$orderStatus = mysqli_real_escape_string($connection, $order->getStatus());

		$queryTag = "UPDATE `order` 
			SET DATE = '$date', 
			    CUSTOMER_NAME = '$orderCustomerName', 
			    CUSTOMER_EMAIL = '$orderCustomerEmail',
				CUSTOMER_PHONE = '$orderCustomerPhone', 
				COMMENT = '$orderComment', 
				STATUS = '$orderStatus'
            WHERE ID = {$orderId};";

		$test = mysqli_query($connection, $queryTag);

		return $test;
	}

	public function deleteProduct($id): array
	{
		$connection = Connection::getInstance()->getConnection();
		$imageArray = [];

		$selectQuery = "
		SELECT i.NAME, i.PATH FROM image i
			INNER JOIN product_image pi on i.ID = pi.IMAGE_ID
		WHERE PRODUCT_ID = {$id};
		";

		$daleteTagQuery = "
			DELETE FROM product_tag
			WHERE PRODUCT_ID = {$id};
		";

		$daleteImageQuery ="
			DELETE FROM product_image
			WHERE PRODUCT_ID = {$id};
		";

		$deleteQuery = "
			DELETE FROM product
			WHERE ID = {$id};
		";

		mysqli_begin_transaction($connection);

		$test = mysqli_query($connection, $daleteTagQuery);
		if ($test)
		{
			$selectImage = mysqli_query($connection, $selectQuery);
			while ($row = mysqli_fetch_assoc($selectImage))
			{
				$imageArray[] = [
					'name' => $row['NAME'],
					'path' => $row['PATH']
				];
			}
			$test = mysqli_query($connection, $daleteImageQuery);

			if (!$test)
			{
				return [];
			}
			$test = mysqli_query($connection, $deleteQuery);

			if (!$test)
			{
				mysqli_rollback($connection);
				return [];
			}
		}

		mysqli_commit($connection);

		return ['images' => $imageArray, 'susses' => $test];
	}

	public function deleteTag($id): bool
	{
		$connection = Connection::getInstance()->getConnection();

		$deleteQuery = mysqli_query($connection, "
		DELETE FROM tag
		WHERE ID = {$id};
		");

		return $deleteQuery;
	}

	public function deleteOrder($id): bool
	{
		$connection = Connection::getInstance()->getConnection();

		$testId = [];
		$selectOrderItemQuery = "
		SELECT ID FROM product_order
		WHERE ORDER_ID = {$id};
		";

		$query = mysqli_query($connection, $selectOrderItemQuery);
		while ($row = mysqli_fetch_assoc($query))
		{
			$testId[] = $row['ID'];
		}
		$deleteOrderItemQuery = "
		DELETE FROM product_order
		WHERE ORDER_ID = {$id};
		";

		$deleteOrderQuery = "
		DELETE FROM `order`
		WHERE ID = {$id};
		";

		mysqli_begin_transaction($connection);
		if (empty($testId))
		{
			$test = mysqli_query($connection, $deleteOrderQuery);
		}
		else
		{
			$test = mysqli_query($connection, $deleteOrderItemQuery);
			if ($test)
			{
				$test = mysqli_query($connection, $deleteOrderQuery);
			}
		}
		if ($test)
		{
			mysqli_commit($connection);
		}

		mysqli_rollback($connection);
		return $test;
	}


	public function getTagsByAdmin(): array
	{
		$connection = Connection::getInstance()->getConnection();
		$Query = mysqli_query($connection, "
			SELECT t.ID, t.NAME
			FROM tag t
		");
		if (!$Query)
		{
			throw new Exception(mysqli_error($connection));
		}
		$List = [];
		while ($row = mysqli_fetch_assoc($Query))
		{
			$List[] = [
				(int)$row['ID'],
				$row['NAME'],
			];
		}

		$tableField = [
			new TableField('ID', 'id', 'ID'),
			new TableField('Название', 'text', 'NAME'),
		];

		return [(array)$tableField, (array)$List];
	}

	public function getOrdersByAdmin(): array
	{
		$connection = Connection::getInstance()->getConnection();
		$Query = mysqli_query($connection, "
			SELECT ID, DATE, CUSTOMER_NAME, CUSTOMER_EMAIL, CUSTOMER_PHONE, COMMENT, STATUS
			FROM `order`
		");

		if (!$Query)
		{
			throw new Exception(mysqli_error($connection));
		}
		$List = [];
		while ($row = mysqli_fetch_assoc($Query))
		{
			$List[] = [
				(int)$row['ID'],
				$row['DATE'],
				$row['CUSTOMER_NAME'],
				$row['CUSTOMER_EMAIL'],
				$row['CUSTOMER_PHONE'],
				($row['COMMENT'] === null || $row['COMMENT'] === 'NULL') ? '' : $row['COMMENT'],
				$row['STATUS'],
			];
		}
		$tableField = [
			new TableField('ID', 'id', 'ID'),
			new TableField('Дата', 'text', 'DATE'),
			new TableField('Имя покупателя', 'text', 'CUSTOMER_NAME'),
			new TableField('Почта покупателя', 'text', 'CUSTOMER_EMAIL'),
			new TableField('Телефон покупателя', 'text', 'CUSTOMER_PHONE'),
			new TableField('Комментарий', 'text', 'COMMENT'),
			new TableField('Статус заказа', 'text', 'STATUS'),
		];
		return [(array)$tableField, (array)$List];
	}


	public function getVinilStatuses(): array{
		$connection = Connection::getInstance()->getConnection();
		$Query = mysqli_query($connection, "
			SELECT ID
			FROM `status`
		");
		if (!$Query)
		{
			throw new Exception(mysqli_error($connection));
		}
		$List = [];
		while ($row = mysqli_fetch_assoc($Query))
		{
			$List[] = $row['ID'];
		}
		return $List;
	}

	public function getProductTagRelation():array{
		$connection = Connection::getInstance()->getConnection();
		$Query = mysqli_query($connection, "
			SELECT PRODUCT_ID, TAG_ID
			FROM `product_tag`
		");
		if (!$Query)
		{
			throw new Exception(mysqli_error($connection));
		}
		$List = [];
		while ($row = mysqli_fetch_assoc($Query))
		{
			$List[] = [(int)$row['PRODUCT_ID'], (int)$row['TAG_ID']];
		}
		return $List;
	}

	public function setProductTag($id, $tags):array{
		$connection = Connection::getInstance()->getConnection();
		$deleteQuery = "DELETE FROM product_tag WHERE PRODUCT_ID = {$id};";

		$setQuery = "";
		for ($i = 0, $iMax = count($tags); $i< $iMax; $i++){
			$setQuery .= "INSERT INTO product_tag (PRODUCT_ID, TAG_ID) VALUES ({$id}, {$tags[$i]});";
		}

		//mysqli_begin_transaction($connection);

		$test = mysqli_query($connection, $deleteQuery);
		if ($test)
		{
			$test = mysqli_multi_query($connection, $setQuery);
		}
		if ($test)
		{
			// $test = mysqli_commit($connection);
			return [];
		}
		// mysqli_rollback($connection);
		return ['error'];
	}
}