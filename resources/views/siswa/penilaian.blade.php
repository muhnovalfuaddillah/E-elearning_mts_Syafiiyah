@extends('layouts.app')

@section('title', 'Nilai Saya - Pembelajaran Digital')
@section('breadcrumb', 'Penilaian')
@section('page-title', 'Rekapitulasi Nilai Akademik')

@section('content')
<div class="w-full px-4 md:px-6 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Tabel Nilai -->
        <div class="lg:col-span-2 space-y-6">
            <div class="luxury-card overflow-hidden">
                <div class="p-4 md:p-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
                    <div>
                        <h6 class="text-white font-semibold text-lg">Transkrip Nilai Saya</h6>
                        <p class="text-white/40 text-xs md:text-sm">Rincian nilai harian (tugas), ujian tengah semester (UTS), dan akhir semester (UAS).</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[600px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                 <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                                 <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                                 <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-48">Rata Harian (40%)</th>
                                 <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">UTS (30%)</th>
                                 <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">UAS (30%)</th>
                                 <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-28">Nilai Akhir</th>
                             </tr>
                         </thead>
                         <tbody>
                             @forelse($nilai as $index => $item)
                                 <tr class="border-b border-white/5 hover:bg-white/5">
                                     <td class="p-3 md:p-4 text-white/80 text-sm">{{ $index + 1 }}</td>
                                     <td class="p-3 md:p-4 text-white font-medium text-sm">
                                         {{ $item->mapel->nama_mapel ?? 'Mapel' }}
                                         <span class="block text-[10px] text-white/40 mt-0.5">{{ $item->mapel->kode_mapel ?? '' }}</span>
                                     </td>
                                     <td class="p-3 md:p-4 text-center text-white/80 text-sm">
                                         <div class="font-bold text-white">{{ $item->nilai_harian !== null ? number_format($item->nilai_harian, 1) : '-' }}</div>
                                         <div class="text-[9px] text-white/40 mt-1 flex flex-wrap justify-center gap-1">
                                             <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 1">H1: {{ $item->nilai_harian_1 !== null ? round($item->nilai_harian_1) : '-' }}</span>
                                             <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 2">H2: {{ $item->nilai_harian_2 !== null ? round($item->nilai_harian_2) : '-' }}</span>
                                             <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 3">H3: {{ $item->nilai_harian_3 !== null ? round($item->nilai_harian_3) : '-' }}</span>
                                             <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 4">H4: {{ $item->nilai_harian_4 !== null ? round($item->nilai_harian_4) : '-' }}</span>
                                             <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 5">H5: {{ $item->nilai_harian_5 !== null ? round($item->nilai_harian_5) : '-' }}</span>
                                             <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 6">H6: {{ $item->nilai_harian_6 !== null ? round($item->nilai_harian_6) : '-' }}</span>
                                         </div>
                                     </td>
                                    <td class="p-3 md:p-4 text-center text-white/80 text-sm">{{ $item->nilai_uts ?? '-' }}</td>
                                    <td class="p-3 md:p-4 text-center text-white/80 text-sm">{{ $item->nilai_uas ?? '-' }}</td>
                                    <td class="p-3 md:p-4 text-center font-bold text-sm">
                                        @php
                                            $val = $item->nilai_akhir;
                                            $badgeColor = 'text-purple-400 bg-purple-500/10 border-purple-500/20';
                                            if ($val >= 85) {
                                                $badgeColor = 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20';
                                            } elseif ($val >= 75) {
                                                $badgeColor = 'text-blue-400 bg-blue-500/10 border-blue-500/20';
                                            } elseif ($val > 0) {
                                                $badgeColor = 'text-red-400 bg-red-500/10 border-red-500/20';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-lg border {{ $badgeColor }}">
                                            {{ $val ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-white/50">
                                        <i class="fas fa-folder-open text-5xl mb-3 text-white/10"></i>
                                        <p>Belum ada rincian nilai yang terdaftar.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Grafik Visualisasi Kekuatan Akademik -->
        <div class="space-y-6">
            <div class="luxury-card p-6">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-white/10">
                    <i class="fas fa-chart-pie text-pink-400 text-lg"></i>
                    <h5 class="text-white font-bold text-lg">Analisis Akademik</h5>
                </div>
                
                <div class="relative w-full aspect-square max-w-[280px] mx-auto">
                    <canvas id="gradeRadarChart"></canvas>
                </div>

                 <div class="mt-6 p-4 bg-white/5 rounded-xl border border-white/5 text-xs text-white/50 leading-relaxed">
                     <p class="font-bold text-purple-400 mb-1.5"><i class="fas fa-info-circle"></i> Info Rumus Nilai Akhir:</p>
                     <ul class="list-disc list-inside space-y-1">
                         <li>Bobot Rata Harian: <strong>40%</strong> (Rata-rata Harian 1-6)</li>
                         <li>Bobot Nilai UTS: <strong>30%</strong></li>
                         <li>Bobot Nilai UAS: <strong>30%</strong></li>
                     </ul>
                 </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('gradeRadarChart');
        if (!ctx) return;

        @php
            $labels = [];
            $dataNilai = [];
            foreach ($nilai as $n) {
                $labels[] = $n->mapel->nama_mapel;
                $dataNilai[] = $n->nilai_akhir;
            }
        @endphp

        const labels = {!! json_encode($labels) !!};
        const dataValues = {!! json_encode($dataNilai) !!};

        if (labels.length === 0) {
            ctx.parentElement.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-center p-6 text-white/30">
                    <i class="fas fa-chart-line text-4xl mb-2 text-white/10 text-slate-500"></i>
                    <p class="text-xs">Grafik radar membutuhkan minimal 1 nilai mata pelajaran.</p>
                </div>
            `;
            return;
        }

        const config = {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Akhir',
                    data: dataValues,
                    fill: true,
                    backgroundColor: 'rgba(168, 85, 247, 0.2)',
                    borderColor: 'rgba(168, 85, 247, 1)',
                    pointBackgroundColor: 'rgba(236, 72, 153, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(236, 72, 153, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    r: {
                        angleLines: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        pointLabels: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            font: {
                                size: 9
                            }
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.3)',
                            backdropColor: 'transparent',
                            showLabelBackdrop: false
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        };

        let radarChart = new Chart(ctx, config);

        window.addEventListener('theme-changed', function() {
            const isLight = document.body.classList.contains('light-theme');
            radarChart.options.scales.r.angleLines.color = isLight ? 'rgba(0, 0, 0, 0.05)' : 'rgba(255, 255, 255, 0.05)';
            radarChart.options.scales.r.grid.color = isLight ? 'rgba(0, 0, 0, 0.05)' : 'rgba(255, 255, 255, 0.05)';
            radarChart.options.scales.r.pointLabels.color = isLight ? 'rgba(0, 0, 0, 0.6)' : 'rgba(255, 255, 255, 0.5)';
            radarChart.options.scales.r.ticks.color = isLight ? 'rgba(0, 0, 0, 0.4)' : 'rgba(255, 255, 255, 0.3)';
            radarChart.update();
        });
    });
</script>
@endsection
