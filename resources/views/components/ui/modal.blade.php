<div target="{{ $target }}" class="absolute game_modal-holder hidden">
    <div target="{{ $target }}" class="game_modal-overlay fixed top-0 h-screen w-screen bg-gray-700 opacity-80">
    </div>
    <div target="{{ $target }}" class="game_modal-content fixed flex flex-col w-full h-full justify-center">
        <div class="flex flex-row w-full justify-center">
            <div class="w-11/12 bg-gray-200 mt-4 shadow-md">
                <div class="game_modal-header bg-gray-300 w-full px-4 py-4 text-center">
                </div>
                <div class="game_modal-body w-full px-4 py-4">
                </div>
                <div id="game_modal-footer" class="bg-gray-300 w-full px-4 py-4">
                    <x-btn-primary isALink="{{ false }}" type="button" onClick="GameApi.closeModal('{{ $target }}')">Close</x-btn-primary>
                </div>
            </div>
        </div>
    </div>
</div>