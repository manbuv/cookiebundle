<?php


namespace CookieBundle\Controller;

use CookieBundle\CookieBundle;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Pimcore\Controller\FrontendController;
use Pimcore\Tool\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Pimcore\Model\Translation;

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

        $config = \CookieBundle\Tool\Config::get();
        $version = $config['values']['version'];
        if (!$version)
            $version = 1;
        else
            $version++;

        // Google Analytics
        $settings['service']['gtag'] = $values['service.gtag'];
        $settings['service']['gtagUa'] = $values['service.gtagUa'];

        // Google Tagmanager
        $settings['service']['googleTagmanager'] = $values['service.googleTagmanager'];
        $settings['service']['googleTagmanagerId'] = $values['service.googleTagmanagerId'];

        // Google Maps
        $settings['service']['googleMaps'] = $values['service.googleMaps'];
        $settings['service']['googleMapsApiKey'] = $values['service.googleMapsApiKey'];
        $settings['service']['googleMapsLibraries'] = (!$values['service.googleMapsLibraries']) ? 'places' : $values['service.googleMapsLibraries'];

        // Facebook Pixel
        $settings['service']['facebookPixel'] = $values['service.facebookPixel'];
        $settings['service']['facebookPixelId'] = $values['service.facebookPixelId'];

        $settings['service']['youtube'] = $values['service.youtube'];
        $settings['service']['vimeo'] = $values['service.vimeo'];
        $settings['service']['webContent'] = $values['service.webContent'];
        $settings['service']['recaptcha'] = $values['service.recaptcha'];

        $settings['version'] = $version;

        \CookieBundle\Tool\Config::save($settings);

        $output = [
            'success' => true
        ];

        $this->adminJson($output);
    }


    /**
     * @param Request $request
     * @Route("/importTranslations")
     */
    public function importTranslationsAction(Request $request)
    {
        $csv = PIMCORE_COMPOSER_PATH . '/manbuv/cookie/src/CookieBundle/Resources/translation/cb-translations.csv';

        try {
            Translation\Website::importTranslationsFromFile($csv, true, Admin::getLanguages());
        } catch (\Exception $e) {
            throw new AbortMigrationException(sprintf('Failed to install admin translations. "%s"', $e->getMessage()));
        }

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
