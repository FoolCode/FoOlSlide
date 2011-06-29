<script type="text/javascript">

	archives = <?php echo json_encode($archives); ?>;
	default_team = '<?php echo addslashes(get_setting('fs_gen_default_team')) ?>';

	function chapter_options(index, chapter) {
		result = "<div class='chapter chapter_" + index + "'><table class='form'>";
		
		result += "<?php echo _('Chapter filename') ?> : <b>" + chapter.filename + "</b>";

		result += "<input class='input_hidden' type='hidden' value='" + index + "' /></td></tr>";

		result += "<tr><td>Title:</td><td><input class='input_name' type='text' value='' /></td></tr>";

		if(chapter.numbers.length > 1)
			result += "<tr><td>Volume number:</td><td><input class='input_volume' type='text' value='" + chapter.numbers[chapter.numbers.length-2] + "' /></td></tr>";
		else
			result += "<tr><td>Volume number:</td><td><input class='input_volume' type='text' value='0' /></td></tr>";
		

		if(chapter.numbers.length > 0)
			result += "<tr><td>Chapter number:</td><td><input class='input_chapter' type='text' value='" + chapter.numbers[chapter.numbers.length-1] + "' /></td></tr>";
		else
			result += "<tr><td>Chapter number:</td><td><input class='input_chapter' type='text' value='0' /></td></tr>";
		
		result += "<tr><td>Subchapter number:</td><td><input class='input_subchapter' type='text' value='0' /></td></tr>";		
		
		result += '<tr><td>Chapter language:</td><td><?php echo str_replace("\n", "", form_language(array('name' => 'input_language', 'class' => 'input_language'))); ?></td></tr>';

		result += "<tr><td>Teams:</td><td class='insert_teams'>";
			
		team_found = false;
		jQuery.each(chapter.teams, function(index, team){
			team_found = true;
			result += "<input type='text' class='set_teams' value='" + team.substring(1, team.length-1) + "' />";
		});
		if(!team_found) {
			result += "<input type='text' class='set_teams' value='" + default_team + "' />";
		}
		
		result += "<input type='text' class='set_teams' value='' onKeyUp='addField(this);' /></td></tr>";
		
		result += "</table>";
			
		result += "<div class'gbuttons'><a class='gbutton' href='#' onClick='submit_chapter(this); return false;'>Submit</a><div class='clearer'></div><div>";
		
		result += "</div>"
		return result;
	}
	
	function chapters_options(chapters) {
		jQuery.each(chapters, function(index, data){
			jQuery('#chapters').append(chapter_options(index, data));
		});
	}
	
	function set_volume() {
		value = jQuery('#set_volume').val();
		jQuery('.input_volume').each(function(index){
			jQuery(this).val(value);
		});
	}
	
	function set_init_chapter() {
		value = jQuery('#set_init_chapter').val();
		count = 0;
		jQuery('.input_chapter').each(function(index){
			jQuery(this).val(parseInt(value) + parseInt(count));
			count++;
		});
	}
	
	function set_teams() {
		result = "";
		jQuery('.set_teams', '.teams_setter').each(function(index){
			if (jQuery(this).val() != "")
				result += "<input type='text' class='set_teams' value='" + jQuery(this).val() + "' />";
		});
		
		result += "<input type='text' class='set_teams' value='' onKeyUp='addField(this);' />";
		jQuery(".insert_teams").each(function(index){
			jQuery(this).html(result);
		});
	}
	
	function try_filename()
	{
		result = {};
		
		value = jQuery('#manual_filename').val();
		value = value.replace("{v","v");
		value = value.replace("v}","v");
		value = value.replace("{s","s");
		value = value.replace("s}","s");
		firstC = value.indexOf("{c");
		if(firstC)
		{
			lastC = value.indexOf("c}");
		}
		
		
		value = jQuery('#manual_filename').val();
		value = value.replace("{c", "c");
		value = value.replace("c}","c");
		value = value.replace("{v","v");
		value = value.replace("v}","v");
		firstS = value.indexOf("{s");
		if(firstS)
		{
			lastS = value.indexOf("s}");
		}
		
		value = jQuery('#manual_filename').val();
		value = value.replace("{c", "c");
		value = value.replace("c}","c");
		value = value.replace("{s","s");
		value = value.replace("s}","s");
		firstV = value.indexOf("{v");
		if(firstV)
		{
			lastV = value.indexOf("v}");
		}
	
		jQuery.each(archives, function(index, data){
			if(firstC)jQuery('.chapter_' + index + " .input_chapter").val(data.filename.substring(firstC, lastC));
			if(firstS)jQuery('.chapter_' + index + " .input_subchapter").val(data.filename.substring(firstS, lastS));
			if(firstV)jQuery('.chapter_' + index + " .input_volume").val(data.filename.substring(firstV, lastV));
		});
	}
	
	function submit_chapter(obj, all)
	{
		box = jQuery(obj).parent().parent();
		jQuery(box).css({'background': '#FCDEDE', 'opacity' : '0.6'});
		teams = [];
		jQuery('.set_teams',box).each(function(index){
			teams.push(jQuery(this).val());
		})
		
		index = jQuery('.input_hidden', box).val();

		jQuery.post('<?php echo site_url('/admin/comics/import/' . $comic->stub) ?>', {
			action: 'execute',
			type: 'single_compressed',
			name: '',
			server_path: archives[index].server_path,
			comic_id: archives[0].comic_id,
			name: jQuery('.input_name', box).val(),
			chapter: jQuery('.input_chapter', box).val(),
			subchapter: jQuery('.input_subchapter', box).val(),
			volume: jQuery('.input_volume', box).val(),
			language: jQuery('[name="input_language"]', box).val(),
			team: teams
		}, function(result){
			if(result.error === undefined) jQuery(box).css({'background': '#DDFCE7', 'opacity' : '0.4'});
			else jQuery(box).css({'opacity' : '0.9'});
		},'json').complete(function(){
			if(all !== undefined) {
				submit_all(all+1);
			}});
	}
	
	function submit_all(index){
		submit_chapter(jQuery('.chapter:eq('+index+') .gbutton'), index);
	}
	
	jQuery(document).ready(function(){
		chapters_options(archives);
		jQuery('#manual_filename').val(archives[0].filename);
	});
	
</script>
<style>
	.chapter {
		padding:8px; 
		border:1px solid #aaa;
		margin:8px;
		border-radius:5px;
		-webkit-border-radius:5px;
		-moz-border-radius:5px;
	}
	.chapter div {
		margin:5px;
		width:700px;
	}
</style>

<div id="tools">
	<?php echo _('Manual setup: write {cc} for where the chapter is positioned. {ss} for subchapter. {vv} for volume. The amount of letters sets the amount of numbers. "Chapter_123" is caught by writing "Chapter_{ccc}".') ?>
	<br/>
	<input type="text" id="manual_filename" value="" /> <a href="#" class="" onClick="try_filename(); return false;">Try</a>
	<br/><br/>
	<?php echo _("Mass set the volume") ?>:<br/>
	<input type="text" id="set_volume" value="" /> <a href="#" class="" onClick="set_volume(); return false;">Set</a>
	<br/><br/>
	<?php echo _("Set first chapter number and +1 each next chapter") ?>:<br/>
	<input type="text" id="set_init_chapter" value="" /> <a href="#" class="" onClick="set_init_chapter(); return false;">Set</a>
	<br/><br/>
	<?php echo _("Mass set the teams") ?>:<br/>
	<div class="teams_setter" style="width:500px;">
		<input type="text" class="set_teams" value="" /> <a href="#" class="" onClick="set_teams(); return false;">Set</a>
		<br/><input type="text" class="set_teams" value="" onKeyUp="addField(this);" />
	</div>
	<br/><br/>
	<?php echo _("If you checked all chapter numbers") ?>:
	<a href="#" onClick="submit_all(0)" class="">Submit all</a>
</div>

<br/>
</div>
<br/><?php echo _("Currently adding chapters to") ?>: <b><?php echo $comic->name ?></b><br/><br/>

<div id="chapters"></div>