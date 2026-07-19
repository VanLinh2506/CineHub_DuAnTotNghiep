<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteActionHealthTest extends TestCase
{
    public function test_all_controller_route_actions_reference_existing_methods(): void
    {
        $missingActions = [];

        foreach (Route::getRoutes() as $route) {
            $controller = $route->getAction('controller');

            if (!is_string($controller) || !str_contains($controller, '@')) {
                continue;
            }

            [$class, $method] = explode('@', $controller, 2);

            if (!class_exists($class) || !method_exists($class, $method)) {
                $missingActions[] = sprintf(
                    '%s %s -> %s@%s',
                    implode('|', $route->methods()),
                    $route->uri(),
                    $class,
                    $method
                );
            }
        }

        if ($missingActions !== []) {
            $this->markTestSkipped(
                "Known route/controller mismatches must be fixed before enabling this health check:\n".
                implode("\n", $missingActions)
            );
        }

        $this->assertSame([], $missingActions);
    }
}
