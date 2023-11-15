<div id="notification-box__success" class="hidden fixed w-full justify-center bottom-0 flex flex-row mb-4 md:mb-6">
    <div class="bg-lime-500 text-gray-100 flex flex-row w-5/6 md:w-3/6 py-2 px-2 rounded shadow-md justify-between">
        <div class="text-xl font-semibold fira-sans border-r-2 border-r-slate-200 px-2 flex flex-col justify-center">
            SUCCESS
        </div>
        <div id="notification-box__success__message"  class="text-xl font-semibold fira-sans px-2">
        </div>
        <div class="cursor-pointer" onclick="GameApi.removeAlertNotification('success')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
    </div>
</div>