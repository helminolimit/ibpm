<?php

namespace App\Providers;

use App\Events\AduanBaru;
use App\Events\AduanDihantar;
use App\Events\AduanDitugaskan;
use App\Events\AduanSelesai;
use App\Events\StatusDikemaskini;
use App\Listeners\HantarEmailAduanDitugaskan;
use App\Listeners\HantarEmailAduanSelesai;
use App\Listeners\HantarEmailNotifikasiAdmin;
use App\Listeners\HantarEmailPengesahan;
use App\Listeners\HantarEmailStatusKemaskini;
use App\Models\AduanIct;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerEventListeners();
        $this->registerViewComposers();
    }

    protected function registerViewComposers(): void
    {
        View::composer('landing', function ($view) {
            $view->with([
                'stats' => Cache::remember(
                    'landing.stats',
                    now()->addMinutes(15),
                    fn () => [
                        'aduan_resolved' => number_format(AduanIct::where('status', 'SELESAI')->count()),
                        'sla_pct' => '98',
                        'active_users' => '1.4k',
                    ]
                ),
                'announcements' => [],
            ]);
        });
    }

    protected function registerEventListeners(): void
    {
        Event::listen(AduanDihantar::class, HantarEmailPengesahan::class);
        Event::listen(AduanBaru::class, HantarEmailNotifikasiAdmin::class);
        Event::listen(StatusDikemaskini::class, HantarEmailStatusKemaskini::class);
        Event::listen(AduanDitugaskan::class, HantarEmailAduanDitugaskan::class);
        Event::listen(AduanSelesai::class, HantarEmailAduanSelesai::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
