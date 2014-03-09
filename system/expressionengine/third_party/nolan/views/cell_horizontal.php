<table class="nolan_table" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th style='width: 10px;'></th>
			<?php $i = 0 ?>
			<?php foreach($col_labels as $label): ?>
				<th>
					<?php if(isset($col_types[$i]) && $col_types[$i] != 'checkbox'):?>
						<?php echo $label; ?>
					<?php endif; ?>
				</th>
				<?php $i++; ?>
			<?php endforeach ?>
			<th></th>
		</tr>
	</thead>
	<tbody <?php echo ($type == 'field') ? 'class="native_nolan"' : '';?> data-maxrows="<?=$max_rows?>">
	<?php foreach($row_data as $key => $row): ?>
		<tr class="row">
			<td class='nolan_drag_handle'>&nbsp;</td>
			<?php $i = 0 ?>
				<?php foreach($row as $cell => $cell_data): ?>
				<td class="nolan_content_col" width="<?=$col_width?>">
					<?php if(isset($col_types[$i]) && $col_types[$i] == 'textarea'):?>
						<?php 

							$data = array(
				              'name'        => $cell_name.'['.$cell.']['.$key.']',
				              'value'       => html_entity_decode($cell_data),
				              'cols'        => '10',
				              'rows'        => '5'
				            );

							echo form_textarea($data); 
						?>
					<?php elseif(isset($col_types[$i]) && $col_types[$i] == 'checkbox'):?>
						<div class="nolan_checkbox">
							<label>
							<?php echo form_checkbox($cell_name.'['.$cell.']['.$key.']', 'checked', $cell_data) ?>&nbsp;
							<?php if(isset($col_labels[$i]) && $col_labels[$i] != '') {echo $col_labels[$i];} ?>
						</label>
						</div>
					<?php else: ?>
						<?php echo form_input($cell_name.'['.$cell.']['.$key.']', $cell_data) ?></td>
					<?php endif; ?>
				</td>
				<?php $i++; ?>
			<?php endforeach ?>
			<td class='nolan_nav'><?php echo $nav; ?></td>
		</tr>
	<?php endforeach ?>
	
	<?php if(! count($row_data)): ?>
		<tr class="row">
			<td class='nolan_drag_handle'>&nbsp;</td>
				<?php $i = 0 ?>
				<?php foreach($col_names as $col_name): ?>
					
						<?php if(isset($col_types[$i]) && $col_types[$i] == 'textarea'):?>
							<td class="nolan_content_col" width="<?=$col_width?>">
							<?php 
							
								$data = array(
					              'name'        => $cell_name.'['.$col_name.'][0]',
					              'value'       => '',
					              'cols'        => '5',
					              'rows'        => '5'
					            );

								echo form_textarea($data); 
							?>
						<?php elseif(isset($col_types[$i]) && $col_types[$i] == 'checkbox'):?>
							<td class="nolan_content_col" width="<?=$col_width?>">
							<div class="nolan_checkbox">
								<label>
								<?php echo form_checkbox($cell_name.'['.$col_name.'][0]', 'checked', '') ?> &nbsp;
								<?php if(isset($col_labels[$i]) && $col_labels[$i] != '') {echo $col_labels[$i];} ?>
							</label>
							</div>
						<?php else: ?>
							<td class="nolan_content_col" width="<?=$col_width?>">
							<?php echo form_input($cell_name.'['.$col_name.'][0]', '') ?></td>
						<?php endif; ?>
					</td>
					<?php $i++; ?>
				<?php endforeach ?>
			<td class='nolan_nav'><?php echo $nav; ?></td>
		</tr>
	<?php endif ?>	
	
	</tbody>
</table>