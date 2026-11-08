<main class="mx-auto min-h-screen w-full max-w-5xl px-4 py-10 sm:px-6">
    <header class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Your taste profile</p>
        <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">My preferences</h1>
                <p class="mt-2 text-stone-600">
                    Predicted chance that you will like each fruit, based on your choices.
                </p>
            </div>
            @if ($mlAvailable)
                <span class="rounded-full bg-stone-100 px-3 py-1 text-sm font-medium text-stone-700">
                    {{ $feedbackCount }} {{ \Illuminate\Support\Str::plural('response', $feedbackCount) }} learned
                </span>
            @endif
        </div>
    </header>

    @if (! $mlAvailable)
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-950" role="status">
            <h2 class="font-semibold">Preference estimates are unavailable</h2>
            <p class="mt-1 text-sm">
                Start the Python recommender service, then refresh this page to see your fruit estimates.
            </p>
            <flux:button type="button" wire:click="refreshPreferences" class="mt-4">
                Refresh estimates
            </flux:button>
        </section>
    @elseif ($feedbackCount === 0)
        <p class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-950">
            These are neutral starting estimates. Like or pass on fruit to teach the recommender your preferences.
        </p>
    @elseif (! $trained)
        <p class="mb-5 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-950">
            The PPO model has not completed training yet, so these estimates use your saved feedback and fruit similarity.
        </p>
    @endif

    @if ($fruits === [])
        <section class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
            <h2 class="text-xl font-semibold text-stone-900">No fruit in the catalog yet</h2>
            <p class="mt-2 text-stone-600">Add a fruit to start building your preference list.</p>
            <a href="{{ route('fruits.create') }}" class="mt-5 inline-flex min-h-11 items-center rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800">
                New fruit
            </a>
        </section>
    @else
        <ul class="grid gap-4 sm:grid-cols-2">
            @foreach ($fruits as $fruit)
                <li class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold text-stone-900">{{ $fruit['name'] }}</h2>
                        @if ($mlAvailable && $fruit['like'] !== null)
                            <span class="text-lg font-bold tabular-nums text-emerald-800">
                                {{ number_format($fruit['like'] * 100, 1) }}%
                            </span>
                        @else
                            <span class="text-sm text-stone-400">—</span>
                        @endif
                    </div>
                    @if ($mlAvailable && $fruit['like'] !== null)
                        <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-stone-100" role="meter" aria-label="{{ $fruit['name'] }} predicted like chance" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ number_format($fruit['like'] * 100, 1, '.', '') }}">
                            <div class="h-full rounded-full bg-emerald-600 transition-all" style="width: {{ number_format($fruit['like'] * 100, 2, '.', '') }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-stone-500">Predicted like chance</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <p class="mt-6 text-xs leading-5 text-stone-500">
        Estimates are personalized to this browser session. They reflect the current model and can change as you rate more fruit.
    </p>
</main>
