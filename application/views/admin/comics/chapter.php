<?php
$this->buttoner[] = array(
	'text' => _('Delete chapter'),
	'href' => site_url('/admin/comics/delete/chapter/' . $chapter->id),
	'plug' => _('Do you really want to delete this chapter and its pages?')
);

$this->buttoner[] = array(
	'text' => _('Read chapter'),
	'href' => $chapter->href()
);

echo buttoner();

echo form_open();
echo $table;
echo form_close();
?>

<div class="section"><?php echo _("Pages") ?>:</div>


<?php
$session_name = $this->session->get_js_session(TRUE);
$session_data = $this->session->get_js_session();
?>

<div class="uploadify">
	<link href="<?php echo site_url(); ?>assets/uploadify/uploadify.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo site_url(); ?>assets/uploadify/jquery.uploadify.js"></script>
	<script type="text/javascript">
		
		function deleteImage(id)
		{
			jQuery.post('<?php echo site_url('/admin/comics/delete/page/') ?>', {id: id}, function(){
				jQuery('#image_' + id).hide();
			});
		}
	
		function deleteAllPages()
		{
			jQuery.post('<?php echo site_url('/admin/comics/delete/allpages/') ?>', {id: <?php echo $chapter->id ?>}, function(){
				location.reload();
			});
		}
	
		function updateSession()
		{
			jQuery.post('<?php echo site_url('/admin/comics/get_sess_id'); ?>', 
			function(result){
				
				jQuery('#file_upload_flash').uploadifySettings( 'postData', {
					'ci_sessionz' : result.session, 
					'<?php echo $this->security->get_csrf_token_name(); ?>' : result.csrf, 
					'chapter_id' : <?php echo $chapter->id; ?>,
					'uploader' : 'uploadify',
					'overwrite' : '1'
				}, false );
				setTimeout('updateSession()', 6000);
			}, 'json');
		}
		
		jQuery(document).ready(function() {
			jQuery('#file_upload_flash').uploadify({
				'swf'  : '<?php echo site_url(); ?>assets/uploadify/uploadify.swf',
				'uploader'    : '<?php echo site_url('/admin/comics/upload/compressed_chapter'); ?>',
				'cancelImage' : '<?php echo site_url(); ?>assets/uploadify/uploadify-cancel.png',
				'checkExisting' : false,
				'preventCaching' : false,
				'multi' : true,
				'buttonText' : '<?php echo _('Upload zip and images'); ?>',
				'width': 200,
				'auto'      : true,
				'requeueErrors' : true,
				'uploaderType'    : 'flash',
				'postData' : {},
				'onSWFReady'  : function() {
					updateSession();
				}
			});
		});

	</script>
	
	<div id="file_upload_flash"></div>
</div>

<div id="fileupload">
	<link href="<?php echo site_url(); ?>assets/jquery-file-upload/jquery-ui.css" rel="stylesheet" id="theme" />
	<link href="<?php echo site_url(); ?>assets/jquery-file-upload/jquery.fileupload-ui.css" rel="stylesheet" />
	<?php echo form_open_multipart(""); ?>
        <div class="fileupload-buttonbar">
		<label class="fileinput-button">
			<span>Add files...</span>
			<input type="file" name="Filedata" multiple>
		</label>
		<button type="submit" class="start">Start upload</button>
		<button type="reset" class="cancel">Cancel upload</button>
		<button type="button" class="delete">Delete files</button>
	</div>
	<?php echo form_close(); ?>
	<div class="fileupload-content">
		<table class="files"></table>
		<div class="fileupload-progressbar"></div>
	</div>
</div>
<script id="template-upload" type="text/x-jquery-tmpl">
    <tr class="template-upload{{if error}} ui-state-error{{/if}}">
        <td class="preview"></td>
        <td class="name">${name}</td>
        <td class="size">${sizef}</td>
        {{if error}}
            <td class="error" colspan="2">Error:
                {{if error === 'maxFileSize'}}File is too big
                {{else error === 'minFileSize'}}File is too small
                {{else error === 'acceptFileTypes'}}Filetype not allowed
                {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                {{else}}${error}
                {{/if}}
            </td>
        {{else}}
            <td class="progress"><div></div></td>
            <td class="start"><button>Start</button></td>
        {{/if}}
        <td class="cancel"><button>Cancel</button></td>
    </tr>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
    <tr class="template-download{{if error}} ui-state-error{{/if}}">
        {{if error}}
            <td></td>
            <td class="name">${name}</td>
            <td class="size">${sizef}</td>
            <td class="error" colspan="2">Error:
                {{if error === 1}}File exceeds upload_max_filesize (php.ini directive)
                {{else error === 2}}File exceeds MAX_FILE_SIZE (HTML form directive)
                {{else error === 3}}File was only partially uploaded
                {{else error === 4}}No File was uploaded
                {{else error === 5}}Missing a temporary folder
                {{else error === 6}}Failed to write file to disk
                {{else error === 7}}File upload stopped by extension
                {{else error === 'maxFileSize'}}File is too big
                {{else error === 'minFileSize'}}File is too small
                {{else error === 'acceptFileTypes'}}Filetype not allowed
                {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                {{else error === 'uploadedBytes'}}Uploaded bytes exceed file size
                {{else error === 'emptyResult'}}Empty file upload result
                {{else}}${error}
                {{/if}}
            </td>
        {{else}}
            <td class="preview">
                {{if thumbnail_url}}
                    <a href="${url}" target="_blank"><img src="${thumbnail_url}"></a>
                {{/if}}
            </td>
            <td class="name">
                <a href="${url}"{{if thumbnail_url}} target="_blank"{{/if}}>${name}</a>
            </td>
            <td class="size">${sizef}</td>
            <td colspan="2"></td>
        {{/if}}
        <td class="delete">
            <button data-type="${delete_type}" data-url="${delete_url}" data-id="${delete_data}">Delete</button>
        </td>
    </tr>
</script>
<script src="<?php echo site_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo site_url(); ?>assets/js/jquery.tmpl.js"></script>
<script src="<?php echo site_url(); ?>assets/jquery-file-upload/jquery.fileupload.js"></script>
<script src="<?php echo site_url(); ?>assets/jquery-file-upload/jquery.fileupload-ui.js"></script>
<script src="<?php echo site_url(); ?>assets/jquery-file-upload/jquery.iframe-transport.js"></script>

<?php
$this->buttoner = array();
$this->buttoner[] = array(
	'text' => _('Delete all pages'),
	'href' => site_url('/admin/comics/delete/allpages/' . $chapter->id),
	'plug' => _('Do you really want to delete all the images in this chapter?'));
echo buttoner();
?>

<script>

	$(function () {
		$('#fileupload').fileupload({
			url: '<?php echo site_url('/admin/comics/upload/compressed_chapter'); ?>',
			sequentialUploads: true,
			formData: [
				{
					name: 'chapter_id',
					value: <?php echo $chapter->id; ?>
				}, {
					name: 'uploader',
					value: 'jquery-file-upload'
				}, {
					name: 'overwrite',
					value: '1'
				}
			]
		});

		$.post('<?php echo site_url('/admin/comics/get_file_objects'); ?>', { id : <?php echo $chapter->id; ?> }, function (files) {
			var fu = $('#fileupload').data('fileupload');
			fu._adjustMaxNumberOfFiles(-files.length);
			fu._renderDownload(files)
				.appendTo($('#fileupload .files'))
				.fadeIn(function () {
				$(this).show();
			});
		});

		$('#fileupload .files a:not([target^=_blank])').live('click', function (e) {
			e.preventDefault();
			$('<iframe style="display:none;"></iframe>')
				.prop('src', this.href)
				.appendTo('body');
		});

	});
	
</script>

<div class="list pages">
	<?php
	$count = 0;
	foreach ($pages as $item) {
		$count++;
		echo '<div class="element">
                <div class="controls gbutton" onclick="deleteImage(' . $item['id'] . ')">' . _('Delete') . '</div>
                <img id="image_' . $item['id'] . '" src="' . $item["thumb_url"] . '" />
             </div>';
	}
	?>
</div>