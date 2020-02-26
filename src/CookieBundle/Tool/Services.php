<?php


namespace CookieBundle\Tool;


class Services
{

    /**
     * @param $service
     * @return bool
     */
    public static function isAllowed($service)
    {

        if (!$cookieString = $_COOKIE['cbcookie']) {
            return false;
        }

        $cookie = [];
        $cookieArr = explode_and_trim('!', $cookieString);
        foreach ($cookieArr as $val) {
            $arr = explode_and_trim('=', $val);
            $cookie[$arr[0]] = $arr[1];
        }

        if ($cookie[$service] == 'true')
            return true;

        return false;
    }

}
