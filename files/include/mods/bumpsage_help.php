<?php

/**
 *    Mod title: Bumpsage
 *         File: /include/mods/bumpsage_help.php
 *  Description: Included from /help.php
 */

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Load the mod_bump_sage.php language file
if (file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/mod_bumpsage.php'))
    require PUN_ROOT.'lang/'.$pun_user['language'].'/mod_bumpsage.php';
elseif (file_exists(PUN_ROOT.'lang/English/mod_bumpsage.php'))
    require PUN_ROOT.'lang/English/mod_bumpsage.php';

if(isset($lang_bumpsage))
{

?>
<h2><span><?php echo $lang_bumpsage['Bump Sage'] ?></span></h2>
<div class="box">
	<div class="inbox">
		<p><a name="bump_sage"></a><?php echo $lang_bumpsage['Bump Sage info'] ?></p>
        <?php if(!$pun_user['g_bump_topics']) { ?><p><?php echo sprintf($lang_bumpsage['not allowed'], pun_htmlspecialchars($lang_bumpsage['bump'])) ?></p><?php } ?>
        <?php if(!$pun_user['g_sage_replies']) { ?><p><?php echo sprintf($lang_bumpsage['not allowed'], pun_htmlspecialchars($lang_bumpsage['sage'])) ?></p><?php } ?>
        <p><code><?php echo pun_htmlspecialchars($lang_bumpsage['bump']) ?></code> <?php echo $lang_bumpsage['at beginning of reply'].' '.$lang_help['produces'] ?> <samp><?php echo $lang_bumpsage['bump help'] ?></a></samp></p>
        <p><code><?php echo pun_htmlspecialchars($lang_bumpsage['sage']) ?></code> <?php echo $lang_bumpsage['at beginning of reply'].' '.$lang_help['produces'] ?> <samp><?php echo $lang_bumpsage['sage help'] ?></a></samp></p>
	</div>
</div>

<?php

}
