<?php echo form_open('admin/nitrocart_categories/categories/delete'); ?>
<div class="one_full" id="">
		<section class="title">
				<h4><?php echo lang('nitrocart_categories:title');?></h4>
		</section>
		<section class="item">
			<div class="content">
		<?php if (empty($categories)): ?>
				<div class="no_data">
					<br />
					<p>
						<?php echo lang('nitrocart_categories:categories');?>
					</p>

					<?php echo lang('nitrocart_categories:no_categories'); ?>
					<br /><br /><br />
					<p>
					<small>Enjoy using NitroCart! Send us your feedback here <a href='mailto:feedback@nitrocart.net'>feedback@nitrocart.net</a></small>
					</p>

				</div>
			</div>
		</section>
	<?php else: ?>
		<table class='sortable' id='sortable_list'>
			<thead>
				<tr>
					<th></th>
					<th><?php echo lang('nitrocart_categories:id');?></th>
					<th><?php echo lang('nitrocart_categories:category');?></th>
					<th><?php echo lang('nitrocart_categories:image');?></th>
					<th>Sub Categories</th>
					<th style="width: 120px"></th>
				</tr>
			</thead>
			<tbody>

				<?php
					$data = new StdClass();
					foreach ($categories AS $category): ?>

					<?php

						//init the data field
						//$data->category = $category;
					?>

					<tr>

						<td><a class='handle'>:::</a> <input type="checkbox" name="action_to[]" value="<?php echo $category->id; ?>"  /></td>
						<td><?php echo $category->id; ?></td>
						<td><?php echo $category->name; ?></td>
						<td>
							<?php echo CategoryHelper_category_image( $category );?>
						</td>

						<td><?php echo $category->children_count; ?></td>
						<td>
							<span style="float:right;">
								<?php echo dropdownMenu('admin/nitrocart_categories/categories', $category->id,  TRUE,TRUE,FALSE);?>
							</span>
						</td>
					</tr>



				<?php endforeach; ?>



			</tbody>
			<tfoot>
				<tr>
					<td colspan="6"><div style="float:right;"></div></td>
				</tr>
			</tfoot>
		</table>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
		</div>
		</div>
		</section>
	<?php endif; ?>


	<?php if (isset($pagination)): ?>
		<?php echo $pagination; ?>
	<?php endif; ?>

</div>
<?php echo form_close(); ?>
<script>


//
// http://johnny.github.io/jquery-sortable/#group-options
//
$(function  () {
	$('.sortable').sortable({
	  containerSelector: 'table > tbody',
	  itemPath: '> tbody',
	  itemSelector: 'tr',
	  handle:'.handle',
	  pullPlaceholder: false,
	  nested: false,
	  placeholder: '<tr class="placeholder"/>',
	  afterMove:_am,
	  onDrop:_onDrop,
	  onDrag: _onDrag,
	  onDragStart: _onDragStart,

	})
})
</script>
<style>
	body.dragging, body.dragging * {
	  cursor: move !important;
	}
	#subcategories.dragging, #subcategories.dragging tr {
	  cursor: move !important;
	}
	.dragged {
	  position: absolute;
	  opacity: 0.5;
	  z-index: 2000;
	}

	.sortable tr {
	  position: relative;
	  /** More li styles **/
	}
	.sortable tr:before {
	  position: absolute;
	  /** Define arrowhead **/
	}
	.handle
	{
		cursor: move !important;
	}
</style>