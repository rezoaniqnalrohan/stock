@props(['message' => 'Nothing here yet.'])
<div class="py-12 text-center">
    <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-100 grid place-items-center mb-3">
        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.5a1.5 1.5 0 00-1.4 1 2 2 0 01-3.8 0 1.5 1.5 0 00-1.4-1H4"/></svg>
    </div>
    <p class="text-sm text-slate-500">{{ $message }}</p>
    @if (isset($action))<div class="mt-4">{{ $action }}</div>@endif
</div>
