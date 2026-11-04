<div target="{{ $target }}" class="game_modal-holder hidden fixed inset-0 z-[60]">
    {{-- Dimmed backdrop --}}
    <div target="{{ $target }}" class="game_modal-overlay absolute inset-0 bg-main-dark/80"></div>

    {{-- Centred window; clicking the backdrop padding closes it --}}
    <div target="{{ $target }}" class="game_modal-content absolute inset-0 flex flex-col justify-center items-center p-4 overflow-y-auto"
         onclick="if (event.target === this) GameApi.closeModal('{{ $target }}')">
        <div class="w-full max-w-xl my-auto bg-main-light border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
            {{-- Title bar: traffic lights + title + close --}}
            <div class="flex items-center gap-2.5 bg-main-dark px-4 py-3">
                <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                <span class="game_modal-header ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm truncate"></span>
                <button type="button" onclick="GameApi.closeModal('{{ $target }}')" aria-label="Close"
                        class="ml-auto shrink-0 text-white/70 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                    </svg>
                </button>
            </div>

            <div class="game_modal-body w-full p-5 md:p-6"></div>

            <div id="game_modal-footer" class="border-t-4 border-main-dark px-5 py-4 flex justify-end">
                <button type="button" onclick="GameApi.closeModal('{{ $target }}')"
                        class="fira-sans font-black uppercase tracking-wider text-sm bg-white text-main-dark border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Close</button>
            </div>
        </div>
    </div>
</div>
