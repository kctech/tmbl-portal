<div>
    @if(!empty($redirect))
        @php
            session()->reflash();
            redirect()->route($redirect);
        @endphp
    @endif
</div>
