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
	.panel {max-width:1000px; margin: 0 auto;} 
	.ads.banner{max-width:980px !important; max-width:none; text-align:center;}
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
		<div class="title fleft dropdown_parent"><div class="text"><?php echo '<a href="'.$chapter->href().'">'. ((strlen($chapter->title()) > 58)?(substr($chapter->title(), 0, 50).'...'):$chapter->title()) . '</a>' ?> ⤵</div>
			<?php
			echo '<ul class="dropdown">';
			foreach ($chapters->all as $ch) {
				echo '<li>' . $ch->url() . '</li>';
			}
			echo '</ul>'
			?>
		</div>
		<div class="title fleft icon_wrapper dnone" ><img class="icon off" src="<?php echo glyphish(181); ?>" /><img class="icon on" src="<?php echo glyphish(181, TRUE); ?>" /></div>
		<?php echo $chapter->download_url(NULL, "fleft"); ?>

		<div class="title fright dropdown_parent dropdown_right"><div class="text"><?php echo count($pages); ?> ⤵</div>
			<?php
			$url = $chapter->href();
			echo '<ul class="dropdown" style="width:90px;">';
			for ($i = 1; $i <= count($pages); $i++) {
				echo '<li><a href="' . $url . 'page/' . $i . '" onClick="changePage(' . ($i - 1) . '); return false;">' . _("Page") . ' ' . $i . '</a></li>';
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
			<div class="preview"><img src="<?php echo $pages[$current_page - 1]['thumb_url'] ?>" width="<?php echo ($pages[$current_page - 1]['width']<1000)?$pages[$current_page - 1]['width']:1000; ?>" height="<?php echo ($pages[$current_page - 1]['width']<1000)?($pages[$current_page - 1]['width']):1000; ?>" /></div>
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
	#ads_iframe_top_banner, #ads_static_top_banner, #ads_iframe_bottom_banner, #ads_static_bottom_banner {margin:10px auto;}
	<?php
	if (get_setting('fs_ads_left_banner_active')) {
		echo '.panel,#ads_iframe_top_banner, #ads_static_bottom_banner,#ads_iframe_bottom_banner, #ads_static_top_banner  {position:relative; left:95px;}';
	}
	?>
</style>


<div class="clearer"></div>
<script src="<?php echo site_url(); ?>assets/js/jquery.plugins.js"></script>
<script type="text/javascript">

	var title = document.title;
	
	var pages = <?php echo json_encode($pages); ?>;

	var next_chapter = "<?php echo $next_chapter; ?>";
	
	var preload_next = 7;

	var preload_back = 2;

	var current_page = <?php echo $current_page - 1; ?>;
	
	var initialized = false;
	
	var baseurl = '<?php echo $chapter->href() ?>';
	
	var gt_page = '<?php echo addslashes(_("Page")) ?>';
	
	function changePage(id, noscroll, nohash)
	{
		id = parseInt(id);
		if (initialized && id == current_page)
			return false;
		initialized = true;
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
		if(!noscroll) jQuery.scrollTo('.panel', 430, {'offset':{'top':-6}});
		
		
		//jQuery("#page").stop(true);

		if(pages[id].loaded !== true) {
			jQuery('#page .inner img.open').css({'opacity':'0'});
			jQuery('#page .inner .preview img').attr('src', pages[id].thumb_url);
			jQuery('#page .inner img.open').attr('src', pages[id].thumb_url);
		}
		else {
			jQuery('#page .inner img.open').css({'opacity':'1'});
			jQuery('#page .inner .preview img').attr('src', pages[id].thumb_url);
			jQuery('#page .inner img.open').attr('src', pages[id].url);
		}
		
		resizePage(id);
		
		if(!nohash) History.pushState(null, null, baseurl+'page/' + (current_page + 1));
		document.title = gt_page+' ' + (current_page+1) + ' :: ' + title;
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


	function resizePage(id) {
		var doc_width = jQuery(document).width();
		var page_width = parseInt(pages[id].width);
		var page_height = parseInt(pages[id].height);
		var nice_width = 980;
		var perfect_width = 980;
		
		if(doc_width > 1200) {
			nice_width = 1120;
			perfect_width = 1000;
		}
		if(doc_width > 1600) {
			nice_width = 1400;
			perfect_width = 1300;
		}
		if(doc_width > 1800) {
			nice_width = 1600;
			perfect_width = 1500;
		}
		
		
		if (page_width > nice_width && (page_width/page_height) > 1.2) {
			if(page_height < 1400) {
				width = page_width;
				height = page_height;
			}
			else { 
				height = 1400;
				width = page_width;
				width = (height*width)/(page_height);
			}
			jQuery("#page").css({'max-width': 'none', 'overflow':'auto'});
			jQuery("#page").animate({scrollLeft:9000},400);
			jQuery('#page .inner .preview img').attr({width:width, height:height});
			jQuery("#page .inner img.open").css({'max-width':'99999px'});
			jQuery('#page .inner img.open').attr({width:width, height:height});
			if(jQuery("#page").width() < jQuery("#page .inner img.open").width()) {
				isSpread = true;
			}
			else {
				jQuery("#page").css({'max-width': width+10, 'overflow':'hidden'});
				isSpread = false;
			}
		}
		else{
			if(page_width < nice_width && doc_width > page_width + 10) {
				width = page_width;
				height = page_height;
			}
			else { 
				width = (doc_width > perfect_width) ? perfect_width : doc_width - 10;
				height = page_height; 
				height = (height*width)/page_width;
			}
			jQuery('#page .inner .preview img').attr({width:width, height:height});
			jQuery('#page .inner img.open').attr({width:width, height:height});
			jQuery("#page").css({'max-width':(width + 10) + 'px','overflow':'hidden'});
			jQuery("#page .inner img.open").css({'max-width':'100%'});
			isSpread = false;
		}
	}

	function nextPage()
	{
		changePage(current_page+1);
		return false;
	}
	
	function prevPage()
	{
		changePage(current_page-1);
		return false;
	}
	
	function preload(id)
	{
		var array = [];
		var arraythumb = [];
		var arraydata = [];
		for(i = -preload_back; i < preload_next; i++)
		{
			if(id+i >= 0 && id+i < pages.length)
			{
				array.push(pages[(id+i)].url);
				arraythumb.push(pages[(id+i)].thumb_url);
				arraydata.push(id+i);
			}
		}
		
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
		
		jQuery.preload(arraythumb, {
			threshold: 40,
			enforceCache: true,
			onComplete:function(data)
			{
			}
		});
	}
	
	function create_numberPanel()
	{
		result = "";
		for (j = pages.length+1; j > 0; j--) {
			nextnumber = ((j/1000 < 1 && pages.length >= 1000)?'0':'') + ((j/100 < 1 && pages.length >= 100)?'0':'') + ((j/10 < 1 && pages.length >= 10)?'0':'') + j;
			result += "<div class='number number_"+ j +" dnone'><a href='" + baseurl + "page/" + j + "' onClick='changePage("+(j-1)+"); return false;'>"+nextnumber+"</a></div>"; 
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
		else jQuery('#page').scrollTo("-=60",100,{axis:"x",easing:'linear'});
	}
	
	function rightMove(e) {
		jQuery('#page').scrollTo("+=60",100,{axis:"x",easing:'linear'});
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
				
				if(e.which==115) jQuery.scrollTo("+=60",100,{axis:"y",easing:'linear'});
				if(e.which==119) jQuery.scrollTo("-=60",100,{axis:"y",easing:'linear'});
				if(e.keyCode==40) {
					e.preventDefault()
					jQuery.scrollTo("+=60",100,{easing:'linear'});
				}
				if(e.keyCode==38) {
					e.preventDefault();
					jQuery.scrollTo("-=60",100,{easing:'linear'});
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
				if(e.which==83) jQuery.scrollTo("+=60",100,{axis:"y",easing:'linear'});
				if(e.which==87) jQuery.scrollTo("-=60",100,{axis:"y",easing:'linear'});
				if(e.which==40) {
					e.preventDefault()
					jQuery.scrollTo("+=60",100,{easing:'linear'});
				}
				if(e.which==38) {
					e.preventDefault();
					jQuery.scrollTo("-=60",100,{easing:'linear'});
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
		
		jQuery(window).bind('statechange',function(){
			var State = History.getState();
			url = State.url.substr(State.url.lastIndexOf('/')+1);
			changePage(url-1, false, true);
			document.title = gt_page+' ' + (current_page+1) + ' :: ' + title;
		});
		
		
		
		State = History.getState();
		url = State.url.substr(State.url.lastIndexOf('/')+1);
		if(url < 1)
			url = 1;
		current_page = url-1;
		History.pushState(null, null, baseurl+'page/' + (current_page+1));
		create_numberPanel();
		changePage(current_page, false, true);
		document.title = gt_page+' ' + (current_page+1) + ' :: ' + title;	
		
		jQuery(window).resize(function() {
			resizePage(current_page);
		});
	});
</script>