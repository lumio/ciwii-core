<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?><?php
	if ($mobile) {
		echo !$autocorrect ? ' autocorrect="off"':'';
		echo !$autocapitalize ? ' autocapitalize="off"':'';
	}
?><?php echo $xml ? ' /':''; ?>>