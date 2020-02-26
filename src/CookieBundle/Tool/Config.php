<?php


namespace CookieBundle\Tool;


use Pimcore\File;
use Symfony\Component\Yaml\Yaml;

class Config
{

    /**
     * @param bool $onlyValues
     * @return array|mixed
     */
    public static function get($onlyValues = false)
    {
        $configYml = file_get_contents((new Config)->getConfigFile());
        $values = Yaml::parse($configYml);

        $settings = [
            'values' => $values,
            'config' => [],
        ];

        if ($onlyValues) {
            return $values;
        }
        return $settings;
    }


    /**
     * @param $config
     * @return bool
     */
    public static function save($config)
    {
        $settingsYml = Yaml::dump($config, 5);
        File::put((new Config)->getConfigFile(), $settingsYml);
        return true;
    }


    /**
     * @return mixed
     */
    private function getConfigFile()
    {
        $fileName = \Pimcore\Config::locateConfigFile('cookiebundle-config.yml');
        if (!file_exists($fileName)) {
            $demo = [
                'service' => []
            ];
            $settingsYml = Yaml::dump($demo, 5);
            File::put($fileName, $settingsYml);
        }

        return $fileName;
    }

}
