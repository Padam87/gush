<?php

/**
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests;

use Gush\Application;
use Gush\Tester\Adapter\TestAdapter;
use Gush\Config;
use Symfony\Component\Console\Input\InputInterface;

class TestableApplication extends Application
{
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function buildAdapter(InputInterface $input = null, Config $config = null)
    {
        $adapter = new TestAdapter($this->config, 'gushphp', 'gush');
        $this->setAdapter($adapter);

        return $adapter;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    protected function readParameters()
    {
        $this->setConfig(\PHPUnit_Framework_MockObject_Generator::getMock('Gush\Config'));
    }
}
