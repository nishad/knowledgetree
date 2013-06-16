<?php
/**
 * $Id$
 *
 * KnowledgeTree Community Edition
 * Document Management Made Simple
 * Copyright (C) 2008, 2009 KnowledgeTree Inc.
 * 
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * You can contact KnowledgeTree Inc., PO Box 7775 #87847, San Francisco, 
 * California 94120-7775, or email info@knowledgetree.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * KnowledgeTree" logo and retain the original copyright notice. If the display of the 
 * logo is not reasonably feasible for technical reasons, the Appropriate Legal Notices
 * must display the words "Powered by KnowledgeTree" and retain the original 
 * copyright notice.
 * Contributor( s): ______________________________________
 *
 */

class KTi18nUtil {
    function setup() {
    }

    function getInstalledLocales() {
        global $default;
        // get a list of directories in ($default->fileSystemRoot . "/i18n")
        $aLocales = array();
        $aLocales["en"] = "en";
        $aLocales["en-GB"] = "en";
        $aLocales["en-UK"] = "en";
        $aLocales["en-US"] = "en";
        $aLocales["en-ZA"] = "en";
        $dir = KT_DIR . '/i18n';
        if ($handle = opendir($dir)) {
           while (false !== ($file = readdir($handle))) {
               if (in_array($file, array('.', '..', 'CVS'))) {
                   continue;
               }
               if (is_dir($dir . '/' . $file)) {
                   $i = strpos($file, '_');
                   if ($i) {
                       $aLocales[substr($file, 0, $i)] = $file;
                   }
                   $aLocales[strtr($file, '_', '-')] = $file;
               }
           }
           closedir($handle);
        }
        return $aLocales;
    }
}

function _kt($sContents, $sDomain = 'knowledgeTree') {
    $oReg =& KTi18nRegistry::getSingleton();
    $oi18n =& $oReg->geti18n($sDomain);
    return $oi18n->gettext($sContents);
}
