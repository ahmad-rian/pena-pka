<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class KanalPengaduanChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Kasus Berdasarkan Kanal Pengaduan';

    use InteractsWithPageFilters;

    protected function getData(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        // Definisikan pemetaan nama panjang ke nama pendek
        $labelMapping = [
            'NON ADUAN - LAPORAN MEDIA' => 'Jenis 1',
            'NOTA DINAS' => 'Jenis 2',
            'PENGADUAN LANGSUNG' => 'Jenis 3',
            'SAPA 129 - GFORM' => 'Jenis 4',
            'SAPA 129 - HOTLINE WA' => 'Jenis 5',
            'SAPA 129 - TELEPON' => 'Jenis 6',
            'SP4N LAPOR' => 'Jenis 7',
            'SURAT' => 'Jenis 8',
            'Mobile Apps' => 'Jenis 9',
        ];

        // Inisialisasi dataset dengan semua label yang di-set ke nilai nol
        $initialData = collect($labelMapping)->mapWithKeys(function ($shortLabel) {
            return [$shortLabel => 0];
        });

        // Ambil data dari tabel 'aduans' berdasarkan 'kanal_pengaduan'
        $data = DB::table('aduans')
            ->select('kanal_pengaduan', DB::raw('count(*) as count'))
            ->when($startDate, fn ($query) => $query->where('tanggal_masuk', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->where('tanggal_masuk', '<=', $endDate))
            ->groupBy('kanal_pengaduan')
            ->orderBy('count', 'desc')
            ->get();

        // Map data yang diambil ke label pendek
        $mappedData = $data->pluck('count', 'kanal_pengaduan')->mapWithKeys(function ($count, $kanal_pengaduan) use ($labelMapping) {
            return [$labelMapping[$kanal_pengaduan] ?? $kanal_pengaduan => $count];
        });

        // Gabungkan data awal dengan data yang di-mapping untuk memastikan semua label ada
        $finalData = $initialData->merge($mappedData);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kasus Berdasarkan Kanal Pengaduan',
                    'data' => $finalData->values()->toArray(),
                    'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $finalData->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    // Render chart dengan penjelasan yang sesuai dengan kanal_pengaduan
    public function render(): View
    {
        // Penjelasan sesuai dengan labelMapping
        $explanations = [
            'Jenis 1' => 'NON ADUAN - LAPORAN MEDIA',
            'Jenis 2' => 'NOTA DINAS',
            'Jenis 3' => 'PENGADUAN LANGSUNG',
            'Jenis 4' => 'SAPA 129 - GFORM',
            'Jenis 5' => 'SAPA 129 - HOTLINE WA',
            'Jenis 6' => 'SAPA 129 - TELEPON',
            'Jenis 7' => 'SP4N LAPOR',
            'Jenis 8' => 'SURAT',
            'Jenis 9' => 'Mobile Apps',
        ];

        // Generate chart
        $chart = parent::render();

        return view('filament.widgets.kanal_chart', [
            'chart' => $chart,
            'explanations' => $explanations,
        ]);
    }
}
