<?php
/**
 *
 */
	switch ($doctype):
		case 'html4-strict':
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><?php
			break;

		case 'html4-transitional':
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><?php
			break;

		case 'xhtml1-transitional':
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><?php
			break;

		case 'xhtml1-frameset':
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><?php
			break;

		case 'xhtml1.1-dtd':
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><?php
			break;

		case 'xhtml1.1-basic':
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><?php
			break;

		case 'html5':
?><!DOCTYPE HTML>
<html><?php
			break;

		case 'xhtml1-strict':
		default:
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><?php
			break;
	endswitch;
?>

<head>
	<title><?php echo $title; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=<?php echo $charset ? $charset:'utf-8'; " />
	
<?php
	foreach ($stylesheets as $sheet):
?>	<link rel="stylesheet" type="text/css" href="<?php echo $sheet['href']; ?>" media="<?php echo $sheet['media']; ?>" />
<?php
	endforeach;
?>

<?php
	if (!empty($styles)):
?>	<style type="text/css">
<?php
		foreach ($styles as $style):
?>
<?php
		endforeach;
?>
	</style>
<?php
	endif;
?>

<?php
	foreach ($javascript as $jsContent):
		if ($jsContent['type'] == 'file'):
?>	<script type="text/javascript" src="<?php echo $jsContent['src']; ?>"></script>
<?php
		else:
?>
	<script type="text/javascript">
	<!-- //
		<?php echo $jsContent['content']; ?>
	// -->
	</script>
<?php
		endif;
	endforeach;
?>
</head>

<body>
	<?php echo $content; ?>
</body>
</html>
