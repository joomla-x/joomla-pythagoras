<?php
/**
 * Part of the Joomla Framework Content Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * @var \Joomla\Content\Type\Tabs $content
 * @codingStandardsIgnoreStart
 */

?>
	<!-- <?= __FILE__ ?> -->
	<nav id="jx-nav" class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
				        data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
				</button>
				<a class="navbar-brand page-scroll" href="#page-top"><?php echo $content->getTitle(); ?></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<?php foreach ($content->elements as $i => $element) : ?>
						<?php
						if ($i == 0 || $i == (count($content->elements) - 1))
						{
							continue;
						}
						?>
						<li><a class="page-scroll"
						       href="#<?php echo $element->getId(); ?>"><?php echo $element->getTitle(); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</nav>

<?php foreach ($content->elements as $i => $element) : ?>
	<?php echo $element->html; ?>
<?php endforeach; ?>
	<!-- EOF <?= __FILE__ ?> -->

<?php
$js = <<<JS
(function($) {
	"use strict"; // Start of use strict
	$(document).ready(function() {
		// jQuery for page scrolling feature - requires jQuery Easing plugin
		$('a.page-scroll').bind('click', function(event) {
			var anchor = $(this);
			$('html, body').stop().animate({
				scrollTop: ($(anchor.attr('href')).offset().top - 50)
			}, 1250, 'easeInOutExpo');
			event.preventDefault();
		});

		// Highlight the top nav as scrolling occurs
		$('body').scrollspy({
			target: '.navbar-fixed-top',
			offset: 100
		});

		// Closes the Responsive Menu on Menu Item Click
		$('.navbar-collapse ul li a').click(function() {
			$('.navbar-toggle:visible').click();
		});

		// Offset for Main Navigation
		$('#mainNav').affix({
			offset: {
				top: 50
			}
		})
	});

})(jQuery); // End of use strict
JS;

$this->addJavascript('.navbar', $js);
