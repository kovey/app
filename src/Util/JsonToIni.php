<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-04-06 10:41:35
 *
 */
namespace Kovey\App\Util;

class JsonToIni
{
    public static function jsonToIni(string $path) : void
    {
        if (!is_dir($path)) {
            return;
        }

        $files = scandir($path);
        foreach ($files as $file) {
            $suffix = substr($file, -9);
            if ($suffix === false || strtolower($suffix) !== '.ini.json') {
                continue;
            }

            $filePath = $path . '/' . $file;
            $content = file_get_contents($filePath);
            if (empty($content)) {
                continue;
            }

            try {
                $result = array();
                $config = json_decode($content, true);
                foreach ($config as $area => $conf) {
                    if (!is_array($conf)) {
                        continue;
                    }

                    $result[] = "[" . $area . "]\n";
                    $result[] = self::toIni($conf);
                }

                file_put_contents(str_replace('.ini.json', '.ini', $filePath), implode("", $result));
            } catch (\Throwable $e) {
            }
        }
    }

    protected static function toIni(Array $config, string | int | bool $key = false) : string
    {
        $result = '';
        foreach ($config as $k => $value) {
            $field = $k;
            if ($key !== false) {
                $field = $key .'.' . $k;
            }

            if (is_array($value)) {
                $result .= self::toIni($value, $field);
                continue;
            }

            $result .= $field .'=' . $value . "\n";
        }

        return $result;
    }
}
