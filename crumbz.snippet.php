<?php
/**
 *  File        crumbz.snippet.php (requires MODx Revolution 2.x)
 * Created on    Jul 02, 2010
 * Project        shawn_wilkerson
 * @package     crumbz
 * @version    1.0
 * @category    navigation
 * @author        W. Shawn Wilkerson
 * @link        http://www.shawnWilkerson.com
 * @copyright  Copyright (c) 2010, W. Shawn Wilkerson.  All rights reserved.
 * @license      GPL
 *
 *
 * This snippet and associated class perform only the duties of a breadcrumb snippet.
 *
 * It ignores, deleted documents, hidden documents, and unpublished documents, as breadcrumbs should typically
 * be only those files actually visitable by a web browser. This class also does not concern itself with ACLs.
 */
 
/* Parameters:
      id                 int Resource ID to build Breadcrumbs from (upwards)                    'id' => $this->modx->resource->get('id')
    
    depth            int Number of resources to return -- Home will always be              'depth' => 10
                    one additional
    
    format            string    [list, vertical, bar]  Returns xhtml formated text            'format' => 'bar'
                    needing wrapped inside of ul, table, div.  This allows simple
                    CSS application via the parent elements
    
    seperator        string which divides the bar elements                                'seperator' => ' &raquo; '
    
    textonly        bool [1/0]    returns only the text from links, not actual links        'textOnly' => false
    
    removeSiteHome    bool [1/0]     removes the 'site_start' / Home document                'removeSiteHome' => false
    
    removeLastID    bool [1/0]     removes the current document or specified document        'removeLastID' => false
                    via &id=`#` above from the result
    
    lastChildAsLink bool [1/0]     optionally returns the last resource (possibly the         'lastChildAsLink' => false
                    current) document as a link
                    
    useLongTitle    bool [0/1]     optionally uses the resource long title as the link     'useLongTitle' => false
                    text with the resource description returning as the title=""
                    attribute in the href tag. Developer is responsible to ensure
                    those fields are completed in the manager
            
*/
$crumbz_base= $modx->getOption('core_path').'components/crumbz/';
include_once $crumbz_base.'crumbz.class.php';
if (!$modx->loadClass('Crumbz', $crumbz_base, true, true))
{
    return 'error: Crumbz class not found';
}
$crumbz= new Crumbz($modx, $scriptProperties);
return $crumbz->run();