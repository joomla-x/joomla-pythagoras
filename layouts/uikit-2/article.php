<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Attribution $content
 * @codingStandardsIgnoreStart
 */

?>
<article class="uk-article">
    <h1 class="uk-article-title"><?php echo $content->article->title; ?></h1>
    <p class="uk-article-meta">Written by <?php echo $content->article->author; ?></p>
    <p class="uk-article-lead"><?php echo $content->article->body; ?></p>
</article>
