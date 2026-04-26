<?php
/**
 * System Plugin for Joomla! - RinenHead
 *
 * @author  Ioannis Fytros <info@rinenweb.eu>
 * @license GNU GPL v3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

class PlgSystemRinenhead extends CMSPlugin
{

    protected $autoloadLanguage = true;

    private function getApp()
    {
        if (method_exists($this, 'getApplication')) {
            $app = $this->getApplication();

            if ($app) {
                return $app;
            }
        }

        return Factory::getApplication();
    }

    /**
     * Add custom markup to the document head.
     *
     * @return void
     */
    public function onBeforeCompileHead()
    {
        $app = $this->getApp();

        if (method_exists($app, 'isClient') && !$app->isClient('site')) {
            return;
        }

        $customHtml = trim((string) $this->params->get('customhtml', ''));

        if ($customHtml === '') {
            return;
        }

        $document = method_exists($app, 'getDocument') ? $app->getDocument() : Factory::getDocument();

        if (method_exists($document, 'getType') && $document->getType() !== 'html') {
            return;
        }

        $document->addCustomTag($customHtml);
    }

    /**
     * Add custom markup before the closing body tag.
     *
     * @return void
     */
    public function onAfterRender()
    {
        $app = $this->getApp();

        if (method_exists($app, 'isClient') && !$app->isClient('site')) {
            return;
        }

        $customJs = trim((string) $this->params->get('customjs', ''));

        if ($customJs === '') {
            return;
        }

        $body = $app->getBody();

        if (stripos($body, '</body>') !== false) {
            $body = preg_replace('/<\/body\s*>/i', "\n" . $customJs . "\n</body>", $body, 1);
        } else {
            $body .= "\n" . $customJs;
        }

        $app->setBody($body);
    }
}
