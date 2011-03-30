<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $template['title']; ?></title>
		<?php echo $template['metadata']; ?>
                <?php echo link_tag('content/themes/default/style.css')?>

	</head>
	<body>
            <div id="content">
                <div id="navig">
                        <a href="<?php echo "";?>"><div id="title"><?php echo "TITLE"; ?></div></a> 
                                <?php echo'<div style="float:left; margin:2px 5px 0; padding-top:6px; font-size:11px;"><a href="">Go back to site &crarr;</a></div>'; ?>

                                        			
                        <div class="clearer"></div>	
            </div>
            
		<?php echo $template['body']; ?>
                
            <div id="theFooter">
                    <div style="text-align:right; font-size:11px; margin:6px 0 2px 0;"><a title="Download or update your FoOlReader, made by FoOlRulez team" href="http://foolrulez.org/blog/foolreader">Powered by FoOlReader</a> | <a title="FoOlRulez.org website" href="http://foolrulez.org">FoOlRulez.org</a></div>
            </div>


            </div>
	</body>
</html>