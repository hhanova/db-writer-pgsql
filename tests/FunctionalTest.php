<?php
/**
 * Created by PhpStorm.
 * User: miroslavcillik
 * Date: 27/10/16
 * Time: 17:20
 */

namespace Keboola\DbWriter\Writer\Pgsql\Tests;

use Keboola\DbWriter\Test\BaseTest;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class FunctionalTest extends BaseTest
{
    const DRIVER = 'Pgsql';

    protected $dataDir = ROOT_PATH . 'tests/data/functional';

    protected $tmpDataDir = '/tmp/data';

    protected $defaultConfig;

    public function setUp()
    {
        // cleanup & init
        $this->prepareDataFiles();
        $this->defaultConfig = $this->initConfig();

        var_dump(json_encode($this->defaultConfig)); die;

        $writer = $this->getWriter($this->defaultConfig['parameters']);

        foreach ($this->defaultConfig['parameters']['tables'] as $table) {
            // clean destination DB
            $writer->drop($table['dbName']);
        }
    }

    public function testRun()
    {
        $process = new Process('php ' . ROOT_PATH . 'run.php --data=' . $this->dataDir . ' 2>&1');
        $process->run();

        var_dump($process->getOutput());
        var_dump($process->getErrorOutput());
        die;

        $this->assertEquals(0, $process->getExitCode());
    }

//    public function testRunEmptyTable()
//    {
//        $this->initConfig(function () {
//            $config = $this->defaultConfig;
//            $tables = array_map(function ($table) {
//                $table['items'] = array_map(function ($item) {
//                    $item['type'] = 'IGNORE';
//                    return $item;
//                }, $table['items']);
//                return $table;
//            }, $config['parameters']['tables']);
//            $config['parameters']['tables'] = $tables;
//
//            return $config;
//        });
//
//        $process = new Process('php ' . ROOT_PATH . 'run.php --data=' . $this->dataDir);
//        $process->run();
//
//        $this->assertEquals(0, $process->getExitCode());
//    }

    public function testTestConnection()
    {
        $this->initConfig(function ($config) {
            $config['action'] = 'testConnection';
            return $config;
        });

        $process = new Process('php ' . ROOT_PATH . 'run.php --data=' . $this->dataDir . ' 2>&1');
        $process->run();

        $this->assertEquals(0, $process->getExitCode());

        $data = json_decode($process->getOutput(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('success', $data['status']);
    }

    private function initConfig(callable $callback = null)
    {
        $yaml = new Yaml();
        $configPath = $this->dataDir . '/config.json';
        $config = $yaml->parse(file_get_contents($configPath));

        $config['parameters']['writer_class'] = self::DRIVER;
        $config['parameters']['db']['user'] = $this->getEnv(self::DRIVER, 'DB_USER', true);
        $config['parameters']['db']['#password'] = $this->getEnv(self::DRIVER, 'DB_PASSWORD', true);
        $config['parameters']['db']['password'] = $this->getEnv(self::DRIVER, 'DB_PASSWORD', true);
        $config['parameters']['db']['host'] = $this->getEnv(self::DRIVER, 'DB_HOST');
        $config['parameters']['db']['port'] = $this->getEnv(self::DRIVER, 'DB_PORT');
        $config['parameters']['db']['database'] = $this->getEnv(self::DRIVER, 'DB_DATABASE');
        $config['parameters']['db']['schema'] = $this->getEnv(self::DRIVER, 'DB_SCHEMA');


        if ($callback !== null) {
            $config = $callback($config);
        }

        $tmpConfigPath = $this->tmpDataDir . '/config.json';
        @unlink($tmpConfigPath);
        file_put_contents($tmpConfigPath, $yaml->dump($config));

        return $config;
    }

    private function prepareDataFiles()
    {
        $fs = new Filesystem();
        $fs->remove($this->tmpDataDir);
        $fs->mkdir($this->tmpDataDir);
        $fs->mkdir($this->tmpDataDir . '/in/tables/');
        $fs->copy(
            $this->dataDir . '/in/tables/simple.csv',
            $this->tmpDataDir . '/in/tables/simple.csv'
        );
        $fs->copy(
            $this->dataDir . '/in/tables/simple_increment.csv',
            $this->tmpDataDir . '/in/tables/simple_increment.csv'
        );
        $fs->copy(
            $this->dataDir . '/in/tables/special.csv',
            $this->tmpDataDir . '/in/tables/special.csv'
        );
    }
}
