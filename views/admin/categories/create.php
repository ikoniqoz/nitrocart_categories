<div class="one_half" id="">

	<section class="title">
		<h4><?php echo lang('nitrocart_categories:new'); ?></h4>
	</section>

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

	<input type='hidden' name='parent_id' value='0'>

	<section class="item form_inputs">
		<div class="content">

			<fieldset>
				<ul>
					<li>
						<a class='btn green' href="admin/nitrocart_categories/categories/">Back to List</a>
					</li>
					<li class="<?php echo alternator('even', ''); ?>">
						<label for="name"><?php echo lang('nitrocart_categories:name');?><span>*</span></label>
						<div class="input">
							<?php echo form_input('name', set_value('name', $name), 'id="name" '); ?>
						</div>
					</li>
					<li>
						<label for="user_data"><?php echo lang('nitrocart_categories:description');?><span></span></label>
						<div class="input">
								<?php echo form_textarea('description', set_value('description', isset($description)?$description:""), 'class="wysiwyg-simple"'); ?>
								<br />
						</div>
					</li>
				</ul>
			</fieldset>

			<div class="buttons">
					<button class="btn blue" value="save_exit" name="btnAction" type="submit">
						<span><?php echo lang('nitrocart_categories:save_exit');?></span>
					</button>

					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))); ?>

					<a href="admin/nitrocart_categories/categories/" class="btn gray">Cancel</a>
			</div>

		</div>
	</section>
	<?php echo form_close(); ?>

</div>