<main class="mx-auto flex min-h-screen w-full max-w-3xl flex-col justify-center gap-6 px-4 py-10 sm:px-6">
    <a href="{{ route('home') }}" class="w-fit text-sm font-semibold text-emerald-800 hover:text-emerald-900">
        ← Back to Discover
    </a>

    <header>
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">FruitMatch</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900 sm:text-4xl">Add a fruit</h1>
        <p class="mt-2 text-stone-600">
            Add nutrition per 100 g. The recommender will estimate how well this fruit matches what you have liked or passed on.
        </p>
    </header>

    <form wire:submit="save" class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm sm:p-8">
        <div>
            <label for="fruit-name" class="text-sm font-semibold text-stone-800">Fruit name</label>
            <input id="fruit-name" type="text" wire:model="name" maxlength="40" required
                class="mt-1 w-full rounded-xl border-stone-300 bg-white text-stone-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
            @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-stone-900">Nutrition per 100 g</h2>
            <flux:button type="button" wire:click="randomizeNutrition" wire:loading.attr="disabled" wire:target="randomizeNutrition">
                Randomize nutrition
            </flux:button>
        </div>
        <p class="mt-1 text-sm text-stone-500">
            Randomize fills each field with a value between the lowest and highest value for that nutrient in the fruit catalog. You can edit the values before saving.
        </p>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="fruit-energy" class="text-sm font-medium text-stone-700">Energy (kcal)</label>
                <input id="fruit-energy" type="number" min="0" max="1000" step="1" wire:model="energy" required
                    class="mt-1 w-full rounded-xl border-stone-300 bg-white text-stone-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
                @error('energy') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="fruit-protein" class="text-sm font-medium text-stone-700">Protein (g)</label>
                <input id="fruit-protein" type="number" min="0" max="100" step="0.01" wire:model="protein" required
                    class="mt-1 w-full rounded-xl border-stone-300 bg-white text-stone-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
                @error('protein') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="fruit-carbohydrates" class="text-sm font-medium text-stone-700">Carbohydrates (g)</label>
                <input id="fruit-carbohydrates" type="number" min="0" max="100" step="0.01" wire:model="carbohydrates" required
                    class="mt-1 w-full rounded-xl border-stone-300 bg-white text-stone-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
                @error('carbohydrates') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="fruit-fat" class="text-sm font-medium text-stone-700">Fat (g)</label>
                <input id="fruit-fat" type="number" min="0" max="100" step="0.01" wire:model="fat" required
                    class="mt-1 w-full rounded-xl border-stone-300 bg-white text-stone-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
                @error('fat') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="fruit-sugars" class="text-sm font-medium text-stone-700">Sugars (g)</label>
                <input id="fruit-sugars" type="number" min="0" max="100" step="0.01" wire:model="sugars" required
                    class="mt-1 w-full rounded-xl border-stone-300 bg-white text-stone-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
                @error('sugars') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="fruit-salt" class="text-sm font-medium text-stone-700">Salt (g)</label>
                <input id="fruit-salt" type="number" min="0" max="10" step="0.001" wire:model="salt" required
                    class="mt-1 w-full rounded-xl border-stone-300 bg-white text-stone-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" />
                @error('salt') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-end gap-3">
            <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center rounded-xl px-4 text-sm font-semibold text-stone-700 hover:bg-stone-100">
                Cancel
            </a>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                Add fruit and get its prediction
            </flux:button>
        </div>
    </form>
</main>
