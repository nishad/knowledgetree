<?php

/**
 * $Id:$
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

/**
 * PURPOSE:
 *
 * The purpose of this script is to list documents that are not currently indexed.
 *
 * Usage: reportUnindexedDocuments.php [reindex]
 *
 * If 'reindex' is specified, documents will be re-indexed.
 *
 */

session_start();
chdir(dirname(__FILE__));
require_once(realpath('../../../config/dmsDefaults.php'));

$sql = 'select id from documents';
$rows = DBUtil::getResultArray($sql);

$indexer = Indexer::get();
$diagnosis = $indexer->diagnose();

if (!empty($diagnosis))
{
    die($diagnosis);
}

require_once('indexing/indexerCore.inc.php');
$reindex=false;
if ($argc > 0)
{
    foreach($argv as $arg)
    {
        switch (strtolower($arg))
        {
            case 'reindex':
                $reindex=true;
                print "* " . _kt("Reindexing documents when they are encountered.") . "\n";
                break;
            case 'help':
                print "Usage: registerTypes.php [clear]\n";
                exit;
        }
    }
}

print "Querying document index...\n\n";
print "Note that this is quite an expensive task....\n\n";

$notIndexed = array();
$i = 0;
foreach($rows as $row)
{
    $docId = $row['id'];
    if (!$indexer->isDocumentIndexed($docId))
    {
        $notIndexed[] = $docId;
    }
    if ($i % 100 == 0) print '.';
    if ($i++ % 4000 == 0) print "\n";
}

print "\nReporting...\n";

if (empty($notIndexed))
{
    print "All documents are indexed\n";
}
else
{
    print "\n-----START-----\n\"Document Id\",\"Title\",\"Full Path\"\n";

    $notIndexed = implode(',', $notIndexed);
    $sql = "select d.id, dm.name as title, d.full_path  from documents d inner join document_metadata_version dm on d.metadata_version_id = dm.id where d.id in ($notIndexed) ";
    $rows = DBUtil::getResultArray($sql);

    foreach($rows as $row)
    {
        print '"'  .$row['id'] . '","'  .$row['title'] . '","'  .$row['full_path'] . '"' . "\n";
        if ($reindex)
        {
            Indexer::index($docId);
            $GLOBALS["_OBJECTCACHE"] = array();
        }
    }

    print  "-----END-----\n\nDone\n";
}

exit;
?>