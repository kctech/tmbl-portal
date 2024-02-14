<div wire:ignore>
    <div class="d-flex flex-row justify-content-between align-items-center">
        <div class="flex-grow-1">
            <select
                x-data
                x-ref="select"
                x-init="
                    jQuery($refs.select).select2({
                        theme:'bootstrap4',
                        minimumResultsForSearch:10,
                        width:'100%',
                        tags: true,
                        tokenSeparators: [','],
                        placeholder: '{{$placeholder ?? 'Select or type an option'}}'
                    });
                    jQuery($refs.select).on('change', function (e) {
                        let elementId = $($refs.select).prop('id');
                        var data = $($refs.select).val();
                        @this.set(elementId,data);
                        jQuery($refs.select).parent().parent().find('.counter').html(data.length);
                    });"
                {{ $attributes }}
                multiple
            >
                {{$slot}}
            </select>
        </div>
        <div class="px-2" style="min-width: fit-content;">
            <i class="fa fa-badge-check"></i>&nbsp;<span class="counter">0</span>
        </div>
    </div>
</div>
