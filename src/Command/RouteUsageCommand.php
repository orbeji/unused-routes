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
        $this->addOption('watch', 'w', null, 'Watch for changes and rebuild automatically');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //read all used routes
        $usedRoutes = $this->getUsedRoutes();
        //obtain all available routes
        $allRoutes = $this->getAllRouteNames();
        //remove used routes from all available ones
        $unusedRoutes = $this->getUnusedRoutes($usedRoutes, $allRoutes);

        $this->printResult($unusedRoutes, $input, $output);

        return self::SUCCESS;
    }

    private function getUsedRoutes(): array
    {
        return file($this->unusedRoutesFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public function getAllRouteNames(): array
    {
        $routeCollection = $this->router->getRouteCollection();

        $routeNames = [];
        foreach ($routeCollection->all() as $routeName => $route) {
            $routeNames[] = $routeName;
        }

        return $routeNames;
    }

    private function getUnusedRoutes(array $usedRoutes, array $allRoutes): array
    {
        $unusedRoutes = array();
        foreach ($allRoutes as $route) {
            if (!in_array($route, $usedRoutes)) {
                $unusedRoutes[] = $route;
            }
        }
        return $unusedRoutes;
    }

    private function printResult(array $unusedRoutes, InputInterface $input, OutputInterface $output): void
    {
        global $input;
        $io = new SymfonyStyle($input, $output);

        $io->table(
            ['Route'],
            $unusedRoutes
        );

    }
}