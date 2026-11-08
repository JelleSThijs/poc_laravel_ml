<section class="mx-auto flex min-h-screen w-full max-w-5xl flex-col items-center justify-center gap-6 px-4 py-10 sm:px-6">
    <header class="w-full text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">FruitMatch</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">Find your favourite fruit</h1>
        <p class="mx-auto mt-2 max-w-xl text-stone-600">Like or pass on each fruit. Your choices teach the recommender what you enjoy.</p>
        <p class="mt-4 text-sm text-stone-500" aria-live="polite">
            <span class="font-semibold text-stone-800">{{ $this->ratedCount }}</span> choices so far
        </p>
    </header>

    @if (session('status'))
        <p class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900" role="status">
            {{ session('status') }}
        </p>
    @endif

    @if ($lastAction)
        <p class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900" role="status">
            {{ $lastAction === 'like' ? 'You liked' : 'You passed on' }} {{ $lastFoodName }}. Your recommender learned from that.
        </p>
    @endif

    @if ($food)
        <article
            wire:key="fruit-{{ $food->id }}"
            x-data="{ startX: 0, deltaX: 0, dragging: false, finish() { if (Math.abs(this.deltaX) > 80) { this.deltaX > 0 ? $wire.like() : $wire.pass() } this.deltaX = 0; this.dragging = false } }"
            @pointerdown="startX = $event.clientX; deltaX = 0; dragging = true; $event.currentTarget.setPointerCapture($event.pointerId)"
            @pointermove="if (dragging) deltaX = $event.clientX - startX"
            @pointerup="finish()"
            @pointercancel="deltaX = 0; dragging = false"
            x-bind:style="'transform: translateX(' + deltaX + 'px) rotate(' + deltaX / 28 + 'deg); transition: ' + (dragging ? 'none' : 'transform 220ms ease') + ';'"
            class="w-full max-w-md touch-pan-y select-none overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-xl"
        >
            <div class="relative aspect-[4/3] overflow-hidden bg-emerald-50">
                @if ($food->image_path)
                    <img
                        src="{{ asset($food->image_path) }}"
                        alt="{{ $food->name }}"
                        class="h-full w-full object-cover"
                        loading="eager"
                    />
                @else
                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-emerald-50 to-amber-50">
                        <span class="text-8xl" aria-hidden="true">🍊</span>
                    </div>
                @endif
                <span class="absolute left-5 top-5 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-800 shadow-sm">
                    Fresh fruit
                </span>
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/75 to-transparent px-6 pb-5 pt-16">
                    <h2 class="text-3xl font-bold tracking-tight text-white">{{ $food->name }}</h2>
                </div>
                <div wire:loading.flex wire:target="like,pass" class="absolute inset-0 items-center justify-center bg-white/75 backdrop-blur-sm">
                    <span class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-800 shadow">Learning your taste…</span>
                </div>
            </div>

            <div class="p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Nutrition per 100 g</p>
                <dl class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    <div class="rounded-xl bg-stone-50 px-3 py-2">
                        <dt class="text-xs text-stone-500">Energy</dt>
                        <dd class="font-semibold text-stone-900">{{ $food->energy }} kcal</dd>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-3 py-2">
                        <dt class="text-xs text-stone-500">Carbohydrates</dt>
                        <dd class="font-semibold text-stone-900">{{ $food->carbohydrates }} g</dd>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-3 py-2">
                        <dt class="text-xs text-stone-500">Sugars</dt>
                        <dd class="font-semibold text-stone-900">{{ $food->sugars }} g</dd>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-3 py-2">
                        <dt class="text-xs text-stone-500">Protein</dt>
                        <dd class="font-semibold text-stone-900">{{ $food->protein }} g</dd>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-3 py-2">
                        <dt class="text-xs text-stone-500">Fat</dt>
                        <dd class="font-semibold text-stone-900">{{ $food->fat }} g</dd>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-3 py-2">
                        <dt class="text-xs text-stone-500">Salt</dt>
                        <dd class="font-semibold text-stone-900">{{ $food->salt }} g</dd>
                    </div>
                </dl>
            </div>
        </article>

        <div class="flex w-full max-w-md items-center justify-center gap-4">
            <flux:button type="button" wire:click="pass" wire:loading.attr="disabled" wire:target="like,pass" aria-label="Pass on {{ $food->name }}" class="min-h-12 flex-1">
                Pass
            </flux:button>
            <flux:button type="button" wire:click="like" wire:loading.attr="disabled" wire:target="like,pass" aria-label="Like {{ $food->name }}" class="min-h-12 flex-1">
                Like it
            </flux:button>
        </div>
        <p class="text-center text-xs text-stone-500">Swipe right to like · swipe left to pass. Fruits can repeat, and frequent recommendations are gently reduced.</p>

        @if ($mlAvailable)
            <section class="w-full max-w-md rounded-2xl border border-stone-200 bg-white p-5 shadow-sm" aria-labelledby="recommendation-likelihoods">
                <h2 id="recommendation-likelihoods" class="text-lg font-semibold text-stone-900">Selection chances for this card</h2>
                <p class="mt-1 text-xs leading-5 text-stone-500">
                    These chances were used to select the card shown above. Your like or pass updates the scores for the following card.
                </p>
                <ul class="mt-4 space-y-4">
                    @foreach ($recommendationScores as $score)
                        @if ($score['recommendation'] !== null)
                            <li>
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="font-medium text-stone-800">{{ $score['name'] }}</span>
                                    <span class="font-semibold tabular-nums text-emerald-800">
                                        {{ number_format($score['recommendation'] * 100, 1) }}%
                                    </span>
                                </div>
                                <div class="mt-1 h-2 overflow-hidden rounded-full bg-stone-100">
                                    <div class="h-full rounded-full bg-emerald-600" style="width: {{ number_format($score['recommendation'] * 100, 2, '.', '') }}%"></div>
                                </div>
                                <p class="mt-1 text-xs text-stone-500">
                                    Taste estimate: {{ number_format($score['like'] * 100, 0) }}% likely to suit you
                                </p>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <p class="mt-4 text-xs leading-5 text-stone-500">
                    These are relative selection probabilities across the current fruit catalog. Adding a fruit changes the distribution.
                </p>
            </section>
        @elseif ($food)
            <p class="max-w-md text-center text-sm text-amber-800">
                Recommendation likelihoods are unavailable. Make sure the Python recommender service is running.
            </p>
        @endif
    @else
        <div class="w-full max-w-md rounded-[2rem] border border-emerald-100 bg-white px-6 py-12 text-center shadow-sm">
            <span class="text-5xl" aria-hidden="true">🍓</span>
            <h2 class="mt-4 text-2xl font-bold text-stone-900">No fruit is available right now</h2>
            <p class="mt-2 text-stone-600">Add fruit to the catalog and it will appear here.</p>
        </div>
    @endif

    <p class="max-w-md text-center text-xs leading-5 text-stone-500">
        The Python recommender learns from every like or pass. It can repeat a favourite while reducing recommendations that have appeared often.
    </p>
</section>
