<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Returns the sidebar in the theme
 * 
 * @param string team name
 * @author Woxxy
 * @return string facebook widget for the team
 */
if(!function_exists('get_sidebar'))
{
	function get_sidebar()
	{
		$echo = '';
		$echo .= '<ul class="sidebar">';
		if(get_setting_irc())$echo .= '<li>'. get_irc_widget() .'</li>';
		if(get_setting_facebook())$echo .= '<li>'. get_facebook_widget() .'</li>';
		$echo .= '</ul>';
		return $echo;
	}
}

/**
 * Returns IRC for the team
 * If $team is not set, it returns the home team's irc
 * 
 * @param string team name
 * @author Woxxy
 * @return string facebook for the team
 */
if(!function_exists('get_setting_irc'))
{
	function get_setting_irc($team = NULL)
	{
		if(is_null($team)) return get_home_team()->irc;
		$team = new Team();
		$team->where('name', $team)->limit(1)->get();
		return $team->irc;
	}
}

/**
 * Returns IRC widget for the team
 * If $team is not set, it returns the home team's irc widget
 * 
 * @param string team name
 * @author Woxxy
 * @return string facebook for the team
 */
if(!function_exists('get_irc_widget'))
{
	function get_irc_widget($team = NULL)
	{
		$irc = get_setting_irc($team);
		
		$echo = _('Come chatting with us on') . ' <a href="'.parse_irc($irc).'">' . $irc . '</a>';
		return $echo;
	}
}

/**
 * Returns facebook url for the team
 * If $team is not set, it returns the home team's facebook
 * 
 * @param string team name
 * @author Woxxy
 * @return string facebook for the team
 */
if(!function_exists('get_setting_facebook'))
{
	function get_setting_facebook($team = NULL)
	{
		$hometeam = get_setting('fs_gen_default_team');
		$team = new Team();
		$team->where('name', $hometeam)->limit(1)->get();
		return $team->facebook;
	}
}

/**
 * Returns facebook widget for the team
 * If $team is not set, it returns the home team's facebook widget
 * 
 * @param string team name
 * @author Woxxy
 * @return string facebook widget for the team
 */
if(!function_exists('get_facebook_widget'))
{
	function get_facebook_widget($team = NULL)
	{
		$facebook = get_setting_facebook($team);
		
		$echo = "<iframe src='http://www.facebook.com/plugins/likebox.php?href=".urlencode($facebook)."&amp;width=240&amp;colorscheme=light&amp;show_faces=false&amp;stream=true&amp;header=true&amp;height=427' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:240px; height:427px; background:#fff; background:rgba(255,255,255,0.7)' allowTransparency='true'></iframe>";
		return $echo;
	}
}