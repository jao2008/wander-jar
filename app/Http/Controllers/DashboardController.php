<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GroupMessage;
use App\Models\Pin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Total de pins do utilizador
        $totalPins = Pin::where('user_id', $user->id)->count();

        // Grupos do utilizador
        $groups = $user->groups()
            ->withPivot('role')
            ->withCount('pins')
            ->with('users')
            ->orderBy('name')
            ->get();

        $totalGroups = $groups->count();

        // Total de mensagens dos grupos onde o utilizador pertence
        $groupIds = $groups->pluck('id');
        $totalMessages = GroupMessage::whereIn('group_id', $groupIds)->count();

        // Eventos ativos e futuros
        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $eventsQuery = Event::query()
            ->where(function ($w) use ($today, $nowTime) {
                $w->whereDate('event_date', '>', $today)
                    ->orWhere(function ($w2) use ($today, $nowTime) {
                        $w2->whereDate('event_date', '=', $today)
                            ->where(function ($w3) use ($nowTime) {
                                $w3->whereNull('event_time')
                                    ->orWhere('event_time', '>=', $nowTime);
                            });
                    });
            });

        if (Schema::hasColumn('events', 'is_active')) {
            $eventsQuery->where('is_active', true);
        }

        $totalEvents = (clone $eventsQuery)->count();

        $upcomingEvents = (clone $eventsQuery)
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->take(4)
            ->get();

        // Pins recentes do utilizador
        $recentPins = Pin::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Activity chart — Pins por mês (últimos 6 meses)
        |--------------------------------------------------------------------------
        */
        $monthsBack = 5;
        $startMonth = now()->startOfMonth()->subMonths($monthsBack);
        $endMonth = now()->endOfMonth();

        $pinsByMonthRaw = Pin::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startMonth, $endMonth])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $activityPoints = [];
        $maxValue = 0;
        $totalInPeriod = 0;

        for ($i = 0; $i <= $monthsBack; $i++) {
            $monthDate = now()->startOfMonth()->subMonths($monthsBack - $i);
            $key = $monthDate->format('Y-m');

            $value = (int) ($pinsByMonthRaw[$key] ?? 0);
            $maxValue = max($maxValue, $value);
            $totalInPeriod += $value;

            $activityPoints[] = [
                'key' => $key,
                'label' => ucfirst($monthDate->translatedFormat('M')),
                'full_label' => ucfirst($monthDate->translatedFormat('F Y')),
                'value' => $value,
            ];
        }

        $activityPoints = collect($activityPoints)->map(function ($point) use ($maxValue) {
            $point['height'] = $maxValue > 0
                ? max(10, (int) round(($point['value'] / $maxValue) * 100))
                : 10;

            return $point;
        })->values();

        $activityChart = [
            'points' => $activityPoints,
            'max' => $maxValue,
            'total' => $totalInPeriod,
            'range_label' => 'Últimos 6 meses',
        ];

        return view('dashboard', compact(
            'totalPins',
            'totalGroups',
            'totalMessages',
            'totalEvents',
            'recentPins',
            'groups',
            'upcomingEvents',
            'activityChart'
        ));
    }
}