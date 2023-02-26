<?php
/** @var array $orderList */
/** @var int $orderId */
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="/css/reset.css">
	<link rel="stylesheet" href="/css/admin_style.css">
</head>

<body>
<div class="admin-table-container">
	<table class="admin-table">
		<tr class="table-tr table-header-td">
			<td class="table-td table-header-td table-td-unable">
				ID
			</td>
			<td class="table-td table-header-td table-td-unable">
				 ID товара
			</td>
			<td class="table-td table-header-td table-td-unable">
				ID заказа
			</td>
			<td class="table-td table-header-td">
				Название
			</td>
			<td class="table-td table-header-td">
				Количество
			</td>
			<td class="table-td table-header-td">
				Цена
			</td>
			<td class="table-td table-header-td">

			</td>
			<td class="table-td table-header-td">

			</td>
		</tr>
		<?php foreach ($orderList as $order):?>
		<form action = "/admin/order/update/<?=$order['ID']?>/" method="post">
		<tr class = "table-tr row">
			<td class="table-td table-td-unable">
				<div class="cell-content-div">
					<div class="cell-text-div">
						<input class="cell-input" type="number" name="ID" readonly="readonly" data-field="ID" value="<?= $order['ID']?>">
					</div>
				</div>
			</td>
			<td class="table-td table-td-unable">
				<div class="cell-content-div">
					<div class="cell-text-div">
						<input class="cell-input" type="number" name="PRODUCT_ID" data-field="PRODUCT_ID" value="<?= $order['PRODUCT_ID']?>">
					</div>
				</div>
			</td>
			<td class="table-td table-td-unable">
				<div class="cell-content-div">
					<div class="cell-text-div">
						<input class="cell-input" type="number" name="ORDER_ID" data-field="ORDER_ID" value="<?= $order['ORDER_ID']?>">
					</div>
				</div>
			</td>
			<td class="table-td">
				<div class="cell-content-div">
					<div class="cell-text-div">
						<input class="cell-input" type="text" name="NAME" data-field="NAME" value="<?= $order['NAME']?>">
					</div>
				</div>
			</td>
			<td class="table-td">
				<div class="cell-content-div">
					<div class="cell-text-div">
						<input class="cell-input" type="number" name="COUNT" data-field="COUNT" value="<?= $order['COUNT']?>">
					</div>
				</div>
			</td>
			<td class="table-td">
				<div class="cell-content-div">
					<div class="cell-text-div">
						<input class="cell-input" type="number" name="PRICE" data-field="PRICE" value="<?= $order['PRICE']?>">
					</div>
				</div>
			</td>
			<td class="table-td">
				<div class="cell-content-div">
					<div class="cell-button-div">
						<input class = "btn save-button submit-button" type="submit" value="Сохранить">
					</div>
				</div>
			</td>
			<td class="table-td">
				<div class="cell-content-div">
					<div class="cell-button-div">
						<a href = "/admin/order/delete/<?= $order['ID'] ?>/" class="btn delete-button danger-button">Удалить</a>
					</div>
				</div>
			</td>
			</tr>
		</form>
		<?php endforeach; ?>
	</table>
	<div class="add-button-container">
		<a href = "/admin/order/add/<?= $orderId ?>/" class = "btn add-button">Добавить</a>
	</div>
	<div class="button-move">
		<a href="/admin" class="image-button"> Вернуться на главную </a>
	</div>
</div>
</body>