<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

/** Čistí HTML z editoru: ponechá formátování z TinyMCE, odstraní skripty a nebezpečné atributy. */
class HtmlSanitizer
{
    private ?HTMLPurifier $purifier = null;

    public function clean(string $html): string
    {
        return $this->purifier()->purify($html);
    }

    private function purifier(): HTMLPurifier
    {
        if ($this->purifier) {
            return $this->purifier;
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('Attr.EnableID', true);
        $config->set('HTML.TargetNoopener', true);
        // Vložená videa z TinyMCE (pluginy media): jen YouTube a Vimeo.
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
        // data: kvůli obrázkům vloženým do editoru přes schránku (HTMLPurifier povolí jen obrázkové typy).
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true, 'data' => true]);

        $definition = $config->getHTMLDefinition(true);
        $definition->addAttribute('iframe', 'allowfullscreen', 'Bool');

        return $this->purifier = new HTMLPurifier($config);
    }
}
