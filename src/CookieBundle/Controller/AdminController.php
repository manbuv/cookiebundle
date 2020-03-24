<?php


namespace CookieBundle\Controller;

use CookieBundle\CookieBundle;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * @Route("/cbcookie")
 */
class AdminController extends FrontendController
{

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->setViewAutoRender($event->getRequest(), true, 'php');
    }


    /**
     * @Route("/get-settings")
     */
    public function getSettingsAction(Request $request)
    {
        $settings = \CookieBundle\Tool\Config::get();
        $this->adminJson($settings);
    }


    /**
     * @param Request $request
     * @Route("/settings/save")
     */
    public function saveSettingsAction(Request $request)
    {
        $data = json_decode($request->get('data'));
        $values = (array)$data;

        $settings['service']['gtag'] = $values['service.gtag'];
        $settings['service']['gtagUa'] = $values['service.gtagUa'];

        $settings['service']['googleMaps'] = $values['service.googleMaps'];
        $settings['service']['googleMapsApiKey'] = $values['service.googleMapsApiKey'];

        $settings['service']['facebookPixel'] = $values['service.facebookPixel'];
        $settings['service']['facebookPixelId'] = $values['service.facebookPixelId'];

        $settings['service']['youtube'] = $values['service.youtube'];
        $settings['service']['vimeo'] = $values['service.vimeo'];
        $settings['service']['webContent'] = $values['service.webContent'];
        $settings['service']['recaptcha'] = $values['service.recaptcha'];

        \CookieBundle\Tool\Config::save($settings);

        $output = [
            'success' => true
        ];

        $this->adminJson($output);
    }


    /**
     * @param $arr
     */
    public function adminJson($arr)
    {
        echo \GuzzleHttp\json_encode($arr);
        exit;
    }

}
