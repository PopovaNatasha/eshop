<?php
/**
 * @var array $tags
 */

$currentPage = $_SERVER['REQUEST_URI'];
$uri = (preg_match('/tag\/[0-9]+/', $currentPage)) ? explode('/', $currentPage) : [];

?>

<div class="side-bar">
	<?php foreach ($tags as $tag):?>
		<a href="/tag/<?= $tag->getId() ?>/" class="category-button <?= (int)$uri[2] === (int)$tag->getId() ? 'active' : '' ?>">
			<?= $tag->getTitle() ?>
		</a>
	<?php endforeach;?>
</div>