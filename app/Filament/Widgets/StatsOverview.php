<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Classes;
use App\Models\Sections;
use App\Models\Student;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Classes', Classes::count()),
            Stat::make('Total Sections', Sections::count()),
            Stat::make('Total Students', Student::count()),
        ];
    }
}
