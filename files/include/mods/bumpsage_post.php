<?php

/**
 *    Mod title: Bumpsage
 *         File: /include/mods/bumpsage_post.php
 *  Description: Included from /post.php
 */

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Load the mod_bumpsage.php language file
@include (PUN_ROOT.'lang/'.$pun_user['language'].'/mod_bumpsage.php')
    or @include (PUN_ROOT.'lang/English/mod_bumpsage.php');

$sage= false;

// If in one of those case, bye bye
if(!isset($lang_bumpsage) || !empty($errors) || isset($_POST['preview']))
    return 1;

// If user can bump
if (stripos($message, $lang_bumpsage['bump'])===0)
{
    // If not allowed
    if(!$pun_user['g_bump_topics'])
    {
        $errors[]= sprintf($lang_bumpsage['not allowed'], pun_htmlspecialchars($lang_bumpsage['bump']));
    }
    else
    {
        // Get info on last answer in the topic
        $result = $db->query('SELECT last_poster, last_post_id FROM '.$db->prefix.'topics WHERE id='.$tid) or error('Unable to fetch data of last post for topic', __FILE__, __LINE__, $db->error());
        $last_post= $db->fetch_row($result);

        $poster= $last_post[0];
        $id= $last_post[1];

        // If last poster is current poster 
        if ($poster==$username)
        {
            $message= "\n\n".$lang_bumpsage['bump result'].substr($message, strlen($lang_bumpsage['bump']));
            $edited_sql = ', edited='.time().', edited_by=poster';
            $db->query('UPDATE '.$db->prefix.'posts SET message=CONCAT(message,\''.$db->escape($message).'\')'.$edited_sql.' WHERE id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());
            $db->query('UPDATE '.$db->prefix.'topics SET last_post='.$now.' WHERE id='.$tid) or error('Unable to update topics', __FILE__, __LINE__, $db->error());

            redirect('viewtopic.php?pid='.$id.'#p'.$id, $lang_post['Edit redirect']);
        }
        else
        {
            $errors[]= $lang_bumpsage['not last poster'];
        }
    }
}

// If user can sage
if (stripos($message, $lang_bumpsage['sage'])===0)
{
    if(!$pun_user['g_sage_replies'])
    {
        $errors[]= sprintf($lang_bumpsage['not allowed'], pun_htmlspecialchars($lang_bumpsage['sage']));
    }
    else
    {
        $message= $lang_bumpsage['sage result'].substr($message, strlen($lang_bumpsage['sage']));
        $sage= true;
    }
}
