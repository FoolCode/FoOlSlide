<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
?>



<div id="page">
	<div class="inner">
		<a href="<?php echo $chapter->next_page($current_page); ?>" onClick="return changePage('<?php echo $current_page; ?>');" >
			<img src="<?php echo $pages[$current_page - 1]['url'] ?>"  />
		</a>
		<div class="number">
			<div class="initnumber"><?php echo $current_page ?></div>
			<div class="on">on</div>
			<div class="finalnumber"><?php echo count($pages); ?></div>
			<div id="myFire"></div>
			<div id="myFireHidden"></div>
			<div id="myLoading"></div>
			<div id="myLoadingHidden"></div>



		</div>
	</div>
</div>



<div class="clearer"></div>
<script src="<?php echo site_url(); ?>assets/js/jquery.plugins.js"></script>
<script type="text/javascript">


	var pages = <?php echo json_encode($pages); ?>;

	var next_chapter = "<?php echo $next_chapter; ?>";
	
	var preload_next = 7;

	var preload_back = 1;

	var current_page = <?php echo $current_page - 1; ?>;
	
	function changePage(id)
	{
		id = parseInt(id);
		if(id > pages.length-1) 
		{
			location.href = next_chapter;
			return false;
		}
		
		preload(id);
		next = parseInt(id+1);
		jQuery('#page img').attr('src', pages[id].url);
		jQuery('#page .inner a').attr('onClick', 'return changePage(\'' + next + '\')');
		jQuery('.initnumber').text(next);
		jQuery("html, body").stop(true,true);
		jQuery.scrollTo('#page', 500, { easing:'elasout' });
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
				lightMyFire();
			}
	
		});
	}
	
	//borrowed from jQuery easing plugin
	//http://gsgd.co.uk/sandbox/jquery.easing.php
	$.easing.elasout = function(x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	};
	
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