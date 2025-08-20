<!-- Block Mes véhicules -->
<div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl">
    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
        <h3 class="text-xl font-bold text-[#2c3e50]">Mes véhicules</h3>
        <button onclick="openModal('add-vehicle-modal')"
            class="h-8 w-8 rounded-full bg-[#2ecc71] hover:bg-[#27ae60] text-white flex items-center justify-center transition-transform duration-300 hover:scale-110">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <div class="p-6">
        @if ($voitures->isEmpty())
            <p class="text-center text-slate-500">Aucun véhicule enregistré.</p>
        @else
            <div class="space-y-4">
                @foreach ($voitures as $voiture)
                    <div class="border rounded-lg p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center">
                        <!-- Infos en grid -->
                        <div
                            class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-4 text-slate-800">
                            <p class="font-semibold">{{ $voiture->brand }}</p>
                            <p class="font-semibold">{{ $voiture->model }}</p>
                            <p class="font-semibold">{{ $voiture->immat }}</p>
                            <p class="flex items-center">
                                <i class="fas fa-calendar-alt fa-fw mr-2 text-slate-400"></i>
                                {{ \Carbon\Carbon::parse($voiture->date_first_immat)->format('d/m/Y') }}
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-palette fa-fw mr-2 text-slate-400"></i>
                                {{ $voiture->color }}
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-users fa-fw mr-2 text-slate-400"></i>
                                {{ $voiture->n_place }} places
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-gas-pump fa-fw mr-2 text-slate-400"></i>
                                {{ $voiture->energie }}
                            </p>
                        </div>
                        <!-- Btn -->
                        <div class="flex space-x-2 mt-4 sm:mt-0 sm:ml-4 flex-shrink-0 self-start">
                            <button @click="openEditVehicleModal({{ json_encode($voiture) }})"
                                class="h-8 w-8 rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form id="delete-form-{{ $voiture->voiture_id }}"
                                action="{{ route('voitures.destroy', $voiture) }}" method="POST"
                                onsubmit="return confirmVehicleDeletion(event, {{ $voitures->count() }})">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="h-8 w-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
