@if (\Session::has('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {!! \Session::get('success') !!}
    </div>
@endif
