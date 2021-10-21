<div class="list-group-item">
    <div class="small text-muted">
        <a
        onclick="
        $('#collapse_{{str_replace('.','_',$key)}}').on('show.bs.collapse',function(event) {
            event.stopPropagation();
        });
        $('#collapse_{{str_replace('.','_',$key)}}').on('hide.bs.collapse',function(event) {
            event.stopPropagation();
        });
        "         
        href="#collapse_{{str_replace('.','_',$key)}}" data-toggle="collapse">
            @if (is_array($label))
               @dump($label) 
            @else
                {{ $label }}
            @endif
        </a>
    </div>
    <div class="collapse" id="collapse_{{str_replace('.','_',$key)}}"> {!! $content !!} </div>
</div>