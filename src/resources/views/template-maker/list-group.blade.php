<a onclick="
    $('#collapse2_{{ $id }}').on('hide.bs.collapse', function () {
        $('#chevron_{{ $id }}').removeClass('fa-chevron-down text-success')
        $('#chevron_{{ $id }}').addClass('fa-chevron-right')
    }); 
    $('#collapse2_{{ $id }}').on('show.bs.collapse', function () {
        $('#chevron_{{ $id }}').addClass('fa-chevron-down text-success')
        $('#chevron_{{ $id }}').removeClass('fa-chevron-right')
    });
    "href="#collapse2_{{$id}}" data-toggle="collapse">
    <div class="card mb-3">
        <div class="card-header py-2 px-3">
            {{ $title }}
            <i id="chevron_{{ $id }}" class="float-right fas fa-chevron-right"></i>
        </div>
        <div class="collapse" id="collapse2_{{ $id }}">
            <div class="card-body py-2 px-3">
                <div class="list-group">
                    {!! $contents !!}
                </div>
            </div>
        </div>
    </div>
</a>