<?php

namespace App\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Filament\Widgets\Provinsi;
use App\Filament\Widgets\LayananChart;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use App\Filament\Widgets\JenisKelaminChart;
use App\Filament\Widgets\JumlahTypeOverview;
use App\Filament\Widgets\JenisKekerasanChart;
use App\Filament\Widgets\KanalPengaduanChart;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            JumlahTypeOverview::class,
            KanalPengaduanChart::class,
            JenisKelaminChart::class,
            JenisKekerasanChart::class,
            LayananChart::class,
            Provinsi::class,
        ];
    }
}
