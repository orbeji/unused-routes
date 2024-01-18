<?php

declare(strict_types=1);

namespace Orbeji\UnusedRoutes\Entity;

class UsedRoute
{
    private string $route;

    private int $timestamp;

    private int $visits;

    private function __construct(string $route, int $timestamp, int $visits)
    {
        $this->route = $route;
        $this->timestamp = $timestamp;
        $this->visits = $visits;
    }

    public static function newVisit(string $route): self
    {
        return new self($route, time(), 1);
    }

    public static function fromGroupedData(string $route, int $timestamp, int $visits): self
    {
        return new self($route, $timestamp, $visits);
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getVisits(): int
    {
        return $this->visits;
    }
}
