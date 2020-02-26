<?php

namespace CookieBundle\Tool\Html;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;


class Tags
{

    /**
     * @param $html
     * @param Request $request
     * @return mixed
     */
    public static function setHeader($html,Request $request)
    {
        return $html;
        $config = \CookieBundle\Tool\Config::get(true);
        $services = $config['service'];

        $head = $html->find('head')[0];
        $head->innertext = $head->innertext . $script . "\n" ;

        return $html;
    }


    /**
     * @param $html
     * @param Request $request
     * @return mixed
     */
    public static function setBody($html,Request $request)
    {
        $locale = $request->getLocale();
        $isDdev = $_SERVER["IS_DDEV"];

        $db = \Pimcore\Db::getConnection();
        $q = " SELECT * FROM translations_website WHERE language = '{$locale}' ";
        $dbQuery = $db->query( $q );
        $transResults = $dbQuery->fetchAll();
        $db->close();

        $transList = [];
        foreach ($transResults as $trans) {
            $transList[$trans['key']] = $trans['text'];
        }

        $translations = 'cbCookie.lang = ' . json_encode($transList) . ';';

        $javascriptPath = PIMCORE_WEB_ROOT . '/bundles/cookie/js/cbcookie.min.js';
        $cssPath = PIMCORE_WEB_ROOT . '/bundles/cookie/css/cbbundle.min.css';
        if ($isDdev) {
            $javascriptPath = PIMCORE_PROJECT_ROOT . '/src/CookieBundle/src/CookieBundle/Resources/assets/js/cbcookie.js';
            $cssPath = PIMCORE_PROJECT_ROOT . '/src/CookieBundle/src/CookieBundle/Resources/assets/css/styles.css';
        }
        $javascriptContent = file_get_contents($javascriptPath);
        $cssContent = file_get_contents($cssPath);


        $files  = "\n" . '<script type="text/javascript">'. $javascriptContent . $translations .'</script>' . "\n";
        $files .= '<style>'. $cssContent .'</style>'. "\n";

        $script = "<script>\n";
        $script .= "cbCookie.job = []; \n";

        $config = \CookieBundle\Tool\Config::get(true);
        $services = $config['service'];

        if ($services['gtag']) {
            if ($services['gtagUa'])
                $script .= "cbCookie.user.gtagUa = '". $services['gtagUa'] ."'; \n";
            $script .= "cbCookie.job.push('gtag'); \n";
        }

        if ($services['recaptcha']) {
            $script .= "cbCookie.job.push('recaptcha'); \n";
        }

        if ($services['googleMaps']) {
            $script .= "cbCookie.user.googleMapsApiKey = '". $services['googleMapsApiKey'] ."'; \n";
            $script .= "cbCookie.job.push('googleMaps'); \n";
        }

        if ($services['youtube']) {
            $script .= "cbCookie.job.push('youtube'); \n";
        }
        if ($services['vimeo']) {
            $script .= "cbCookie.job.push('vimeo'); \n";
        }

        if ($services['webContent']) {
            $script .= "cbCookie.job.push('webContent'); \n";
        }

        //$script .= "cbCookie.init({lang: '". $request->getLocale() ."'}); \n";
        $script .= " window.onload = function () {
        cbCookie.init({lang: '". $locale ."'});
        } \n";

        $script .= "</script>";

        $body = $html->find('head')[0];
        $body->innertext = $files . $script . $body->innertext . "\n" ;

        $elements = '<div id="cb_settings_alert" title="Cookie-Einstellungen" onclick="cbCookie.popup.show()"></div>
                        <div id="cb_settings_alert_extended">
                            <div class="cbsae_container">
                                <div class="cbsae_title"><span cb-trans="cb_title"></span></div>
                                <div class="cbsae_text"><span cb-trans="cb_alertBigPrivacy"></span></div>
                                <div class="cbsae_btns">
                                    <div class="cbsae_btn_accept">
                                        <button onclick="cbCookie.service.acceptAll()">✓ <span cb-trans="cb_acceptAll"></span></button>
                                    </div>
                                    <div class="cbsae_btn_personal">
                                        <a href="" class="cb_privacyUrl_url"></a>
                                        <button onclick="cbCookie.popup.show()"><span cb-trans="cb_personalize"></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>';

        $elements .= '<div id="cb_settings_window" class="">
                        <div class="cb_settings_modal">
                            <div class="cbs_header">
                                <div class="cbs_top">
                                    <button class="cbs_close" onclick="cbCookie.popup.close()">✗ <span cb-trans="cb_close"></span></button>
                                </div>
                                <div class="cbs_title">
                                    <span cb-trans="cb_title"></span>
                                </div>
                                <div class="cbs_disclaimer">
                                    <span cb-trans="cb_disclaimer"></span>
                                </div>
                                <div class="cbs_privacyUrl_container">
                                <a href="" class="cb_privacyUrl_url"></a>
                                </div>
                            </div>
                            <div class="cbs_body">
                                <div class="cbs_button_bar">
                                    <button id="cbs_btn_popup_all_accept" class="cbs_allow" onclick="cbCookie.service.acceptAll()">✓ <span cb-trans="cb_acceptAll"></span></button>
                                    <button id="cbs_btn_popup_all_deny" class="cbs_disallow" onclick="cbCookie.service.denyAll()">✗ <span cb-trans="cb_denyAll"></span></button>
                                </div>
                                <div class="cbs_services">
                                <ul id="cbs_service_list_default" style="margin-bottom: 15px">
                                    <li>
                                    <div class="cbs_list_left">
                                                <div class="cbs_list_title"><span cb-trans="cb_essentialCookies"></div>
                                                <div class="cbs_list_description">
                                                    <small><span cb-trans="cb_essentialCookiesInfoText"></small>
                                                </div>
                                            </div>
                                            <div class="cbs_list_right">
                                                <button class="cb_btn_allow active" disabled style="cursor: default">✓ Allow</button> 
                                            </div>
                                     </li>
                                </ul>
                                    <ul id="cbs_service_list">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>';

        $body = $html->find('body')[0];
        $body->innertext = $body->innertext . $elements . "\n" ;

        return $html;
    }

}
