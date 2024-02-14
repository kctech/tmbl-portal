@props(['id'=>Str::random(5),'editorheight'=>null])
<div x-data="{textEditor:@entangle($attributes->wire('model')).defer}"
     x-init="()=>{setEditor{{$id}}Value(textEditor)}"
     @reseteditor{{$id}}.window="setEditorValue('');"
     wire:ignore>
    <input x-ref="editor{{$id}}"
        id="editor-x{{$id}}"
        type="hidden"
        name="content">
    <trix-editor
        id="editor{{$id}}"
        input="editor-x{{$id}}"
        x-on:trix-change="textEditor=$refs.editor{{$id}}.value;"
    ></trix-editor>
</div>

{{--
@once
@push('scripts_head')
    <script type="text/javascript" src="/js/trix.js"></script>
@endpush
@endonce
--}}

@push('scripts')
    <script>
        function setEditor{{$id}}Value(value) {
            let element{{$id}}= document.getElementById("editor{{$id}}");
            let input = document.getElementById("editor-x{{$id}}");
            if(value=='') {
                input.value = "";
                element.innerHTML = "";
            }
            else {
                element{{$id}}.editor.insertHTML(value);
            }
        }
        (function() {
            addEventListener("trix-initialize", function(e) {
                var hideTrixElements = ['heading1', 'code', 'quote'];
                hideTrixElements.forEach(function myFunction(trix_attr) {
                    var ele = document.querySelector('[data-trix-attribute="'+trix_attr+'"]');
                    if(ele){
                        ele.remove();
                    }
                });

                var ele = document.querySelector(".trix-button-group--file-tools");
                if(ele){
                    ele.remove();
                }
            });
            addEventListener("trix-file-accept", function(e) {
                e.preventDefault();
            })
        })();
    </script>
@endpush

@push('css')
    {{--<link rel="stylesheet" type="text/css" href="/css/trix.css">--}}
    <style>
     .trix-button-group--file-tools{
         display: none!important;
     }
     .trix-button--icon-decrease-nesting-level{
         display: none;
     }
     .trix-button--icon-increase-nesting-level{
         display: none;
     }
     #editor{{$id}}
     {
         height: {{$editorheight ?? '10rem'}};
     }
    </style>
@endpush