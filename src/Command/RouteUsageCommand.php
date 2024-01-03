<?php

namespace Orbeji\UnusedRoutes\Command;

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
class RouteUsageCommand extends Command
{
    private string $unusedRoutesFilePath;
    private RouterInterface $router;

    public function __construct(string $unusedRoutesFilePath, RouterInterface $router) {
        parent::__construct();
        $this->unusedRoutesFilePath = $unusedRoutesFilePath;
        $this->router = $router;
    }

    protected function configure(): void
    {
        $this->addOption('show-all', 'a', null, 'Show all routes in results');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $showAll = $input->getOption('show-all');

        $usedRoutes = $this->getUsedRoutes();
        $allRoutes = $this->getAllRouteNames();
        $unusedRoutes = $this->getRoutesUsageResult($usedRoutes, $allRoutes, $showAll);

        $this->printResult($unusedRoutes, $input, $output);

        return self::SUCCESS;
    }

    private function getUsedRoutes(): array
    {
        $usedRoutes = file($this->unusedRoutesFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $valueCounts = array_count_values($usedRoutes);

        // Format the data into an array with value and count of appearances
        $groupedArray = [];
        foreach ($valueCounts as $value => $count) {
            $groupedArray[$value] = [
                'value' => $value,
                'count' => $count,
            ];
        }
        return $groupedArray;
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