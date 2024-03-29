<?php

declare(strict_types=1);

namespace Orbeji\UnusedRoutes\Command;

use Orbeji\UnusedRoutes\Helper\RouteUsageHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'unused-routes:list',
    description: 'List all unused routes'
)]
final class RouteUsageCommand extends Command
{
    private RouteUsageHelper $routeUsageHelper;

    public function __construct(RouteUsageHelper $routeUsageHelper)
    {
        parent::__construct();
        $this->routeUsageHelper = $routeUsageHelper;
    }

    protected function configure(): void
    {
        $this->addOption('show-all-routes', 'a', null, 'Show all routes in results, used and unused');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $showAllRoutes = (bool)$input->getOption('show-all-routes');

        $routesUsage = $this->routeUsageHelper->getRoutesUsage($showAllRoutes);

        $this->printResult($routesUsage, $input, $output);

        return self::SUCCESS;
    }

    /**
     * @param array<int, array{value: string, count: int, date: string}> $routesUsage
     */
    private function printResult(array $routesUsage, InputInterface $input, OutputInterface $output): void
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $rows = [];
        foreach ($routesUsage as $routeUsage) {
            if ($routeUsage['count'] === 0) {
                $rows[] = $this->addRow($routeUsage, 'red');
            } else {
                $rows[] = $this->addRow($routeUsage, 'green');
            }
        }
        $symfonyStyle->table(
            ['Route', '#Uses', 'Last access'],
            $rows
        );
    }

    /**
     * @param array{value: string, count: int, date: string} $routeUsage
     * @return TableCell[]
     */
    private function addRow(array $routeUsage, string $color): array
    {
        return [
            new TableCell($routeUsage['value'], ['style' => new TableCellStyle(['fg' => $color,])]),
            new TableCell((string)$routeUsage['count'], ['style' => new TableCellStyle(['fg' => $color,])]),
            new TableCell($routeUsage['date'], ['style' => new TableCellStyle(['fg' => $color,])]),
        ];
    }
}
