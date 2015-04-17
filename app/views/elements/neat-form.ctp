<?php
/*
	neat-form: nests a table into a form to line up the fields.
	
	options:
	$model -				name of the model to post this form to.
	
	$rows -	 				array of forms to insert.
							format is name => array of options.
							alternatively, just providing the name will create a
							section header.
	
	[optional]
	$table_id - 			the html id of the table.
							default is none.
	
	[optional]
	$end -	 				text for the submit button.
							default is the CakePHP's default text.
	
	[optional]
	$form_opts -			array of options to send to the $form->create function.
							default is an empty array.
	
	[optional]
	$section_header_tag - 	tag name for section headers.
							default is h3.
	
	[optional]
	$shift_back_before - 	whether or not to append the 'before' value in a row's
							option array to the label.
							default is false.
	
	[optional]
	$css - 					a string or array containing the CSS files to include.
							default is the neat-form.css file.
	
	[optional]
	$isAjax - 				if true, only table rows and their contents will be generated.
							defaults to false.
	
	[optional]
	$submitInfo - 			a string containing text to display. if set, an information box will be generated above the submit button.
							default is none.
	
	[optional]
	$submitInfoClass - 		a string containing the CSS class of the submit info box's div.
							default is 'themoreyouknow submit-info'.
*/
	if (empty($submitInfoClass))
		$submitInfoClass = 'themoreyouknow submit-info';
	
	if (empty($isAjax))
		$isAjax = false;
	
	if (empty($form_opts))
		$form_opts = array();
	
	if (empty($table_id))
		$table_id = '';
	
	if (empty($section_header_tag))
		$section_header_tag = 'h3';
	
	if (empty($end))
		$end = 'Submit';
	
	if (empty($shift_back_before))
		$shift_back_before = false;
	
	if (!isset($css))
		$css = array('neat-form');
?>

<?php
	if (!empty($css) && !$isAjax)
		echo $html->css($css, null, array(), false);
?>

<?php if (!$isAjax) : ?>
<div class="neat-form">
<?php
	if (!empty($model))
		echo $form->create($model, $form_opts);
?>
<table<?php echo (!empty($table_class) ? " class='$table_class'" : ''); echo (!empty($table_id) ? " id='$table_id'" : ''); ?>>
<?php endif; ?>

<?php foreach ($rows as $key => $val) : ?>
	<?php
		// check if we should compose the values together
		$compose = true;
		
		if (is_array($val) && isset($val['compose']) && $val['compose'] === false)
			$compose = false;
	?>
	
	<tr>
		<?php if (!$compose) : ?>
			<?php if (!empty($val['label'])) : ?>
				<td class="label"><?php echo $val['label']; ?></td>
				<td class="content"><?php echo $val['content']; ?></td>
			<?php else : ?>
				<td colspan='2' class="content full-width"><?php echo $val['content']; ?></td>
			<?php endif; ?>
		<?php elseif (is_numeric($key)) : ?>
			<td colspan="2"><?php echo $html->tag('h3', $val); ?></td>
		<?php else : ?>
			<?php
				$showlabel = true;
				$label = null;
				$before = '';
				
				if (!empty($val['label']))
					$label = $val['label'];
				
				if (!empty($val['type']) && $val['type'] == 'hidden')
				{
					$label = null;
					$showlabel = false;
				}
				
				// check for the asterisk
				if (!empty($val['before']) && $shift_back_before)
				{
					$before = $val['before'];
					$val['before'] = '';
				}
				
				$val['label'] = false;
			?>
			
			<?php if ($showlabel) : ?>
				<?php if (!empty($label)) : ?>
					<td class="label"><?php echo $form->label($key, $label), $before; ?></td>
				<?php else : ?>
					<td class="label"><?php echo $form->label($key), $before; ?></td>
				<?php endif; ?>
				
				<td class="content"><?php echo $form->input($key, $val); ?></td>
			<?php else : ?>
				<td class="hidden-input"><?php echo $form->input($key, $val); ?></td>
			<?php endif; ?>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>

<?php if (!$isAjax) : ?>
</table>
<?php
	if (!empty($submitInfo))
		echo $html->div($submitInfoClass, $submitInfo);
?>
<?php
	if (!empty($end))
		echo $form->end($end);
?>
</div>
<?php endif; ?>