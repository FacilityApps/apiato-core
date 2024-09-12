<?php

namespace Apiato\Core\Traits\TestTraits\PhpUnit;

trait TestDatabaseProfilerTrait
{
    protected function startDatabaseProfiler(): void
    {
        $this->app->make('db')->enableQueryLog();
    }

    protected function stopDatabaseProfiler(): void
    {
        $this->app->make('db')->disableQueryLog();
    }

    protected function getDatabaseQueries(): array
    {
        return $this->app->make('db')->getQueryLog();
    }

    protected function dumpDatabaseQueries(): void
    {
        foreach ($this->getDatabaseQueries() as $query) {
            dump($query['query']);
        }
    }

    protected function ddDatabaseQueries(): void
    {
        dd($this->getDatabaseQueries());
    }

    protected function assertDatabaseQueriesCount(int $expectedCount): void
    {
        $actualCount = count($this->getDatabaseQueries());
        $this->assertEquals($expectedCount, $actualCount, "Expected $expectedCount database queries, but got $actualCount.");
    }

    protected function profileDatabaseQueries(callable $callback): mixed
    {
        $this->startDatabaseProfiler();
        $result = $callback();
        $this->stopDatabaseProfiler();

        return $result;
    }

    protected function profileDatabaseQueriesCount(int $expectedCount, callable $callback): mixed
    {
        return $this->profileDatabaseQueries(function () use ($expectedCount, $callback) {
            $result = $callback();
            $this->assertDatabaseQueriesCount($expectedCount);

            return $result;
        });
    }
}
