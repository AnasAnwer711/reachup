@if (\Session::has('error'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {!! \Session::get('error') !!}
    </div>
@endif
