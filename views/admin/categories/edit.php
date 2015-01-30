<div class="one_half" id="">

  <section class="title">
  	<?php if (isset($id) AND $id > 0): ?>
  		<h4><?php echo sprintf(lang('nitrocart_categories:form:title:edit'), $name); ?></h4>
  	<?php else: ?>
  		<h4><?php echo lang('nitrocart_categories:form:title:new'); ?></h4>
  	<?php endif; ?>
  </section>

<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

<input type='hidden' name='parent_id' value='<?php echo (isset($parent_id))?$parent_id:0; ?>'>

<?php if (isset($id) AND $id > 0): ?>
	<?php echo form_hidden('id', $id); ?>
	<input type="hidden" name="cid" id="cid" value="<?php echo $id; ?>" >
<?php endif; ?>
<section class="item form_inputs">
	<div class="content">

		<fieldset>
			<ul>
				<li>
					<a class='btn green' href="admin/nitrocart_categories/categories/">Back to List</a>
					<?php if (isset($id) AND $id > 0): ?>
					<?php if($parent_id>0) :?>
						<?php $parent_cat = category_get($parent_id); ?>
						<?php if($parent_cat->parent_id >0):?>


								<?php $parent_cat2 = category_get($parent_cat->parent_id); ?>
								<?php if($parent_cat2->parent_id >0):?>

										<?php $parent_cat3 = category_get($parent_cat2->parent_id); ?>
										<?php if($parent_cat3->parent_id >0):?>
											<a class='btn gray' href="admin/nitrocart_categories/categories/edit/<?php echo $parent_cat3->parent_id;?>">...</a>
										<?php endif; ?>





									<a class='btn gray' href="admin/nitrocart_categories/categories/edit/<?php echo $parent_cat2->parent_id;?>"><?php echo HelperGet_category_name($parent_cat2->parent_id);?></a>
								<?php endif; ?>


							<a class='btn gray' href="admin/nitrocart_categories/categories/edit/<?php echo $parent_cat->parent_id;?>"><?php echo HelperGet_category_name($parent_cat->parent_id);?></a>
						<?php endif; ?>
						<a class='btn gray' href="admin/nitrocart_categories/categories/edit/<?php echo $parent_id;?>"><?php echo HelperGet_category_name($parent_id);?></a>
					<?php endif;?>

					<a class='btn orange' href="admin/nitrocart_categories/categories/edit/<?php echo $id;?>"><?php echo $name;?></a>
					<?php endif;?>

				</li>
				<li class="<?php echo alternator('even', ''); ?>">
					<label for="name"><?php echo lang('nitrocart_categories:name');?><span>*</span></label>
					<div class="input">
						<?php echo form_input('name', set_value('name', $name), 'id="name" '); ?>
					</div>
				</li>
				<li class="<?php echo alternator('', 'even'); ?>">
					<label for="slug"><?php echo lang('nitrocart_categories:slug');?><span>*</span></label>
					<div class="input"><?php echo form_input('slug', set_value('slug', $slug)); ?></div>
				</li>

        <li class="">
          <label for="hidden">Visibility<span></span></label>
          <div class="input">
          Current Status is:
                <?php if($hidden):?>
                    <a title='Click to Toggle' href='admin/nitrocart_categories/categories/visibility/<?php echo $id;?>/off' class='tooltip-s button delete_button'>Hidden</a>
                <?php else: ?>
                    <a title='Click to Toggle' href='admin/nitrocart_categories/categories/visibility/<?php echo $id;?>/on' class='tooltip-s button edit_button'>Visible</a>
                <?php endif;?>

                <!--
                $onoff = array(0=>'Hidden',1=>'Visible');
                echo $onoff[$hidden];
                -->

           </div>
        </li>


				<li>
					<label><?php echo lang('nitrocart_categories:image');?></label>
					<div id='' name=''>

							Current Image
							<div class='CategoryImage'>
								{{if file_id !=='' }}
							        <img src="{{url:site}}files/thumb/{{file_id}}/100/" alt="" width="100px" />
							        <a file-id="{{file_id}}" href="#" class="delCategoryImage sbtn sbtn-flat red">Delete</a>
						        {{endif}}

						 	</div>


    						<label for="">
                    			<h3>Drag category image into the DropZone</h3>
    						</label>

			                <div style="border:dashed 5px #777;background:#fff;">
			                    <!-- DROP ZONE -->
			                    <div class="input dropzone" id="mydrop" style="min-height:200px;background:#eee;">

			                        <center>
			                            <br />
			                            <span style="vertical-align:middle;font-size:40px;text-align:center;">Drop Image Here</span>
			                        </center>

			    				        </div>
			                    <!-- DROP ZONE -->
			                </div>

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

<?php if( isset($id)): ?>

<style>
	body.dragging, body.dragging * {
	  cursor: move !important;
	}
	/*#subcategories.dragging, #subcategories.dragging tr {
	  cursor: move !important;
	}*/
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

<script>
//
// http://johnny.github.io/jquery-sortable/#group-options
//
$(function  () {
	$('.sortable').sortable({
	  containerSelector: 'table',
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

<div class="one_half last" id="">



<section class="title" >
  <h4>Sub Categories</h4>
  <span style="float:right;margin-right:10px;">
    <a id="" href="nitrocart_categories/admin/categories/addchild/<?php echo $id;?>" class='modal tooltip-s small button blue'><?php echo lang('nitrocart_categories:create');?> Subcategory</a>
  </span>
</section>



<section class="item form_inputs">
	<div class="content">
		<table class='sortable' id='sortable_list'>
			<thead>
				<tr>
					<th></th>
					<th><?php echo lang('nitrocart_categories:id');?></th>
					<th><?php echo lang('nitrocart_categories:category');?></th>
					<th>Image</th>
          <th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($children as $child)
					{
						$editlink 	= "<a class='btn orange' href='admin/nitrocart_categories/categories/edit/"  .$child->id ."'>edit</a> ";
						$dellink  	= "<a class='btn red delete confirm' href='admin/nitrocart_categories/categories/delete/"  .$child->id . "/".$id."'>delete</a>";
						$handle		= "<a class='handle'>:::</a>";

						echo "<tr>";
						echo "		<td>{$handle}</td>";
						echo "		<td>{$child->id}</td>";
						echo "		<td>{$child->name}</td>";
            echo "    <td>" . CategoryHelper_category_image( $child ) . "</td>";            
						echo "		<td><span style='float:right'>{$editlink} {$dellink}</span></td>";
						echo "</tr>";
					}

				?>
			</tbody>
		</table>
	</div>
</section>
<?php endif; ?>
</div>

    <script>



      function image_added(file_id)
      {

        var str  = '<img src="{{url:site}}files/thumb/'+file_id+'/100/" alt="" width="100px" /> ';
            str += '<a file-id="'+file_id+'" href="#" class="delCategoryImage sbtn sbtn-flat red">Delete</a>';

            $("div.CategoryImage").html(str);
      }


        //
        // Settings
        //

        // Dropzone.autoDiscover = true;
        Dropzone.options.mydrop = {

          // Make sure only images are accepted
          acceptedFiles: "image/*",
          autoProcessQueue: true,
          maxFiles: 1,

        };


      $(function() {


        //ajax handlers to delete images from products
        /**
        *
        * @param  {[type]} e [description]
        * @return {[type]}   [description]
        */
        $(document).on('click', '.delCategoryImage', function(event) {  //this works much better

              var options = $(this);

              //Warn about delete
              if(confirm("Are you sure you want to remove this image ? "))
              {
                  var url = "<?php echo site_url();?>admin/nitrocart_categories/images/remove/{{id}}/" + options.attr("file-id");

                  $.post(url).done(function(data)
                  {

                      var obj = jQuery.parseJSON(data);

                      if(obj.status == 'success')
                      {

                          options.fadeTo("slow", 0.1);
                          setTimeout(function() {
                              options.parent().delay(4000).remove();
                          }, 3000);

                      }
                      else
                      {
                          alert(obj.message);
                      }

                  });

              }

              // Prevent Navigation
              event.preventDefault();


        });



        // Now that the DOM is fully loaded, create the dropzone, and setup the
        // event listeners
        var myDropzone = new Dropzone("div#mydrop", { url: "<?php echo site_url();?>admin/nitrocart_categories/images/upload/<?php echo $id;?>"});



        myDropzone.on("addedfile", function(file) {
          file.previewElement.addEventListener("click", function() { myDropzone.removeFile(file); });
        });

        myDropzone.on("sending", function(file, xhr, formData) {
          formData.append("filesize", file.size); // Will send the filesize along with the file as POST data.
          // Will send the filesize along with the file as POST data.
          var token_name = "<?php echo $this->security->get_csrf_token_name();?>";
          var token_value = "<?php echo $this->security->get_csrf_hash();?>";
          formData.append(token_name, token_value);
        });


        //feedback
        Dropzone.options.myDropzone = {


          init: function()
          {

                this.on("addedfile", function(file) {
                 //do stuff
                });
                this.on("uploadprogress", function(file) {
                 //do stuff
                });

          }

        };

        myDropzone.on("success", function(file, responseText) {
                  // Handle the responseText here. For example, add the text to the preview element:
                  file.previewTemplate.appendChild(document.createTextNode("Complete"));

                  var obj = jQuery.parseJSON(responseText);

                  image_added(obj.file_id);

        });

        myDropzone.on("complete", function(file) {

                  setTimeout(function() {
                      myDropzone.removeFile(file);
                  }, 2000);

        });

      });


    </script>


<style type="text/css">


/* The MIT License */
.dropzone,
.dropzone *,
.dropzone-previews,
.dropzone-previews * {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
.dropzone {
  position: relative;
  border: 1px solid rgba(0,0,0,0.08);
  background: rgba(0,0,0,0.02);
  padding: 1em;
}
.dropzone.dz-clickable {
  cursor: pointer;
}
.dropzone.dz-clickable .dz-message,
.dropzone.dz-clickable .dz-message span {
  cursor: pointer;
}
.dropzone.dz-clickable * {
  cursor: default;
}
.dropzone .dz-message {
  opacity: 1;
  -ms-filter: none;
  filter: none;
}
.dropzone.dz-drag-hover {
  border-color: rgba(0,0,0,0.15);
  background: rgba(0,0,0,0.04);
}
.dropzone.dz-started .dz-message {
  display: none;
}
.dropzone .dz-preview,
.dropzone-previews .dz-preview {
  background: rgba(255,255,255,0.8);
  position: relative;
  display: inline-block;
  margin: 17px;
  vertical-align: top;
  border: 1px solid #acacac;
  padding: 6px 6px 6px 6px;
}
.dropzone .dz-preview.dz-file-preview [data-dz-thumbnail],
.dropzone-previews .dz-preview.dz-file-preview [data-dz-thumbnail] {
  display: none;
}
.dropzone .dz-preview .dz-details,
.dropzone-previews .dz-preview .dz-details {
  width: 100px;
  height: 100px;
  position: relative;
  background: #ebebeb;
  padding: 5px;
  margin-bottom: 22px;
}
.dropzone .dz-preview .dz-details .dz-filename,
.dropzone-previews .dz-preview .dz-details .dz-filename {
  overflow: hidden;
  height: 100%;
}
.dropzone .dz-preview .dz-details img,
.dropzone-previews .dz-preview .dz-details img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100px;
  height: 100px;
}
.dropzone .dz-preview .dz-details .dz-size,
.dropzone-previews .dz-preview .dz-details .dz-size {
  position: absolute;
  bottom: -28px;
  left: 3px;
  height: 28px;
  line-height: 28px;
}
.dropzone .dz-preview.dz-error .dz-error-mark,
.dropzone-previews .dz-preview.dz-error .dz-error-mark {
  display: block;
}
.dropzone .dz-preview.dz-success .dz-success-mark,
.dropzone-previews .dz-preview.dz-success .dz-success-mark {
  display: block;
}
.dropzone .dz-preview:hover .dz-details img,
.dropzone-previews .dz-preview:hover .dz-details img {
  display: none;
}
.dropzone .dz-preview .dz-success-mark,
.dropzone-previews .dz-preview .dz-success-mark,
.dropzone .dz-preview .dz-error-mark,
.dropzone-previews .dz-preview .dz-error-mark {
  display: none;
  position: absolute;
  width: 40px;
  height: 40px;
  font-size: 30px;
  text-align: center;
  right: -10px;
  top: -10px;
}
.dropzone .dz-preview .dz-success-mark,
.dropzone-previews .dz-preview .dz-success-mark {
  color: #8cc657;
}
.dropzone .dz-preview .dz-error-mark,
.dropzone-previews .dz-preview .dz-error-mark {
  color: #ee162d;
}
.dropzone .dz-preview .dz-progress,
.dropzone-previews .dz-preview .dz-progress {
  position: absolute;
  top: 100px;
  left: 6px;
  right: 6px;
  height: 6px;
  background: #d7d7d7;
  display: none;
}
.dropzone .dz-preview .dz-progress .dz-upload,
.dropzone-previews .dz-preview .dz-progress .dz-upload {
  display: block;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  width: 0%;
  background-color: #8cc657;
}
.dropzone .dz-preview.dz-processing .dz-progress,
.dropzone-previews .dz-preview.dz-processing .dz-progress {
  display: block;
}
.dropzone .dz-preview .dz-error-message,
.dropzone-previews .dz-preview .dz-error-message {
  display: none;
  position: absolute;
  top: -5px;
  left: -20px;
  background: rgba(245,245,245,0.8);
  padding: 8px 10px;
  color: #800;
  min-width: 140px;
  max-width: 500px;
  z-index: 500;
}
.dropzone .dz-preview:hover.dz-error .dz-error-message,
.dropzone-previews .dz-preview:hover.dz-error .dz-error-message {
  display: block;
}
.dropzone {
  border: 1px solid rgba(0,0,0,0.03);
  min-height: 360px;
  -webkit-border-radius: 3px;
  border-radius: 3px;
  background: rgba(0,0,0,0.03);
  padding: 23px;
}
.dropzone .dz-default.dz-message {
  opacity: 1;
  -ms-filter: none;
  filter: none;
  -webkit-transition: opacity 0.3s ease-in-out;
  -moz-transition: opacity 0.3s ease-in-out;
  -o-transition: opacity 0.3s ease-in-out;
  -ms-transition: opacity 0.3s ease-in-out;
  transition: opacity 0.3s ease-in-out;
  background-image: url("../images/spritemap.png");
  background-repeat: no-repeat;
  background-position: 0 0;
  position: absolute;
  width: 428px;
  height: 123px;
  margin-left: -214px;
  margin-top: -61.5px;
  top: 50%;
  left: 50%;
}
@media all and (-webkit-min-device-pixel-ratio:1.5),(min--moz-device-pixel-ratio:1.5),(-o-min-device-pixel-ratio:1.5/1),(min-device-pixel-ratio:1.5),(min-resolution:138dpi),(min-resolution:1.5dppx) {
  .dropzone .dz-default.dz-message {
    background-image: url("../images/spritemap@2x.png");
    -webkit-background-size: 428px 406px;
    -moz-background-size: 428px 406px;
    background-size: 428px 406px;
  }
}
.dropzone .dz-default.dz-message span {
  display: none;
}
.dropzone.dz-square .dz-default.dz-message {
  background-position: 0 -123px;
  width: 268px;
  margin-left: -134px;
  height: 174px;
  margin-top: -87px;
}
.dropzone.dz-drag-hover .dz-message {
  opacity: 0.15;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=15)";
  filter: alpha(opacity=15);
}
.dropzone.dz-started .dz-message {
  display: block;
  opacity: 0;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
  filter: alpha(opacity=0);
}
.dropzone .dz-preview,
.dropzone-previews .dz-preview {
  -webkit-box-shadow: 1px 1px 4px rgba(0,0,0,0.16);
  box-shadow: 1px 1px 4px rgba(0,0,0,0.16);
  font-size: 14px;
}
.dropzone .dz-preview.dz-image-preview:hover .dz-details img,
.dropzone-previews .dz-preview.dz-image-preview:hover .dz-details img {
  display: block;
  opacity: 0.1;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=10)";
  filter: alpha(opacity=10);
}
.dropzone .dz-preview.dz-success .dz-success-mark,
.dropzone-previews .dz-preview.dz-success .dz-success-mark {
  opacity: 1;
  -ms-filter: none;
  filter: none;
}
.dropzone .dz-preview.dz-error .dz-error-mark,
.dropzone-previews .dz-preview.dz-error .dz-error-mark {
  opacity: 1;
  -ms-filter: none;
  filter: none;
}
.dropzone .dz-preview.dz-error .dz-progress .dz-upload,
.dropzone-previews .dz-preview.dz-error .dz-progress .dz-upload {
  background: #ee1e2d;
}
.dropzone .dz-preview .dz-error-mark,
.dropzone-previews .dz-preview .dz-error-mark,
.dropzone .dz-preview .dz-success-mark,
.dropzone-previews .dz-preview .dz-success-mark {
  display: block;
  opacity: 0;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
  filter: alpha(opacity=0);
  -webkit-transition: opacity 0.4s ease-in-out;
  -moz-transition: opacity 0.4s ease-in-out;
  -o-transition: opacity 0.4s ease-in-out;
  -ms-transition: opacity 0.4s ease-in-out;
  transition: opacity 0.4s ease-in-out;
  background-image: url("../images/spritemap.png");
  background-repeat: no-repeat;
}
@media all and (-webkit-min-device-pixel-ratio:1.5),(min--moz-device-pixel-ratio:1.5),(-o-min-device-pixel-ratio:1.5/1),(min-device-pixel-ratio:1.5),(min-resolution:138dpi),(min-resolution:1.5dppx) {
  .dropzone .dz-preview .dz-error-mark,
  .dropzone-previews .dz-preview .dz-error-mark,
  .dropzone .dz-preview .dz-success-mark,
  .dropzone-previews .dz-preview .dz-success-mark {
    background-image: url("../images/spritemap@2x.png");
    -webkit-background-size: 428px 406px;
    -moz-background-size: 428px 406px;
    background-size: 428px 406px;
  }
}
.dropzone .dz-preview .dz-error-mark span,
.dropzone-previews .dz-preview .dz-error-mark span,
.dropzone .dz-preview .dz-success-mark span,
.dropzone-previews .dz-preview .dz-success-mark span {
  display: none;
}
.dropzone .dz-preview .dz-error-mark,
.dropzone-previews .dz-preview .dz-error-mark {
  background-position: -268px -123px;
}
.dropzone .dz-preview .dz-success-mark,
.dropzone-previews .dz-preview .dz-success-mark {
  background-position: -268px -163px;
}
.dropzone .dz-preview .dz-progress .dz-upload,
.dropzone-previews .dz-preview .dz-progress .dz-upload {
  -webkit-animation: loading 0.4s linear infinite;
  -moz-animation: loading 0.4s linear infinite;
  -o-animation: loading 0.4s linear infinite;
  -ms-animation: loading 0.4s linear infinite;
  animation: loading 0.4s linear infinite;
  -webkit-transition: width 0.3s ease-in-out;
  -moz-transition: width 0.3s ease-in-out;
  -o-transition: width 0.3s ease-in-out;
  -ms-transition: width 0.3s ease-in-out;
  transition: width 0.3s ease-in-out;
  -webkit-border-radius: 2px;
  border-radius: 2px;
  position: absolute;
  top: 0;
  left: 0;
  width: 0%;
  height: 100%;
  background-image: url("../images/spritemap.png");
  background-repeat: repeat-x;
  background-position: 0px -400px;
}
@media all and (-webkit-min-device-pixel-ratio:1.5),(min--moz-device-pixel-ratio:1.5),(-o-min-device-pixel-ratio:1.5/1),(min-device-pixel-ratio:1.5),(min-resolution:138dpi),(min-resolution:1.5dppx) {
  .dropzone .dz-preview .dz-progress .dz-upload,
  .dropzone-previews .dz-preview .dz-progress .dz-upload {
    background-image: url("../images/spritemap@2x.png");
    -webkit-background-size: 428px 406px;
    -moz-background-size: 428px 406px;
    background-size: 428px 406px;
  }
}
.dropzone .dz-preview.dz-success .dz-progress,
.dropzone-previews .dz-preview.dz-success .dz-progress {
  display: block;
  opacity: 0;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
  filter: alpha(opacity=0);
  -webkit-transition: opacity 0.4s ease-in-out;
  -moz-transition: opacity 0.4s ease-in-out;
  -o-transition: opacity 0.4s ease-in-out;
  -ms-transition: opacity 0.4s ease-in-out;
  transition: opacity 0.4s ease-in-out;
}
.dropzone .dz-preview .dz-error-message,
.dropzone-previews .dz-preview .dz-error-message {
  display: block;
  opacity: 0;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
  filter: alpha(opacity=0);
  -webkit-transition: opacity 0.3s ease-in-out;
  -moz-transition: opacity 0.3s ease-in-out;
  -o-transition: opacity 0.3s ease-in-out;
  -ms-transition: opacity 0.3s ease-in-out;
  transition: opacity 0.3s ease-in-out;
}
.dropzone .dz-preview:hover.dz-error .dz-error-message,
.dropzone-previews .dz-preview:hover.dz-error .dz-error-message {
  opacity: 1;
  -ms-filter: none;
  filter: none;
}
.dropzone a.dz-remove,
.dropzone-previews a.dz-remove {
  background-image: -webkit-linear-gradient(top, #fafafa, #eee);
  background-image: -moz-linear-gradient(top, #fafafa, #eee);
  background-image: -o-linear-gradient(top, #fafafa, #eee);
  background-image: -ms-linear-gradient(top, #fafafa, #eee);
  background-image: linear-gradient(to bottom, #fafafa, #eee);
  -webkit-border-radius: 2px;
  border-radius: 2px;
  border: 1px solid #eee;
  text-decoration: none;
  display: block;
  padding: 4px 5px;
  text-align: center;
  color: #aaa;
  margin-top: 26px;
}
.dropzone a.dz-remove:hover,
.dropzone-previews a.dz-remove:hover {
  color: #666;
}
@-moz-keyframes loading {
  from {
    background-position: 0 -400px;
  }
  to {
    background-position: -7px -400px;
  }
}
@-webkit-keyframes loading {
  from {
    background-position: 0 -400px;
  }
  to {
    background-position: -7px -400px;
  }
}
@-o-keyframes loading {
  from {
    background-position: 0 -400px;
  }
  to {
    background-position: -7px -400px;
  }
}
@keyframes loading {
  from {
    background-position: 0 -400px;
  }
  to {
    background-position: -7px -400px;
  }
}


</style>