<?php
/**
 * @var \Joomla\Content\Type\Paragraph $content
 */
?>
<?php if ($content->variant == Joomla\Content\Type\Paragraph::EMPHASISED) : ?>
	<p><em><?= $content->text; ?></em></p>
<?php else : ?>
	<p><?= $content->text; ?></p>
<?php endif; ?>
