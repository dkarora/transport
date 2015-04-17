<?php if (empty($scholars)) : ?>

	No Road Scholars here!
	
<?php else : ?>

	<table>
		
		<tr>
			<th>Name</th>
			<th>Organization</th>
			<th>Credits</th>
		</tr>
		
		<?php $i = 0; ?>
		<?php foreach ($scholars as $scholar) : ?>
			
			<tr<?php if($i % 2) { echo ' class="altrow"'; } ?>>
				<td><?php echo $scholar['User']['full_name']; ?></td>
				<td><?php echo !empty($scholar['Group']) ? $scholar['Group']['name'] : 'n/a'; ?></td>
				<td><?php echo $scholar['total_credits']; ?></td>
			</tr>
			
			<?php $i++; ?>
			
		<?php endforeach; ?>
		
	</table>

<?php endif; ?>
