<?php
$this->buttoner[] = array(
	'text' => 'Delete chapter',
	'href' => site_url('/admin/comics/delete/chapter/' . $chapter->id),
	'plug' => 'Do you really want to delete this chapter and its pages?'
);

echo buttoner();

echo form_open();
echo $table;
echo form_close();
?>

<div class="section">Pages:</div>


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
						
				jQuery('#file_upload').uploadifySettings( 'postData', {
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
			jQuery('#file_upload').uploadify({
				'swf'  : '<?php echo site_url(); ?>assets/uploadify/uploadify.swf',
				'uploader'    : '<?php echo site_url('/admin/comics/upload/compressed_chapter'); ?>',
				'cancelImage' : '<?php echo site_url(); ?>assets/uploadify/uploadify-cancel.png',
				'checkExisting' : false,
				'preventCaching' : false,
				'multi' : true,
				'buttonText' : 'Upload zip and images',
				'width': 200,
				'auto'      : true,
				'requeueErrors' : true,
				'postData' : {}
			});
		});
	
		updateSession();
	
	</script>
	<div id="file_upload">Upload</div>
</div>
<?php
$this->buttoner = array();
$this->buttoner[] = array('href' => 'JavaScript:deleteAllPages()', 'text' => 'Delete all pages', 'plug' => 'Do you really want to delete all the images in this chapter?');
echo buttoner();
?>

<div class="list pages">
    <table>
        <tr>
			<?php
			$count = 0;
			foreach ($pages as $item) {
				$count++;
				echo '<td>
                <div class="controls gbutton" onclick="deleteImage(' . $item['id'] . ')">Delete</div>
                <img id="image_' . $item['id'] . '" src="' . $item["thumb_url"] . '" />
             </td>';
				if ($count % 4 == 0)
					echo '</tr><tr>';
			}
			?>     </tr>
    </table>

</div>