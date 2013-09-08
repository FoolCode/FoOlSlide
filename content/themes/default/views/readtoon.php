<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>

<div class="panel">
	<div class="topbar">
		<div>
			<div class="topbar_left">
				<h1 class="tbtitle dnone"><?php echo $comic->url() ?> :: <?php echo $chapter->url() ?></h1>
				<div class="tbtitle dropdown_parent"><div class="text"><?php echo $comic->url() ?> ⤵</div>
					<?php
					echo '<ul class="dropdown">';
					foreach ($comics->all as $co)
					{
						echo '<li>' . $co->url() . '</li>';
					}
					echo '</ul>'
					?>
				</div>
				<div class="tbtitle dropdown_parent"><div class="text"><?php echo '<a href="' . $chapter->href() . '">' . ((strlen($chapter->title()) > 58) ? (substr($chapter->title(), 0, 50) . '...') : $chapter->title()) . '</a>' ?> ⤵</div>
					<?php
					echo '<ul class="dropdown">';
					foreach ($chapters->all as $ch)
					{
						echo '<li>' . $ch->url() . '</li>';
					}
					echo '</ul>'
					?>
				</div>
				<div class="tbtitle icon_wrapper dnone" ><img class="icon off" src="<?php echo glyphish(181); ?>" /><img class="icon on" src="<?php echo glyphish(181, TRUE); ?>" /></div>
				<?php echo $chapter->download_url(NULL, "fleft"); ?>
			</div>
		</div>
		<div class="clearer"></div>
	</div>
</div>


<div id="page">
	<div class="inner">
        <?php foreach ($pages as $n => $page) : ?>
			<a href="<?php echo $chapter->href() . 'page/' . ($n + 2) ?>" onClick="return changePage(<?php echo $n + 1 ?>);">
                <img class="page-<?php echo $n ?>" src="<?php $page['url'] ?>">
		    </a>
        <?php endforeach; ?>
	</div>
</div>

<div class="clearer"></div>

<div id="bottombar">
    <div class="socialbuttons">
        <div class="tweet">
            <a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $chapter->href() ?>" data-count="horizontal" data-via="<?php echo get_setting_twitter(); ?>" data-related="<?php echo get_setting_twitter(); ?>">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
        </div>
		<div class="facebook">
			<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode($chapter->href()) ?>&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:95px; height:21px;" allowTransparency="true"></iframe>
		</div>
		<div class="googleplus">
			<g:plusone size="medium" href="<?php echo $chapter->href() ?>"></g:plusone>
		</div>
    </div>
</div>

<script type="text/javascript">
	var title = document.title;
	var pages = <?php echo json_encode($pages); ?>;

	var next_chapter = "<?php echo $next_chapter; ?>";
    var preload_next = 5;
    var preload_prev = 2;
    var current_page = <?php echo $current_page - 1; ?>;

	var initialized = false;

	var base_url = '<?php echo $chapter->href() ?>';
	var site_url = '<?php echo site_url() ?>';

	var gt_page = '<?php echo addslashes(_("Page")) ?>';
	var gt_key_suggestion = '<?php echo addslashes(_("Use W-A-S-D or the arrow keys to navigate")) ?>';
	var gt_key_tap = '<?php echo addslashes(_("Double-tap to change page")) ?>';

	function changePage(id, noscroll, nohash)
	{
		id = parseInt(id);
		if (initialized && id == current_page)
			return false;

		if(!initialized) {
			create_message('key_suggestion', 4000, gt_key_suggestion);
		}

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

        preload();
		current_page = id;
		next = parseInt(id+1);
		jQuery("html, body").stop(true,true);

        jQuery.scrollTo('.page-' + current_page);

		if(!nohash) History.pushState(null, null, base_url+'page/' + (current_page + 1));
		document.title = gt_page+' ' + (current_page+1) + ' :: ' + title;
        jQuery("#page").css({'max-width' : (parseInt(pages[id].width) + 10) + 'px'});

		jQuery("#ads_top_banner.iframe iframe").attr("src", site_url + "content/ads/ads_top.html");
		jQuery("#ads_bottom_banner.iframe iframe").attr("src", site_url + "content/ads/ads_bottom.html");

		return false;
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

    function preload()
    {
        var array = [];
        var arrayData = [];

        for (var i = 0; i < pages.length; i++) {
            array.push(pages[i].url);
            arrayData.push(i);
        }

        jQuery.preload(array, {
            threshold: 40,
            enforceCache: true,
            onComplete: function(data) {
                var idx = data.index;

                if (data.index == page) return false;

                var page = arrayData[idx];
                pages[page].loaded = true;

                jQuery('.page-' + page).animate({'opacity':'1.0'}, 800);
                jQuery('.page-' + page).attr('src', pages[page].url);
            }
        });
    }

	function chapters_dropdown()
	{
		location.href = jQuery('#chapters_dropdown').val();
	}

	var isSpread = false;
	var button_down = false;
	var button_down_code;

	jQuery(document).ready(function() {
		jQuery(document).keydown(function(e){

			if(!button_down && !jQuery("input").is(":focus"))
			{
				button_down = true;
				code = e.keyCode || e.which;

				if(e.keyCode==37 || e.keyCode==65)
				{
					if(!isSpread) prevPage();
					else if(e.timeStamp - timeStamp37 < 400 && e.timeStamp - timeStamp37 > 150) prevPage();
					timeStamp37 = e.timeStamp;

					button_down = true;
					e.preventDefault();
					button_down_code = setInterval(function() {
						if (button_down) {
							jQuery('#page').scrollTo("-=13",{axis:"x"});
						}
					}, 20);
				}
				if(e.keyCode==39 || e.keyCode==68)
				{
					if(!isSpread) nextPage();
					else if(e.timeStamp - timeStamp39 < 400 && e.timeStamp - timeStamp39 > 150) nextPage();
					timeStamp39 = e.timeStamp;

					button_down = true;
					e.preventDefault();
					button_down_code = setInterval(function() {
						if (button_down) {
							jQuery('#page').scrollTo("+=13",{axis:"x"});
						}
					}, 20);
				}


				if(code == 40 || code == 83)
				{
					e.preventDefault();
					button_down_code = setInterval(function() {
						if (button_down) {
							jQuery.scrollTo("+=13");
						}
					}, 20);
				}

				if(code == 38 || code == 87)
				{
					e.preventDefault();
					button_down_code = setInterval(function() {
						if (button_down) {
							jQuery.scrollTo("-=13");
						}
					}, 20);

				}
			}

		});

		jQuery(document).keyup(function(e){
			button_down_code = window.clearInterval(button_down_code);
			button_down = false;
		});

		timeStamp37 = 0;
		timeStamp39 = 0;

		jQuery(window).bind('statechange',function(){
			var State = History.getState();
			url = parseInt(State.url.substr(State.url.lastIndexOf('/')+1));
			changePage(url-1, false, true);
			document.title = gt_page+' ' + (current_page+1) + ' :: ' + title;
		});

		State = History.getState();
		url = State.url.substr(State.url.lastIndexOf('/')+1);
		if(url < 1)
			url = 1;
		current_page = url-1;
		History.pushState(null, null, base_url+'page/' + (current_page+1));
		changePage(current_page, false, true);
		document.title = gt_page+' ' + (current_page+1) + ' :: ' + title;
	});
</script>

<script type="text/javascript">
	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
</script>
