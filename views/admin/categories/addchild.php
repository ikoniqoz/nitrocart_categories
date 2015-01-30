	<div style="">
	<?php echo form_open_multipart('admin/nitrocart_categories/categories/savechild/', 'id="myform" class="crud"'); ?>
	<?php echo form_hidden('id', $id ); ?>



				<?php echo form_hidden('start_order_from', 0); ?>

	 			<div style='float:left;width:200px;'>
	 			<section>
					<ul id="" >
							<li>
								<label>Child <?php echo lang('nitrocart:categories:category_name');?></label>
								<div class="">
									<?php echo form_input('value1'); ?>
								</div>
							</li>
						</ul>
						</section>
					</div>


				<div style='clear:both'>
					<section>
						<div class="buttons">
							<button class="btn blue" value="save" name="btnAction" type="submit"><span>Save</span></button>
							<button class="btn blue" value="save_and_edit" name="btnAction" type="submit"><span>Save &amp; edit</span></button>
							<a class="btn gray cancel" href="admin/nitrocart_categories">Cancel</a>
						</div>
					</section>
				</div>




	<?php echo form_close(); ?>
	</div>