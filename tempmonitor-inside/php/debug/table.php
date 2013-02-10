<table border='1' cellpadding='3'>
<tr>
	<th colspan='<?php echo count($this->columns) ?>'>
		<h3><?php echo $this->tableName ?></h3>
	</th>
</tr>
<tr>
	<?php foreach($this->columns as $c): ?>
		<th><?php echo $c ?></th>
	<?php endforeach; ?>
</tr>
	<?php foreach($this->rows as $r): ?>
		<tr>
			<?php foreach($this->columns as $c): ?>
				<td>
					<?php echo $r[$c] ?>
				</td>
			<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
</table>

	