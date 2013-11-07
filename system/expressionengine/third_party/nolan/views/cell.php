<table class="nolan_table" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th style='width: 10px;'></th>
			<?php foreach($col_labels as $label): ?>
				<th><?php echo $label; ?></th>
			<?php endforeach ?>
			<th></th>
		</tr>
	</thead>
	<tbody data-maxrows="<?=$max_rows?>">
	<?php foreach($row_data as $key => $row): ?>
		<tr class="row">
			<td class='nolan_drag_handle'>&nbsp;</td>
				<?php foreach($row as $cell => $cell_data): ?>
				<td class="nolan_content_col"><?php echo form_input($cell_name.'['.$cell.']['.$key.']', $cell_data) ?></td>
				<?php endforeach ?>
			<td class='nolan_nav'><?php echo $nav; ?></td>
		</tr>
	<?php endforeach ?>
	
	<?php if(! count($row_data)): ?>
		<tr class="row">
			<td class='nolan_drag_handle'>&nbsp;</td>
				<?php foreach($col_names as $col_name): ?>
				<td class="nolan_content_col"><?php echo form_input($cell_name.'['.$col_name.'][0]', '') ?></td>
				<?php endforeach ?>
			<td class='nolan_nav'><?php echo $nav; ?></td>
		</tr>
	<?php endif ?>	
	
	</tbody>
</table>
