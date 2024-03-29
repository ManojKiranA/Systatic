<?php

namespace Damcclean\Systatic\Config;

use Illuminate\Config\Repository;
use Brick\VarExporter\VarExporter;
use Symfony\Component\Dotenv\Dotenv;

class Config
{
    public function __construct()
    {
        $this->env = new Dotenv();
        $this->config = new Repository(require CONFIGURATION);
    }

    public function get(string $key)
    {
        $config = $this->config->get($key);

        if ($config != null) {
            return $config;
        }

        if (strpos($key, '.') != false) {
            $key = str_replace('.', '_', $key);
        }

        $env = $this->env(strtoupper($key));

        return $env;
    }

    public function getArray()
    {
        $config = include CONFIGURATION;

        return $config;
    }

    public function updateArray(array $data)
    {
        $config = include CONFIGURATION;
        $config = array_merge($config, $data);

        $str = '<?php ' . PHP_EOL
            . VarExporter::export($config, true) . ';' . PHP_EOL;

        file_write_contents(CONFIGURATION, $str);

        return $config;
    }

    public function env(string $key)
    {
        $env = $this->env->load(BASE . '/.env');

        return $_ENV[$key];
    }
}
