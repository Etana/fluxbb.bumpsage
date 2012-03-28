##
##
##        Mod title:  Bumpsage
##
##      Mod version:  1.0
##  Works on FluxBB:  1.4.8
##     Release date:  2012-03-28
##      Review date:  YYYY-MM-DD (Leave unedited)
##           Author:  arw (arw.contact@gmail.com)
##
##      Description:  This mod allow to users to bump a topic or sage a reply
##
##   Repository URL:  http://fluxbb.org/resources/mods/xxx (Leave unedited)
##
##   Affected files:  post.php
##                    help.php
##
##       Affects DB:  Yes
##
##            Notes:  Permissions to bump or sage are given to groups by
##                    administrator in plugin menu Bumpsage.
## 
##                    If <bump> is at beginning of a reply and topic's last
##                    post is from the user. The reply will be appended to
##                    this post and the last post date on forums/index will be
##                    updated.
## 
##                    If <sage> is used at the beginning of a reply, index and
##                    forums last post date won't be updated.
## 
##                    <bump> and <sage> or their replacement can be changed in
##                    /lang/{language}/mod_bump_sage.php ( respectively with 
##                    keys 'bump' and 'sage', 'bump result', 'sage result' ).
##
##       DISCLAIMER:  Please note that "mods" are not officially supported by
##                    FluxBB. Installation of this modification is done at 
##                    your own risk. Backup your forum database and any and
##                    all applicable files before proceeding.
##
##


#
#---------[ 1. UPLOAD ]-------------------------------------------------------
#

install_mod.php to /

files/lang/English/mod_bumpsage.php to /lang/English/mod_bumpsage.php

files/lang/French/mod_bumpsage.php to /lang/French/mod_bumpsage.php

files/include/mods/bumpsage.php to /include/mods/bumpsage.php

files/include/mods/bumpsage.php to /include/mods/bumpsage_help.php

files/plugins/AP_Bumpsage.php to /plugins/AP_Bumpsage.php

#
#---------[ 2. RUN ]----------------------------------------------------------
#

install_mod.php


#
#---------[ 3. DELETE ]-------------------------------------------------------
#

install_mod.php


#
#---------[ 4. OPEN ]---------------------------------------------------------
#

post.php


#
#---------[ 5. FIND (line: 198) ]---------------------------------------------
#

			// Update topic
			$db->query('UPDATE '.$db->prefix.'topics SET num_replies='.$num_replies.', last_post='.$now.', last_post_id='.$new_pid.', last_poster=\''.$db->escape($username).'\' WHERE id='.$tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());


#
#---------[ 6. REPLACE WITH ]-------------------------------------------------
#

			// Update topic
			$db->query('UPDATE '.$db->prefix.'topics SET num_replies='.$num_replies.($sage?'':', last_post='.$now).', last_post_id='.$new_pid.', last_poster=\''.$db->escape($username).'\' WHERE id='.$tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());


#
#---------[ 7. FIND (line: 159) ]---------------------------------------------
#

    $now = time();


#
#---------[ 8. AFTER, ADD ]---------------------------------------------------
#


    if (file_exists(PUN_ROOT.'include/mods/bumpsage.php'))
        include PUN_ROOT.'include/mods/bumpsage.php';


#
#---------[ 9. OPEN ]--------------------------------------------
#

help.php


#
#---------[ 10. FIND (line: 159) ]---------------------------------------------
#

require PUN_ROOT.'footer.php';


#
#---------[ 11. BEFORE, ADD ]---------------------------------------------------
#


if (file_exists(PUN_ROOT.'include/mods/bumpsage_help.php'))
        include PUN_ROOT.'include/mods/bumpsage_help.php';



#
#---------[ 12. SAVE/UPLOAD ]-------------------------------------------------
#
