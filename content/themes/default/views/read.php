<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>



<div id="page">
	<div class="inner">
		<a href="<?php echo $chapter->next_page($current_page); ?>" onclick="changePage('<?php echo $current_page; ?>'); return false;">
			<img src="<?php echo $pages[$current_page-1]['url'] ?>"  />
		</a>
		<div class="number">
			<div class="initnumber">1</div>
			<div class="on">on</div>
			<div class="finalnumber"><?php echo count($pages); ?></div>



		</div>
	</div>
</div>



<div class="clearer"></div>
<script src="<?php echo site_url(); ?>assets/js/jquery.plugins.js"></script>
<script type="text/javascript">


	var pages = <?php echo json_encode($pages); ?>;

	var next_chapter = "<?php echo $next_chapter; ?>";
	
	var preload_next = 4;

	var preload_back = 2;

	var current_page = <?php echo $current_page; ?> - 1;
	
	function changePage(id)
	{
		id = parseInt(id);
		if(id == pages.length) 
		{
			location.href = next_chapter;
		}
		preload(id);
		jQuery('#page img').attr('src', pages[id].url);
		jQuery('#page a').attr('onclick', 'changePage(' + parseInt(id+1) + '); return false;');
		jQuery('.initnumber').text(id+1);
		jQuery("html, body").stop(true,true);
		jQuery.scrollTo('#page', 400);
		current_page = id;
		
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
			// placeholder: 'http://foolrulez.org/manga/themes/default/images/ajax-loader.gif',
			onComplete:function(data)
			{
				pages[arraydata[data.index]].loaded = true;
			}
	
		});
	}
	
	function countNextLoaded()
	{
		for(i = current_page; i < )
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
		
		jQuery('#page').bind("contextmenu", function(e) {
			e.preventDefault();
		});

	});
</script>