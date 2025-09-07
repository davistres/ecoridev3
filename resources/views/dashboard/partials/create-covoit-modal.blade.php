<!-- Pop-up pour créer un covoit-->
<div id="create-covoit-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden"
    onclick="closeModal('create-covoit-modal')">
    <div class="bg-white rounded-lg p-8 max-w-3xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Proposer un covoiturage</h2>
            <button onclick="closeModal('create-covoit-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>
        <p class="text-gray-600 mb-4">Veuillez remplir tous les champs correctement en respectant les indications.</p>
        <p class="text-sm text-red-600 mb-6">Tous les champs ayant un astérisque (*) sont OBLIGATOIRES !</p>

        <!-- Body -->
        <form id="createCovoitForm" action="{{ route('covoiturages.store') }}" method="POST">
            @include('dashboard.partials._covoit-modal-form', ['prefix' => 'create'])
        </form>
    </div>
</div>


