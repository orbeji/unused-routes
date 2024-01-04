<?php

namespace Orbeji\UnusedRoutes\Command;

use Orbeji\UnusedRoutes\Provider\UsageRouteProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'unused-routes:list',
    description: 'List all unused routes'
)]
final class RouteUsageCommand extends Command
{
    private RouterInterface $router;
    private UsageRouteProviderInterface $usageRouteProvider;

    public function __construct(UsageRouteProviderInterface $usageRouteProvider, RouterInterface $router) {
        parent::__construct();
        $this->router = $router;
        $this->usageRouteProvider = $usageRouteProvider;
    }

    protected function configure(): void
    {
        $this->addOption('show-all', 'a', null, 'Show all routes in results');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $showAll = $input->getOption('show-all');

        $usedRoutes = $this->usageRouteProvider->getRoutesUsage();
        $allRoutes = $this->getAllRouteNames();
        $unusedRoutes = $this->getRoutesUsageResult($usedRoutes, $allRoutes, $showAll);

        $this->printResult($unusedRoutes, $input, $output);

        return self::SUCCESS;
    }

    public function getAllRouteNames(): array
    {
        $routeCollection = $this->router->getRouteCollection();

        $routeNames = [];
        foreach ($routeCollection->all() as $routeName => $route) {
            if (!str_starts_with($routeName, '_')) {
                $routeNames[] = $routeName;
            }
        }

        return $routeNames;
    }

    private function getRoutesUsageResult(array $usedRoutes, array $allRoutes, bool $showAll): array
    {
        $unusedRoutes = array();
        foreach ($allRoutes as $route) {
            if ($showAll && $this->existRouteInArray($route, $usedRoutes)) {
                $unusedRoutes[] = $usedRoutes[$route];
            } elseif (!$this->existRouteInArray($route, $usedRoutes)) {
                $unusedRoutes[] = [$route, 0];
            }
        }
        return $unusedRoutes;
    }

    private function existRouteInArray(string $route, array $usedRoutes): bool
    {
        $valueExists = false;
        foreach ($usedRoutes as $item) {
            if ($item['value'] === $route) {
                $valueExists = true;
                break;
            }
        }
        return $valueExists;
    }

    private function printResult(array $unusedRoutes, InputInterface $input, OutputInterface $output): void
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->table(
            ['Route', '#Uses'],
            $unusedRoutes
        );
    }
}