<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Routes\Router;
use ReflectionClass;

class RouterTest extends TestCase
{
    private function getRegisteredRoutes(): array
    {
        $ref = new ReflectionClass(Router::class);
        $prop = $ref->getProperty('routes');
        return $prop->getValue();
    }

    public function testRegisterGetRouteNormalizesPath(): void
    {
        Router::get('/unit-test-get-route/', function () {
            return 'OK';
        });

        $routes = $this->getRegisteredRoutes();
        $found = false;

        foreach ($routes as $r) {
            if ($r['method'] === 'GET' && $r['path'] === '/unit-test-get-route') {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, "Route GET /unit-test-get-route should be registered and normalized without trailing slash.");
    }

    public function testRegisterPostRoute(): void
    {
        Router::post('/unit-test-post-route', 'AuthController@login');

        $routes = $this->getRegisteredRoutes();
        $found = false;

        foreach ($routes as $r) {
            if ($r['method'] === 'POST' && $r['path'] === '/unit-test-post-route') {
                $found = true;
                $this->assertEquals('AuthController@login', $r['handler']);
                break;
            }
        }

        $this->assertTrue($found, "Route POST /unit-test-post-route should be registered.");
    }
}

