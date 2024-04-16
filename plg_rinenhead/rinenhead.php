<?php

/**
 * System Plugin for Joomla! - RinenHead
 * @author     Ioannis Fytros <info@rinenweb.eu>
 * @license    GNU GPL v3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;


class PlgSystemRinenhead extends CMSPlugin
  
{
    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();
        
        // Only run this in the front end
        if ($app->isClient('site')) {
            $doc = Factory::getDocument();
            $customHtml = $this->params->get('customhtml', '');
            
            if (!empty($customHtml)) {
                $doc->addCustomTag($customHtml);
            }
        }
    }
	public function onAfterRender()
    {
        $app = Factory::getApplication();

        // Only run this in the front end
        if ($app->isClient('site')) {
            $body = $app->getBody();
            $customJs = $this->params->get('customjs', '');

            if (!empty($customJs)) {
                // Append the custom JavaScript before the closing </body> tag
                $body = str_replace('</body>', "<script type='text/javascript'>\n$customJs\n</script>\n</body>", $body);
                $app->setBody($body);
            }
        }
    }
}