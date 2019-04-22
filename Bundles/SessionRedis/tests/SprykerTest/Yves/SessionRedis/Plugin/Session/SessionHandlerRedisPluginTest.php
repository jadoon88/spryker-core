<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Yves\SessionRedis\Plugin\Session;

use Codeception\Test\Unit;
use Spryker\Shared\SessionRedis\Handler\KeyBuilder\SessionKeyBuilder;
use Spryker\Shared\SessionRedis\SessionRedisConfig;
use Spryker\Yves\SessionRedis\Plugin\Session\SessionHandlerRedisPlugin;
use Spryker\Yves\SessionRedis\SessionRedisFactory;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Yves
 * @group SessionRedis
 * @group Plugin
 * @group Session
 * @group SessionHandlerRedisPluginTest
 * Add your own group annotations below this line
 */
class SessionHandlerRedisPluginTest extends Unit
{
    protected const SESSION_ID = 'yves_session_id';
    protected const DUMMY_DATA = 'dummy data';

    /**
     * @var \Spryker\Yves\SessionRedis\Plugin\Session\SessionHandlerRedisPlugin
     */
    protected $sessionHandlerPlugin;

    /**
     * @var \Spryker\Shared\SessionRedis\Redis\SessionRedisWrapperInterface
     */
    protected $redisClient;

    /**
     * @var \Spryker\Shared\SessionRedis\Handler\KeyBuilder\SessionKeyBuilderInterface
     */
    protected $sessionKeyBuilder;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sessionHandlerPlugin = new SessionHandlerRedisPlugin();
        $this->setupRedisClient();
        $this->setupSessionKeyBuilder();
    }

    /**
     * @return void
     */
    public function testHasCorrectSessionHandlerName(): void
    {
        $this->assertEquals(SessionRedisConfig::SESSION_HANDLER_REDIS_NAME, $this->sessionHandlerPlugin->getSessionHandlerName());
    }

    /**
     * @return void
     */
    public function testCanConnectToRedisWhenOpeningSession(): void
    {
        $this->sessionHandlerPlugin->open('save path', 'session name');

        $this->assertTrue($this->redisClient->isConnected());
    }

    /**
     * @return void
     */
    public function testCanWriteSessionDataToRedis(): void
    {
        $this->sessionHandlerPlugin->write(static::SESSION_ID, static::DUMMY_DATA);
        $this->assertEquals(static::DUMMY_DATA, $this->getSessionData());
    }

    /**
     * @depends testCanWriteSessionDataToRedis
     *
     * @return void
     */
    public function testCanReadSessionDataFromRedis(): void
    {
        $this->assertEquals(static::DUMMY_DATA, $this->sessionHandlerPlugin->read(static::SESSION_ID));
    }

    /**
     * @depends testCanWriteSessionDataToRedis
     *
     * @return void
     */
    public function testCanDestroySessionData(): void
    {
        $this->sessionHandlerPlugin->destroy(static::SESSION_ID);

        $this->assertEmpty($this->getSessionData());
    }

    /**
     * @return void
     */
    public function testCallToGcReturnsTrue(): void
    {
        $this->assertTrue($this->sessionHandlerPlugin->gc(1));
    }

    /**
     * @return void
     */
    protected function setupRedisClient(): void
    {
        $this->redisClient = (new SessionRedisFactory())->createSessionRedisWrapper();
    }

    /**
     * @return string|null
     */
    protected function getSessionData(): ?string
    {
        $sessionData = $this->redisClient->get(
            $this->getSessionKeyFromSessionId(static::SESSION_ID)
        );

        return json_decode($sessionData);
    }

    /**
     * @param string $sessionId
     *
     * @return string
     */
    protected function getSessionKeyFromSessionId(string $sessionId): string
    {
        return $this->sessionKeyBuilder->buildSessionKey($sessionId);
    }

    /**
     * @return void
     */
    protected function setupSessionKeyBuilder(): void
    {
        $this->sessionKeyBuilder = new SessionKeyBuilder();
    }
}
