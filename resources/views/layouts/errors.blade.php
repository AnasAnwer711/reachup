@if($errors->any())
  {!! implode('', $errors->all('<div class="alert alert-danger "><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>:message</div>')) !!}
@endif