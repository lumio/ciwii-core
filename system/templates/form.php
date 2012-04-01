<form<?php
	echo $id		? ' id="'.$id.'"':'';
	echo $class		? ' class="'.$class.'"':'';
?> action="<?php echo $action; ?>"<?php
	echo $target	? ' target="'.$target.'"':'';
	echo $charset	? ' accept-charset="'.$charset.'"':'';
	echo $enctype	? ' enctype="'.$enctype.'"':'';
?>>
	<?php echo $content; ?>
</form>
