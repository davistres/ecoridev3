<!-- Block du dashboard: rôle -->
<div
    class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl h-full flex flex-col">
    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
        <h3 class="text-xl font-bold text-[#2c3e50]">Mon rôle</h3>
    </div>
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <p class="text-slate-600 text-lg">Rôle actuel :</p>
            <span
                class="px-4 py-1 text-base font-semibold text-white bg-[#3498db] rounded-full">{{ $user->role }}</span>
        </div>

        <form action="{{ route('profile.role.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-2">
                <label for="role_passager" class="flex items-center">
                    <input type="radio" name="role" value="Passager"
                        class="h-5 w-5 text-[#2ecc71] focus:ring-[#27ae60]"
                        {{ $user->role == 'Passager' ? 'checked' : '' }}>
                    <span class="ml-3 text-slate-700">Passager</span>
                </label>
                <label for="role_conducteur" class="flex items-center">
                    <input type="radio" name="role" value="Conducteur"
                        class="h-5 w-5 text-[#2ecc71] focus:ring-[#27ae60]"
                        {{ $user->role == 'Conducteur' ? 'checked' : '' }}>
                    <span class="ml-3 text-slate-700">Conducteur</span>
                </label>
                <label for="role_les_deux" class="flex items-center">
                    <input type="radio" name="role" value="Les deux"
                        class="h-5 w-5 text-[#2ecc71] focus:ring-[#27ae60]"
                        {{ $user->role == 'Les deux' ? 'checked' : '' }}>
                    <span class="ml-3 text-slate-700">Les deux</span>
                </label>
            </div>
            <button type="submit"
                class="mt-4 w-full px-5 py-2 bg-[#2ecc71] text-white font-semibold rounded-md hover:bg-[#27ae60] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transition-all duration-300 transform hover:scale-105">
                Valider
            </button>
        </form>
    </div>
</div>
