<?php

namespace CookieBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;


class CookieBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'manbuv/cookie';


    /**
     * @return string
     */
    public function getComposerPackageName()
    {
        return self::PACKAGE_NAME;
    }


    /**
     * @return string
     */
    public function getVersion()
    {
        return 'v1.3.4';
    }


    /**
     * @return array|\Pimcore\Routing\RouteReferenceInterface[]|string[]
     */
    public function getJsPaths()
    {
        return [
            '/bundles/cookie/admin/js/menu.js',
            '/bundles/cookie/admin/js/settings.js',
        ];
    }

    /**
     * @return array|\Pimcore\Routing\RouteReferenceInterface[]|string[]
     */
    public function getCssPaths()
    {
        return [
            '/bundles/cookie/admin/css/styles.css',
        ];
    }

}
