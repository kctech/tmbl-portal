<input
    x-data
    x-ref="input"
    x-init="new Pikaday({
        field: $refs.input,
        bound:true,
        format: 'DD-MM-YYYY',
        maxDate:new Date(),
        defaultDate: moment().subtract(35, 'years').toDate(),
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
