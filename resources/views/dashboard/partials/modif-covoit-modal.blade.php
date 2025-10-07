<!-- Pop-up pour modifier un covoit-->
<div id="modif-covoit-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden" 
    role="dialog" aria-modal="true" aria-labelledby="modifCovoitModalTitle" onclick="closeModal('modif-covoit-modal')">
    <div class="bg-white rounded-lg p-8 max-w-3xl w-full mx-4 overflow-y-auto max-h-screen"
        onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 id="modifCovoitModalTitle" class="text-2xl font-bold text-gray-800">Modifier le covoiturage</h2>
            <button onclick="closeModal('modif-covoit-modal')"
                class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>
        <p class="text-gray-600 mb-4">Veuillez modifier les champs que vous souhaitez mettre à jour.</p>
        <p class="text-sm text-red-600 mb-6">Tous les champs ayant un astérisque (*) sont OBLIGATOIRES !</p>

        <!-- Body -->
        <form id="modifCovoitForm" action="" method="POST" data-action-base="{{ route('covoiturages.update', ['covoiturage' => '__COVOITURAGE_ID__']) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" id="covoiturage_id" name="covoiturage_id">
            @include('dashboard.partials._covoit-modal-form', ['prefix' => 'modif'])
        </form>
    </div>
</div>
    