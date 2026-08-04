<?php
/**
 * System Plugin for Joomla! - RinenHead
 * Injects custom markup into the document <head> and before the closing
 * </body> tag on frontend HTML pages.
 * @author  Ioannis Fytros <info@rinenweb.eu>
 * @license GNU GPL v3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class PlgSystemRinenhead extends CMSPlugin implements SubscriberInterface
{

    protected $autoloadLanguage = true;

    /**
     * Declare the events this plugin listens to.
     *
     * This replaces Joomla's deprecated auto-discovery of public onXxx()
     * methods (which relies on reflection) while keeping the classic
     * single-file plugin layout. Supported since Joomla 4.0.
     * @return  array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onBeforeCompileHead' => 'onBeforeCompileHead',
            'onAfterRender'       => 'onAfterRender',
        ];
    }

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
     * @param   Event  $event  The dispatched event.
     * @return  void
     */
    public function onBeforeCompileHead(Event $event): void
    {
        $app = $this->getApp();

        if (!$app->isClient('site')) {
            return;
        }

        $customHtml = trim((string) $this->params->get('customhtml', ''));

        if ($customHtml === '') {
            return;
        }

        $document = $app->getDocument();

        if ($document->getType() !== 'html') {
            return;
        }

        $document->addCustomTag($customHtml);
    }

    /**
     * Add custom markup before the closing body tag.
     * @param   Event  $event  The dispatched event.
     * @return  void
     */
    public function onAfterRender(Event $event): void
        {
            $app = $this->getApp();
        
            if (!$app->isClient('site')) {
                return;
            }
        
            $document = $app->getDocument();
        
            if ($document->getType() !== 'html') {
                return;
            }
        
            $customJs = trim((string) $this->params->get('customjs', ''));
        
            if ($customJs === '') {
                return;
            }
        
            // Wrap plain JavaScript while leaving existing HTML markup unchanged.
            if (preg_match('/^\s*</', $customJs) !== 1) {
                $customJs = "<script>\n" . $customJs . "\n</script>";
            }
        
            $body = $app->getBody();
        
            if (stripos($body, '</body>') === false) {
                return;
            }
        
            $replaced = preg_replace_callback(
                '/<\/body\s*>/i',
                static function (array $matches) use ($customJs): string {
                    return "\n" . $customJs . "\n" . $matches[0];
                },
                $body,
                1
            );
        
            if ($replaced !== null) {
                $app->setBody($replaced);
            }
        }
    }
