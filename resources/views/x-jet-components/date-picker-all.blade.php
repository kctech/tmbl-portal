<input
    x-data
    x-ref="input"
    x-init="new Pikaday({
        field: $refs.input,
        bound:true,
        format: 'DD MMMM YYYY',
        onSelect: function (e){ 
            @this.set(
                $($refs.input).prop('id'),
                this.toString()
            );
        }
    })"
    type="text"
    readonly
    wire:ignore
    {{ $attributes }}
>
