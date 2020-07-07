<?php

namespace CookieBundle\Tool\Html;

use CookieBundle\Tool\Services;


class Google
{

    /**
     * @param $div
     * @return mixed
     */
    public static function getMapsV3($div)
    {
        $config = \CookieBundle\Tool\Config::get(true);
        $services = $config['service'];

        if ($services['googleMaps'] !== true)
            return $div;

        if (Services::isAllowed('googleMaps')) {
            return $div;
        }

        $div->innertext = '<div class="cb_container cb_container_loading">
                                <div class="cb_inner_active">
                                    <div class="cb_float">
                                        <div class="cb_active_title">Google Maps <span cb-trans="cb_fallback"></span></div>
                                        <button type="button" class="cb_btn_blocked" onclick="cbCookie.service.change(this, true)" cb-service="googleMaps">✓ <span cb-trans="cb_allow"></span></button>
                                    </div>
                                </div>
                            </div>';

        return $div;
    }


    /**
     * @param $input
     * @return mixed
     */
    public static function autocompleteInput($input)
    {
        $config = \CookieBundle\Tool\Config::get(true);
        $services = $config['service'];

        if ($services['googleMaps'] !== true)
            return $input;

        $caFlexbox = '';
        $caContinerClass = '';
        if (!Services::isAllowed('googleMaps')) {
            $caFlexbox = '<div class="cb_inner_box_flex">
                        <div class="cb_text">Google Maps <span cb-trans="cb_fallback"></span></div>
                        <button type="button" class="cb_btn_blocked cb_btn_googleAutocomplete" onclick="cbCookie.service.change(this, true)" cb-service="googleMaps">✓ <span cb-trans="cb_allow"></span></button>
                      </div>';
            $caContinerClass = 'cb_container_loading';
        }

        $input->outertext = '<div class="ca_googleAutocomplete_container '. $caContinerClass .'">
                                    ' . $caFlexbox . $input->outertext .'</div>';

        return $input;
    }


    /**
     * @param $div
     * @return mixed
     */
    public static function recaptchaV2($div)
    {
        $config = \CookieBundle\Tool\Config::get(true);
        $services = $config['service'];

        if ($services['recaptcha'] !== true) {
            return $div;
        }

        $div->innertext = '<div class="ca_googleRecaptcha_container cb_container_loading">
                                <div class="cb_inner_box_flex cb_flex_column">
                                <div class="cb_text">reCAPTCHA <span cb-trans="cb_fallback"></span></div>
                                <button type="button" class="cb_btn_blocked" onclick="cbCookie.service.change(this, true)" cb-service="recaptcha">✓ <span cb-trans="cb_allow"></button>
                                </div>                                
                           </div>';

    }

}
