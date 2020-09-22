<?php

namespace CookieBundle\Tool\Html;

use CookieBundle\Tool\Services;


class Iframe
{

    /**
     * @param $iframe
     * @param bool $cookie
     * @return string
     */
    public static function getHtmlElement($iframe, $cookie = false)
    {
        $iframeSrc = $iframe->src;
        $provider = (new Iframe)->getProvider($iframeSrc);

        $providerClass = 'webContent';
        if (in_array($provider, ['youtube', 'vimeo'])) {
            $providerClass = 'video';
        }

        $providerArr = [
            'youtube' => 'Youtube',
            'vimeo' => 'Vimeo',
            'webContent' => 'WebContent',
        ];

        $sourceInfo = ($provider == 'webContent') ? 'Rescource: ' . (parse_url($iframeSrc, PHP_URL_HOST)) : '';

        if (!Services::isAllowed($provider)) {

            $videoId = '';
            if ($providerClass == 'video') {
                $videoId = (new Iframe)->getVideoId($iframeSrc, $provider);
            }
            $backgroundImg = '';
            $bgImgClass = '';
            if ($videoId) {
                $backgroundImg = (new Iframe)->getVideoPreview($videoId, $provider);
                $bgImgClass = 'cb_has_bg_img';
            }

            $serviceContent = '<div class="cb_inner_active cb_bg_cover" style="background-image: url('. $backgroundImg .')">
                                    <div class="cb_float '. $bgImgClass .'">
                                        <div class="cb_active_title"> '. $providerArr[$provider] .' <span cb-trans="cb_fallback"></span></div>
                                        <div class="cg_active_info">'. $sourceInfo .'</div>
                                        <button type="button" class="cb_btn_blocked" onclick="cbCookie.service.change(this, true)" cb-service="'. $provider .'">✓ <span cb-trans="cb_allow"></span></button>
                                    </div>
                                </div>';
            $iframe->src = '';
        }

        $output = '<div class="cb_container cb_container_iframe cb_container_'. $providerClass .' cb_container_iframe_'. $provider .' cb_container_loading" cb-data-url="'. $iframeSrc .'">
                                '. $iframe->outertext . $serviceContent .'
                            </div>';

        return $iframe->outertext = $output;
    }


    /**
     * @param $iframeSrc
     * @param $provider
     * @return mixed|null
     */
    private function getVideoId($iframeSrc, $provider)
    {
        if ($provider == 'youtube') {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $iframeSrc, $match);
            return $match[1];
        }
        if ($provider == 'vimeo') {
            if(preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $iframeSrc, $output_array)) {
                return $output_array[5];
            }
        }
        return NULL;
    }


    /**
     * @param $id
     * @param $provider
     * @return string
     */
    private function getVideoPreview($id, $provider)
    {
        $fileName = PIMCORE_PUBLIC_VAR . '/'. $provider .'-pv-' . $id . '.jpg';

        if (file_exists($fileName)) {
            return '/var/' . $provider .'-pv-' . $id . '.jpg';
        } else {

            $placeholder = file_get_contents(PIMCORE_WEB_ROOT . '/bundles/cookie/img/gmaps.png');

            if ($provider == 'youtube') {
                try {
                    $fileContent = file_get_contents('https://img.youtube.com/vi/'. $id .'/maxresdefault.jpg');
                    file_put_contents($fileName, $fileContent);
                } catch (\Exception $e) {
                    file_put_contents($fileName, $placeholder);
                }
            }
            if ($provider == 'vimeo') {
                try {
                    $data = file_get_contents("https://vimeo.com/api/v2/video/$id.json");
                    $data = json_decode($data);
                    if ($data[0]) {
                        $fileContent = file_get_contents($data[0]->thumbnail_large);
                        file_put_contents($fileName, $fileContent);
                    } else {
                        file_put_contents($fileName, $placeholder);
                    }
                } catch (\Exception $e) {
                    file_put_contents($fileName, $placeholder);
                }
            }

        }
        return '/var/' . $provider .'-pv-' . $id . '.jpg';
    }


    /**
     * @param $url
     * @return string
     */
    private function getProvider($url)
    {
        if (preg_match('%youtube|youtu\.be%i', $url)) {
            return 'youtube';
        }
        elseif (preg_match('%vimeo%i', $url)) {
            return 'vimeo';
        }
        return 'webContent';
    }

}
