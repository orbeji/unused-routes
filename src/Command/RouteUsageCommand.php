<?php

namespace Orbeji\UnusedRoutes\Command;

use Orbeji\UnusedRoutes\Entity\UsedRoute;
use Orbeji\UnusedRoutes\Provider\UsageRouteProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
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

    public function __construct(UsageRouteProviderInterface $usageRouteProvider, RouterInterface $router)
    {
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
        $routesUsage = $this->getRoutesUsageResult($usedRoutes, $allRoutes, $showAll);

        $this->printResult($routesUsage, $input, $output);

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

    /**
     * @param UsedRoute[] $usedRoutes
     * @param array $allRoutes
     * @param bool $showAll
     * @return array
     */
    private function getRoutesUsageResult(array $usedRoutes, array $allRoutes, bool $showAll): array
    {
        $unusedRoutes = array();
        foreach ($allRoutes as $route) {
            $existRouteInArray = $this->existRouteInArray($route, $usedRoutes);
            if ($showAll && $existRouteInArray) {
                $unusedRoutes[] = [
                    'value' => $existRouteInArray->getRoute(),
                    'count' => $existRouteInArray->getVisits(),
                    'date' => date('d/m/Y', $existRouteInArray->getTimestamp()),
                ];
            } elseif (!$existRouteInArray) {
                $unusedRoutes[] = ['value' => $route, 'count' => 0, 'date' => '-'];
            }
        }
        return $unusedRoutes;
    }

    /**
     * @param string $route
     * @param UsedRoute[] $usedRoutes
     * @return UsedRoute|bool
     */
    private function existRouteInArray(string $route, array $usedRoutes): UsedRoute|bool
    {
        foreach ($usedRoutes as $usedRoute) {
            if ($usedRoute->getRoute() === $route) {
                return $usedRoute;
            }
        }
        return false;
    }

    private function printResult(array $routesUsage, InputInterface $input, OutputInterface $output): void
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $rows = [];
        foreach ($routesUsage as $routeUsage) {
            if ($routeUsage['count'] === 0) {
                $rows[] = [
                    new TableCell($routeUsage['value'], ['style' => new TableCellStyle(['fg' => 'red',])]),
                    new TableCell($routeUsage['count'], ['style' => new TableCellStyle(['fg' => 'red',])]),
                    new TableCell($routeUsage['date'], ['style' => new TableCellStyle(['fg' => 'red',])]),
                ];
            } else {
                $rows[] = [
                    new TableCell($routeUsage['value'], ['style' => new TableCellStyle(['fg' => 'green',])]),
                    new TableCell($routeUsage['count'], ['style' => new TableCellStyle(['fg' => 'green',])]),
                    new TableCell($routeUsage['date'], ['style' => new TableCellStyle(['fg' => 'green',])]),
                ];
            }
        }
        $symfonyStyle->table(
            ['Route', '#Uses', 'Last access'],
            $rows
        );
    }
}