<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use App\Models\Aduan;
use App\Models\Korban;
use App\Models\Terlapor;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class JumlahTypeOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        return [
            Stat::make('Jumlah Aduan', Aduan::query()
                ->when($startDate, fn ($query) => $query->where('tanggal_masuk', '>=', $startDate))
                ->where('tanggal_masuk', '<=', $endDate)
                ->count())
                ->description(''),

            Stat::make('Jumlah Korban', Korban::query()
                ->join('aduan_korban', 'korbans.id', '=', 'aduan_korban.korban_id')
                ->join('aduans', 'aduan_korban.aduan_id', '=', 'aduans.id')
                ->when($startDate, fn ($query) => $query->where('aduans.tanggal_masuk', '>=', $startDate))
                ->where('aduans.tanggal_masuk', '<=', $endDate)
                ->count())
                ->description(''),

            Stat::make('Jumlah Terlapor', Terlapor::query()
                ->join('aduan_terlapor', 'terlapors.id', '=', 'aduan_terlapor.terlapor_id')
                ->join('aduans', 'aduan_terlapor.aduan_id', '=', 'aduans.id')
                ->when($startDate, fn ($query) => $query->where('aduans.tanggal_masuk', '>=', $startDate))
                ->where('aduans.tanggal_masuk', '<=', $endDate)
                ->count())
                ->description(''),
        ];
    }
}
