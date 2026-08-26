<div>
    @if ($this->count > 0)
        <span
            class="absolute bg-red-600 text-white rounded-full text-[10px] font-bold h-4 w-4 flex items-center justify-center shadow"
            style="top: -5px; right: -5px;">
            {{ $this->count }}
        </span>
    @endif
</div>
