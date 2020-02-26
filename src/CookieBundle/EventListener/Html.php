<?php

namespace CookieBundle\EventListener;

use CookieBundle\CookieBundle;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PreviewRequestTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\ResponseInjectionTrait;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Tool;


class Html
{
    use PimcoreContextAwareTrait;
    use PreviewRequestTrait;
    use ResponseInjectionTrait;


    /**
     * @param $event
     */
    public function onKernelResponse($event)
    {
        $request = $event->getRequest();
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        if (!Tool::useFrontendOutputFilters()) {
            return;
        }

        if ($this->isPreviewRequest($request)) {
            return;
        }

        $response = $event->getResponse();
        if (!$this->isHtmlResponse($response)) {
            return;
        }

        $cookiesArr = $request->cookies;
        $cookie = $cookiesArr->get('cbcookie');

        include_once(PIMCORE_PATH . '/lib/simple_html_dom.php');
        $html = str_get_html($response->getContent());

        if (!$html->find('body')[0]->cbcookie)
            return;

        foreach($html->find('iframe') as $iframe) {
            $iframe = \CookieBundle\Tool\Html\Iframe::getHtmlElement($iframe, $cookie);
        }

        foreach($html->find('input.ca_google_autocomplete_input') as $input) {
            $input = \CookieBundle\Tool\Html\Google::autocompleteInput($input);
        }

        foreach($html->find('div.cb_googleMaps_map_container') as $div) {
            $div = \CookieBundle\Tool\Html\Google::getMapsV3($div);
        }

        foreach($html->find('.g-recaptcha') as $div) {
            $div = \CookieBundle\Tool\Html\Google::recaptchaV2($div);
        }

        $html = \CookieBundle\Tool\Html\Tags::setBody($html, $request);

        $content = $html->save();
        $html->clear();
        $response->setContent($content);

    }

}
