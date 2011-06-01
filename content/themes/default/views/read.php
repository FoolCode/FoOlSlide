<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>
<style type="text/css">.panel {width:1000px; margin: 0 auto;}</style>
<div class="panel">
	<div class="large">
		<h1 class="title"><?php echo $comic->url() ?> – <?php echo $chapter->url() ?></h1>
	</div>
</div>

<div id="pagelist" style="display:none;">
	<div class="title"><?php echo _('List of this chapter\'s pages') ?></div>
	<div class="images"><table><tr>
				<?php
				foreach ($pages as $key => $page) {
					echo '<td><a href="#" onClick="changePage(' . $key . ', \'TRUE\');"><img id="thumb_' . $key . '" src="' . $page['thumb_url'] . '" /></a></td>';
				}
				?>
			</tr></table></div>
</div>

<?php
if (get_setting('fs_ads_top_banner') && get_setting('fs_ads_top_banner_active') && get_setting('fs_ads_top_banner_reload'))
	echo '<div class="ads iframe banner" id="ads_iframe_top_banner"><iframe marginheight="0" marginwidth="0" frameborder="0" src="' . site_url() . 'content/ads/ads_top.html' . '"></iframe></div>';
?>

<?php
if (get_setting('fs_ads_top_banner') && get_setting('fs_ads_top_banner_active') && !get_setting('fs_ads_top_banner_reload'))
	echo '<div class="ads static banner ftop" id="ads_static_top_banner">' . get_setting('fs_ads_top_banner') . '</div>';
?>

<?php
if (get_setting('fs_ads_left_banner') && get_setting('fs_ads_left_banner_active') && !get_setting('fs_ads_left_banner_reload'))
	echo '<div class="ads static vertical fleft" id="ads_static_left_banner">' . get_setting('fs_ads_left_banner') . '</div>';
?>

<?php
if (get_setting('fs_ads_left_banner') && get_setting('fs_ads_left_banner_active') && get_setting('fs_ads_left_banner_reload'))
	echo '<div class="ads iframe vertical fleft" id="ads_iframe_left_banner"><iframe marginheight="0" marginwidth="0" frameborder="0" src="' . site_url() . 'content/ads/ads_left.html' . '"></iframe></div>';
?>

<div id="page">

	<div class="inner">
		<a href="<?php echo $chapter->next_page($current_page); ?>" onClick="return changePage('<?php echo $current_page; ?>');" >
			<img src="<?php echo $pages[$current_page - 1]['url'] ?>"  />
		</a>
	</div>
</div>

<?php
if (get_setting('fs_ads_bottom_banner') && get_setting('fs_ads_bottom_banner_active') && get_setting('fs_ads_bottom_banner_reload'))
	echo '<div class="ads iframe banner" id="ads_iframe_bottom_banner"><iframe marginheight="0" marginwidth="0" frameborder="0" src="' . site_url() . 'content/ads/ads_bottom.html' . '"></iframe></div>';
?>

<?php
if (get_setting('fs_ads_bottom_banner') && get_setting('fs_ads_bottom_banner_active') && !get_setting('fs_ads_bottom_banner_reload'))
	echo '<div class="ads static banner fbottom" id="ads_static_bottom_banner">' . get_setting('fs_ads_bottom_banner') . '</div>';
?>

<?php
if (!(get_setting('fs_ads_left_banner') && get_setting('fs_ads_left_banner_active'))) {
	echo '<style type="text/css">
				#page{margin: 10px auto 0px;}
				#ads_iframe_top_banner, #ads_static_top_banner {margin:10px auto;}
			</style>';
}
?>

<div id="widget">
	<div class="initnumber"><?php echo $current_page ?></div>
	<div class="on">on</div>
	<div class="finalnumber"><?php echo count($pages); ?></div>
	<div id="myPagelist"><a href="#" onClick="togglePagelist(); return false;" class="gbutton"><?php echo _('Pagelist') ?></a></div>
</div>

<div class="clearer"></div>
<script src="<?php echo site_url(); ?>assets/js/jquery.plugins.js"></script>
<script type="text/javascript">


	var pages = <?php echo json_encode($pages); ?>;

	var next_chapter = "<?php echo $next_chapter; ?>";
	
	var preload_next = 7;

	var preload_back = 1;

	var current_page = <?php echo $current_page - 1; ?>;
	
	function changePage(id, noscroll)
	{
		id = parseInt(id);
		if(id > pages.length-1) 
		{
			location.href = next_chapter;
			return false;
		}
		
		preload(id);
		next = parseInt(id+1);
		
		if (pages[id].width > 1000 && ((pages[id].width)/(pages[id].height)) > 1.2) {
			jQuery("#page").css("max-height", '1200px');
			jQuery("#page .inner img").css({"max-height": '1200px', 'width':'auto'});
			isSpread = true;
		}
		else{
			jQuery("#page").attr('style', '');
			jQuery("#page .inner img").attr('style','');
			isSpread = false;
		}
		
		jQuery("html, body").stop(true,true);
		if(!noscroll) jQuery.scrollTo('#page', 300);
		jQuery('#page .inner img').attr('src', pages[id].url);
		jQuery('#page .large img').attr('src', pages[id].url);
		jQuery('#page .inner a').attr('onClick', 'return changePage(\'' + next + '\')');
		jQuery('.initnumber').text(next);
		jQuery('#pagelist .images').scrollTo(jQuery('#thumb_' + id).parent(), 400);
		jQuery('.current').removeClass('current');
		jQuery('#thumb_' + id).addClass('current');
<?php
if (get_setting('fs_ads_top_banner') && get_setting('fs_ads_top_banner_active') && get_setting('fs_ads_top_banner_reload'))
	echo 'jQuery("#ads_iframe_top_banner iframe").attr("src","' . site_url() . 'content/ads/ads_top.html");';
?>
<?php
if (get_setting('fs_ads_left_banner') && get_setting('fs_ads_left_banner_active') && get_setting('fs_ads_left_banner_reload'))
	echo 'jQuery("#ads_iframe_left_banner iframe").attr("src","' . site_url() . 'content/ads/ads_left.html");';
?>
<?php
if (get_setting('fs_ads_bottom_banner') && get_setting('fs_ads_bottom_banner_active') && get_setting('fs_ads_bottom_banner_reload'))
	echo 'jQuery("#ads_iframe_bottom_banner iframe").attr("src","' . site_url() . 'content/ads/ads_bottom.html");';
?>
				current_page = id;
				blinker();
				lightMyFire();
		
		
				return false;
			}

			function nextPage()
			{
				current_page++;
				changePage(current_page);
			}
	
			function prevPage()
			{
				current_page--;
				changePage(current_page);
			}
	
			function preload(id)
			{
				array = [];
				arraydata = [];
				for(i = -preload_back; i < preload_next; i++)
				{
					if(id+i >= 0 && id+i < pages.length)
					{
						array.push(pages[(id+i)].url);
						arraydata.push(id+i);
					}
				}
				jQuery.preload(array, {
					threshold: 200,
					enforceCache: true,
					onComplete:function(data)
					{
						pages[arraydata[data.index]].loaded = true;
						jQuery('#thumb_'+ arraydata[data.index]).addClass('loaded');
						lightMyFire();
					}
	
				});
			}
	
			function blinker()
			{
				if(pages[current_page].loaded == undefined)
				{
					jQuery('#myLoading').fadeIn(200).delay(500).fadeOut(200);
					setTimeout('blinker()', 1000);
				}
		
			}
	
			function countNextLoaded()
			{
				result = 0;
				for(i = current_page; i < pages.length; i++)
				{
					if(pages[i].loaded) result++;
				}
				return result;
			}

			function lightMyFire()
			{
				num = countNextLoaded();
				if(num > 12) num = 12;
		
				light = "";
				for(i = 1; i < num; i++)
				{
					light += '<div class="light"></div>';
				}

				jQuery('#myFire').html(light);
			}
	
			function togglePagelist()
			{
				jQuery('#pagelist').slideToggle();
				jQuery.scrollTo('#pagelist', 300);
				jQuery('#pagelist .images').scrollTo('#thumb_' + current_page, 400);
			}

			isSpread = false;

			jQuery(document).ready(function() {
		
				jQuery(document).keyup(function(e){
					if(e.keyCode==37 || e.keyCode==65)
					{
						if (!isSpread && e.timeStamp - timeStamp37 > 100) prevPage();
						else if(e.timeStamp - timeStamp37 < 400 && e.timeStamp - timeStamp37 > 100) prevPage();
						timeStamp37 = e.timeStamp;
					}
					if(e.keyCode==39 || e.keyCode==68) 
					{
						if (!isSpread && e.timeStamp - timeStamp39 > 100) nextPage();
						else if(e.timeStamp - timeStamp39 < 400 && e.timeStamp - timeStamp39 > 100) nextPage();
						timeStamp39 = e.timeStamp;
					}
				});

				jQuery(document).keypress(function(e){
					jQuery("html, body").stop(true,true);			
					if(e.which==100) jQuery.scrollTo("+=100",100,{axis:"x"});
					if(e.which==97) jQuery.scrollTo("-=100",100,{axis:"x"});
					if(e.which==115) jQuery.scrollTo("+=100",200,{axis:"y"});
					if(e.which==119) jQuery.scrollTo("-=100",100,{axis:"y"});
				});

				timeStamp37 = 0;
				timeStamp39 = 0;

				changePage(current_page);
			});
</script>