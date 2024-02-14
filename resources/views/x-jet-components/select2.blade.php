<div wire:ignore>
    <select
        x-data
        x-ref="select"
        x-init="jQuery($refs.select).select2({width:'100%',theme:'bootstrap4',minimumResultsForSearch:14});jQuery($refs.select).on('select2:select', function (e) {  @this.set(jQuery($refs.select).prop('id'),e.params.data.id);});"
        {{ $attributes }}
    >
    {{$slot}}
    </select>
</div>
