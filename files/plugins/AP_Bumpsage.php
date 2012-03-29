<?php

/**
 *    Mod title: Bumpsage
 *         File: /plugins/AP_Bumpsage.php
 *  Description: To choose group settings
 */

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);

// Display the admin navigation menu
generate_admin_menu($plugin);

// Load the mod_bumpsage.php language file
@include(PUN_ROOT.'lang/'.$pun_user['language'].'/mod_bumpsage.php')
    or @include(PUN_ROOT.'lang/English/mod_bumpsage.php');

// If no language file, bye bye
if (!isset($lang_bumpsage))
{
	message("No mod_bumpsage language file");
    return 1;
}

// Changing permissions
if (isset($_POST['save_bump_sage']))
{
    confirm_referrer('admin_loader.php');
    
    if (is_array($_POST['bump_in']))
    {
        $bump_in= '';
        foreach($_POST['bump_in'] as $key => $value)
            if(intval($key)>0) $bump_in.=($bump_in?',':'').$key;
    }
    if (is_array($_POST['sage_in']))
    {
        $sage_in= '';
        foreach($_POST['sage_in'] as $key => $value)
            if(intval($key)>0) $sage_in.=($sage_in?',':'').$key;
    }

    $db->query('UPDATE '.$db->prefix.'groups SET g_bump_topics=0, g_sage_replies=0') or error('Unable to update groups', __FILE__, __LINE__, $db->error());
    if($bump_in) $db->query('UPDATE '.$db->prefix.'groups SET g_bump_topics=1 WHERE g_id IN ('.$bump_in.')') or error('Unable to update groups', __FILE__, __LINE__, $db->error());
    if($sage_in) $db->query('UPDATE '.$db->prefix.'groups SET g_sage_replies=1 WHERE g_id IN ('.$sage_in.')') or error('Unable to update groups', __FILE__, __LINE__, $db->error());
}

// Rebuilding last_post of topics date
if (isset($_POST['rebuild_last_date'], $_POST['older_than']))
{
    confirm_referrer('admin_loader.php');
     
    $operators= array('','<','>','!=');

    $num_operator= 0;

    if (isset($_POST['bump']))
    {
        $num_operator+= 1;
    }
    if (isset($_POST['sage']))
    {
        $num_operator+= 2;
    }
    
    if ($num_operator)
    {
        $older_than= intval($_POST['older_than']);

        // Get topic id, last post date and forum id of topics removed from sage or bump
        $result= $db->query('SELECT T.id, P.posted, T.forum_id FROM '.$db->prefix.'topics T INNER JOIN '.$db->prefix.'posts P ON T.last_post_id=P.id AND P.posted '.$operators[$num_operator].' T.last_post'.($older_than?' WHERE P.posted < '.(time()-3600*$older_than):'')) or error('Unable to get info on last post', __FILE__, __LINE__, $db->error());

        if($db->num_rows($result))
        {
            $forum_ids= array();
            $insert_topics_values= '';

            // Get array of forums, and list of update to do on topics
            for($i= 0; $cur_info_last= $db->fetch_row($result); $i++)
            {
                $insert_topics_values.= ($insert_topics_values?',':'').'('.$cur_info_last[0].','.$cur_info_last[1].')';
                $forum_ids[]= $cur_info_last[2];
            }

            // Updating last_post of topics
            $result= $db->query('INSERT INTO '.$db->prefix.'topics (id,last_post) VALUES '.$insert_topics_values.' ON DUPLICATE KEY UPDATE last_post = VALUES(last_post)') or error('Unable to update topics', __FILE__, __LINE__, $db->error());

            // Remove duplicates
            $forum_ids= array_flip(array_flip($forum_ids));

            foreach($forum_ids as $forum_id)
            {
                // Get the id of the topic with the newest reply
                $result= $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$forum_id.' ORDER BY posted DESC, id DESC') or error('Unable to get id of last replied topic', __FILE__, __LINE__, $db->error());
                $topic_id= $db->result($result,0);

                // Update the forum
                $result= $db->query('INSERT INTO '.$db->prefix.'forums (id, last_post, last_post_id, last_poster) SELECT forum_id, last_post, last_post_id, last_poster FROM '.$db->prefix.'topics WHERE id='.$topic_id.' ON DUPLICATE KEY UPDATE last_post = VALUES(last_post), last_post_id= VALUES(last_post_id), last_poster= VALUES(last_poster)') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
            }

            
        }
    }
}

?>

    <div class="blockform">
		<h2 class="block2"><span><?php echo $lang_bumpsage['Bump Sage'] ?></span></h2>
		<div class="box">
        <form method="post">
            <div class="inform">
                <fieldset>
                    <legend><?php echo $lang_admin_common['User groups'] ?></legend>
                    <div class="infldset">
                        <p><?php echo $lang_bumpsage['Bump Sage info'] ?></p>
                        <table id="forumperms" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="atcl">&#160;</th>
                            <th><?php echo pun_htmlspecialchars($lang_bumpsage['bump']) ?></th>
                            <th><?php echo pun_htmlspecialchars($lang_bumpsage['sage']) ?></th>
                        </tr>
                        </thead>
                        <tbody>
<?php

$result = $db->query('SELECT g_id, g_title, g_bump_topics, g_sage_replies FROM '.$db->prefix.'groups ORDER BY g_id') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result)) { ?>
                        <tr>
                            <th class="actl"><?php echo pun_htmlspecialchars($cur_group['g_title']) ?></th>
                            <td>
                                <input type="checkbox" name="bump_in[<?php echo $cur_group['g_id'] ?>]"<?php if($cur_group['g_bump_topics']) echo ' checked="checked"' ?> />
                            </td>
                             <td>
                                <input type="checkbox" name="sage_in[<?php echo $cur_group['g_id'] ?>]"<?php if($cur_group['g_sage_replies']) echo ' checked="checked"' ?> />
                            </td>

                        </tr><?php } ?>
                        </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <p class="submitend"><input type="submit" name="save_bump_sage" value="<?php echo $lang_admin_common['Save changes'] ?>" /></p>
        </form>
        <form method="post">
            <div class="inform">
                <fieldset>
                    <legend><?php echo $lang_admin_common['Maintenance'] ?></legend>
                    <div class="infldset">
                        <p><?php echo $lang_bumpsage['rebuild last info'] ?></p>
                        <div class="fsetsubmit">
                            <input type="submit" name="rebuild_last_date" value="<?php echo $lang_bumpsage['rebuild last'] ?>" />
                            <?php echo $lang_bumpsage['older than'] ?>
                            <select name="older_than">
                                <option val="0"><?php echo $lang_bumpsage['now'] ?></option>
                                <option val="24"><?php echo $lang_bumpsage['a day'] ?></option>
                                <option val="168"><?php echo $lang_bumpsage['a week'] ?></option>
                                <option val="720"><?php echo $lang_bumpsage['a month'] ?></option>
                            </select>
                            <?php echo $lang_bumpsage['for'] ?>
                            <input type="checkbox" name="bump" checked="checked" /> 
                            <?php echo pun_htmlspecialchars($lang_bumpsage['bump']) ?>
                            <input type="checkbox" name="sage" /> 
                            <?php echo pun_htmlspecialchars($lang_bumpsage['sage']) ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>
        </div>

    </div>
    <div class="clearer"></div>
