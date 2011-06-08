<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>

<?php
if (get_setting('fs_ads_top_banner') && get_setting('fs_ads_top_banner_active') && get_setting('fs_ads_top_banner_reload'))
	echo '<div class="ads iframe banner" id="ads_iframe_top_banner"><iframe marginheight="0" marginwidth="0" frameborder="0" src="' . site_url() . 'content/ads/ads_top.html' . '"></iframe></div>';
?>

<?php
if (get_setting('fs_ads_top_banner') && get_setting('fs_ads_top_banner_active') && !get_setting('fs_ads_top_banner_reload'))
	echo '<div class="ads static banner ftop" id="ads_static_top_banner">' . get_setting('fs_ads_top_banner') . '</div>';
?>


<style type="text/css">
	.panel {width:1000px; margin: 0 auto;} 
	.ads.banner{width:980px !important; max-width:none; text-align:center;}
</style>

<div class="panel">
	<div class="large nooverflow">
		<h1 class="title dnone"><?php echo $comic->url() ?> :: <?php echo $chapter->url() ?></h1>
		<div class="title fleft dropdown_parent"><div class="text"><?php echo $comic->url() ?> ⤵</div>
			<?php
			echo '<ul class="dropdown">';
			foreach ($comics->all as $co) {
				echo '<li>' . $co->url() . '</li>';
			}
			echo '</ul>'
			?>
		</div>	
		<div class="title fleft dropdown_parent"><div class="text"><?php echo $chapter->url() ?> ⤵</div>
			<?php
			echo '<ul class="dropdown">';
			foreach ($chapters->all as $ch) {
				echo '<li>' . $ch->url() . '</li>';
			}
			echo '</ul>'
			?>
		</div>
		<div class="title fright dropdown_parent dropdown_right"><div class="text"><?php echo count($pages); ?> ⤵</div>
			<?php
			$url = $chapter->href();
			echo '<ul class="dropdown" style="width:90px;">';
			for ($i = 1; $i <= count($pages); $i++) {
				echo '<li><a href="' . $url . 'page/' . $i . '" onClick="changePage(' . $i . '); return false;">' . _("Page") . ' ' . $i . '</a></li>';
			}
			echo '</ul>'
			?></div>
		<div class="numbers fright">

			<div class="divider fright"></div>
			<div class="current fright"><?php
			//for ($i = (($val = $current_page - 3) <= 0)?(1):$val; $i <= count($pages) && $i < $current_page + 3; $i++) {
			for ($i = (($val = $current_page + 2) >= count($pages)) ? (count($pages)) : $val; $i > 0 && $i > $current_page - 3; $i--) {
				$current = ((count($pages) / 100 > 1 && $i / 100 < 1) ? '0' : '') . ((count($pages) / 10 > 1 && $i / 10 < 1) ? '0' : '') . $i;
				echo '<div class="number number_' . $i . ' ' . (($i == $current_page) ? 'current_page' : '') . '"><a href="' . $chapter->href . 'page/' . $i . '">' . $current . '</a></div>';
			}
			?></div>
		</div>
		<?php /*
		  <div id="pagelist" style="display:none;">
		  <div class="title"><?php echo _('List of this chapter\'s pages') ?></div>
		  <div class="images"><table><tr>
		  <?php
		  foreach ($pages as $key => $page) {
		  echo '<td><a href="#" onClick="changePage(' . $key . ', \'TRUE\');"><img id="thumb_' . $key . '" src="' . $page['thumb_url'] . '" /></a></td>';
		  }
		  ?>
		  </tr></table></div>
		  </div> */ ?>
		<div class="clearer"></div>

	</div>
</div>




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
		<a href="<?php echo $chapter->next_page($current_page); ?>" onClick="return nextPage();" >
			<div class="preview"><img src="<?php echo $pages[$current_page - 1]['thumb_url'] ?>" width="<?php echo $pages[$current_page - 1]['width'] ?>" height="<?php echo $pages[$current_page - 1]['height'] ?>" /></div>
			<img class="open" src="<?php echo $pages[$current_page - 1]['url'] ?>" width="<?php echo $pages[$current_page - 1]['width'] ?>" height="<?php echo $pages[$current_page - 1]['height'] ?>" />
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

<style type="text/css">
	#page{margin: 10px auto 0px;}
	#ads_iframe_top_banner, #ads_static_top_banner {margin:10px auto;}
	<?php
	if (get_setting('fs_ads_left_banner_active')) {
		echo '.panel,#ads_iframe_top_banner, #ads_static_bottom_banner,#ads_iframe_bottom_banner, #ads_static_top_banner  {position:relative; left:95px;}';
	}
	?>
</style>


<div class="clearer"></div>
<script src="<?php echo site_url(); ?>assets/js/jquery.plugins.js"></script>
<script type="text/javascript">


	var pages = <?php echo json_encode($pages); ?>;

	var next_chapter = "<?php echo $next_chapter; ?>";
	
	var preload_next = 7;

	var preload_back = 2;

	var current_page = <?php echo $current_page - 1; ?>;
	
	function changePage(id, noscroll)
	{
		id = parseInt(id);
		if(id > pages.length-1) 
		{
			location.href = next_chapter;
			return false;
		}
		if(id < 0){
			current_page = 0;
			id = 0;
		} 
		
		preload(id);
		current_page = id;
		next = parseInt(id+1);
		jQuery("html, body").stop(true,true);
		if(!noscroll) jQuery.scrollTo('.panel', 300, {'offset':{'top':-6}});
		
		
		//jQuery("#page").stop(true);

		if (pages[id].width > 1000 && ((pages[id].width)/(pages[id].height)) > 1.2) {
			if(parseInt(pages[id].height) < 1200) {
				width = parseInt(pages[id].width);
				height = parseInt(pages[id].height);
			}
			else { 
				height = 1200;
				width = parseInt(pages[id].width);
				width = (height*width)/(parseInt(pages[id].height)); // width : height = width2 : 1200
			}
			jQuery("#page").css({'max-width': 'none', 'overflow':'auto'});
			jQuery("#page").animate({scrollLeft:9000},400);
			jQuery("#page .inner img.open").css({'max-width':'none', 'height':height});
			jQuery('#page .inner .preview img').attr({width:width, height:height});
			jQuery('#page .inner img.open').attr({width:width, height:height});
			isSpread = true;
		}
		else{
			if(parseInt(pages[id].width) < 1000) {
				width = parseInt(pages[id].width);
				height = parseInt(pages[id].height);
			}
			else { 
				width = 1000;
				height = parseInt(pages[id].height);
				height = (height*width)/(parseInt(pages[id].width));
			}
			jQuery("#page").css({'max-width':(width + 10) + 'px','overflow':'hidden'});
			jQuery("#page .inner img.open").css({'height':height, 'max-width':'99%'});
			jQuery('#page .inner .preview img').attr({width:width, height:height});
			jQuery('#page .inner img.open').attr({width:width, height:height});
			isSpread = false;
		}
		
		if(pages[id].loaded !== true) {
			jQuery('#page .inner img.open').css({'opacity':'0'});
			jQuery('#page .inner .preview img').attr('src', pages[id].thumb_url);
			jQuery('#page .inner img.open').attr('src', pages[id].thumb_url);
		}
		else {
			jQuery('#page .inner .preview img').attr('src', pages[id].thumb_url);
			jQuery('#page .inner img.open').attr('src', pages[id].url);
			jQuery('#page .inner img.open').css({'opacity':'1'});
		}
		
		
		update_numberPanel();
		jQuery('#pagelist .images').scrollTo(jQuery('#thumb_' + id).parent(), 400);
		jQuery('#pagelist .current').removeClass('current');
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
		return false;
	}

	function nextPage()
	{
		current_page++;
		changePage(current_page);
		return false;
	}
	
	function prevPage()
	{
		current_page--;
		changePage(current_page);
		return false;
	}
	
	function preload(id)
	{
		array = [];
		arraythumb = [];
		arraydata = [];
		for(i = -preload_back; i < preload_next; i++)
		{
			if(id+i >= 0 && id+i < pages.length)
			{
				array.push(pages[(id+i)].url);
				arraythumb.push(pages[(id+i)].thumb_url);
				arraydata.push(id+i);
			}
		}
		for(i = -preload_back*2; i < preload_next*2; i++)
		{
			if(id+i >= 0 && id+i < pages.length)
			{
				arraythumb.push(pages[(id+i)].thumb_url);
			}
		}
		jQuery.preload(arraythumb, {
			threshold: 40,
			enforceCache: true});
		
		jQuery.preload(array, {
			threshold: 40,
			enforceCache: true,
			onComplete:function(data)
			{
				pages[arraydata[data.index]].loaded = true;
				jQuery('#thumb_'+ arraydata[data.index]).addClass('loaded');
				jQuery('.numbers .number_'+ (arraydata[data.index]+1)).addClass('loaded');
				if(current_page == arraydata[data.index])
				{
					jQuery('#page .inner img.open').animate({'opacity':'1.0'}, 800);
					jQuery('#page .inner img.open').attr('src', pages[current_page].url);
				}
			}
	
		});
	}
	
	function create_numberPanel()
	{
		result = "";
		for (j = pages.length+1; j > 0; j--) {
			nextnumber = ((j/1000 < 1 && pages.length >= 1000)?'0':'') + ((j/100 < 1 && pages.length >= 100)?'0':'') + ((j/10 < 1 && pages.length >= 10)?'0':'') + j;
			result += "<div class='number number_"+ j +" dnone'><a href='<?php echo ($chapter->href . 'page/'); ?>" + j + "' onClick='changePage("+(j-1)+"); return false;'>"+nextnumber+"</a></div>"; 
		}
		jQuery(".numbers .current").html(result);
	}
	
	function update_numberPanel()
	{
		jQuery('.number.current_page').removeClass('current_page');
		jQuery('.numbers .number_'+(current_page+1)).addClass('current_page');
		jQuery('.numbers .number').addClass('dnone');
		for (i = ((val = current_page - 1) <= 0)?(1):val; i <= pages.length && i < current_page + 4; i++) {
			jQuery('.numbers .number_'+i).removeClass('dnone');
		}
	}
	
	function chapters_dropdown()
	{
		location.href = jQuery('#chapters_dropdown').val();
	}
	
	function togglePagelist()
	{
		jQuery('#pagelist').slideToggle();
		jQuery.scrollTo('#pagelist', 300);
		jQuery('#panel').scrollTo('#thumb_' + current_page, 400);
	}
	
	
	isSpread = false;
	function leftMove(e) {
		if (!isSpread) prevPage();
		else jQuery('#page').scrollTo("-=60",100,{axis:"x"});
	}
	
	function rightMove(e) {
		jQuery('#page').scrollTo("+=60",100,{axis:"x"});
	}


	jQuery(document).ready(function() {
		
		if(!jQuery.browser.webkit && !jQuery.browser.msie){
			
			jQuery(document).keypress(function(e){
				if(isSpread) {
					jQuery("html, body").stop(true,true);		
					jQuery("#page").stop(true,true);		
					if(e.keyCode==37 || e.which==97)
					{
						leftMove(e);
					}
					if(e.keyCode==39 || e.which==100) 
					{
						rightMove(e);
					}
				}
				
				if(e.which==115) jQuery.scrollTo("+=60",100,{axis:"y"});
				if(e.which==119) jQuery.scrollTo("-=60",100,{axis:"y"});
				if(e.keyCode==40) {
					e.preventDefault()
					jQuery.scrollTo("+=60",100);
				}
				if(e.keyCode==38) {
					e.preventDefault();
					jQuery.scrollTo("-=60",100);
				}
			});
			jQuery(document).keyup(function(e){
				if(e.keyCode==37 || e.which==65)
				{
					if(!isSpread) prevPage();
					else if(e.timeStamp - timeStamp37 < 400 && e.timeStamp - timeStamp37 > 150) prevPage();
					timeStamp37 = e.timeStamp;
				}
				if(e.keyCode==39 || e.which==68) 
				{
					if(!isSpread) nextPage();
					else if(e.timeStamp - timeStamp39 < 400 && e.timeStamp - timeStamp39 > 150) nextPage();
					timeStamp39 = e.timeStamp;
				}
			});
		}
		else {
			jQuery(document).keydown(function(e){
				if(isSpread) {
					jQuery("html, body").stop(true,true);		
					jQuery("#page").stop(true,true);
					if(e.keyCode==37 || e.keyCode==65)
					{
						leftMove(e);
					}
					if(e.keyCode==39 || e.keyCode==68) 
					{
						rightMove(e);
					}
				}
				if(e.which==83) jQuery.scrollTo("+=60",100,{axis:"y"});
				if(e.which==87) jQuery.scrollTo("-=60",100,{axis:"y"});
				if(e.which==40) {
					e.preventDefault()
					jQuery.scrollTo("+=60",100);
				}
				if(e.which==38) {
					e.preventDefault();
					jQuery.scrollTo("-=60",100);
				}
			});
			jQuery(document).keyup(function(e){
				if(e.keyCode==37 || e.which==65)
				{
					if(!isSpread) prevPage();
					else if(e.timeStamp - timeStamp37 < 400 && e.timeStamp - timeStamp37 > 150) prevPage();
					timeStamp37 = e.timeStamp;
				}
				if(e.keyCode==39 || e.which==68) 
				{
					if(!isSpread) nextPage();
					else if(e.timeStamp - timeStamp39 < 400 && e.timeStamp - timeStamp39 > 150) nextPage();
					timeStamp39 = e.timeStamp;
				}
			});
		}
		
		timeStamp37 = 0;
		timeStamp39 = 0;
		
		create_numberPanel();
		changePage(current_page);
		
	});
</script>