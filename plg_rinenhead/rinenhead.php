<?php

/**
 * System Plugin for Joomla! - RinenHead
 * @author     Ioannis Fytros <info@rinenweb.eu>
 * @license    GNU GPL v3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

class PlgSystemRinenhead extends CMSPlugin
{
    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();
        
        // Only run this in the front end
        if ($app->isClient('site')) {
            $doc = JFactory::getDocument();
            $customHtml = $this->params->get('customhtml', '');
            
            if (!empty($customHtml)) {
                $doc->addCustomTag($customHtml);
            }
        }
    }
}
