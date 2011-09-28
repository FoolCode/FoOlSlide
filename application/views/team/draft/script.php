<link rel="stylesheet" href="<?php echo site_url() ?>assets/foolproofr/foolproofr.css?v=<?php echo get_setting('fs_priv_version') ?>">
<script type="text/javascript" src="<?php echo site_url() ?>assets/foolproofr/foolproofr.js?v=<?php echo get_setting('fs_priv_version') ?>"></script>
<script type="text/javascript">
	$(window).load(function(){
		$("#proofrme").foolproofr(
<?php
echo json_encode(array(
	"fitImage" => true,
	"updateUrl" => site_url('team/' . $this->teamc->team->stub . '/draft/sync_script'),
	"chapter_id" => $chapter_id,
	"page_number" => $page_number,
	"username" => $this->tank_auth->get_username(),
	"user_id" => $this->tank_auth->get_user_id()
));
?>
						);
						});
</script>

<div class="sidebar">herp</div>
<div class="content">
	<div id="proofrme">
		<img src="<?php echo $page_url ?>" />
	</div>
</div>