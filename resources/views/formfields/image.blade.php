@if (isset($options->cropper) && !empty($options->cropper))
    @include('admin::formfields.custom.cropper')
@else
    @if(isset($dataTypeContent->{$row->field}))
        <img src="@if( !filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL)){{ Admin::image( $dataTypeContent->{$row->field} ) }}@else{{ $dataTypeContent->{$row->field} }}@endif"
             style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;">
    @endif
    <input @if($row->required == 1 && !isset($dataTypeContent->{$row->field})) required @endif type="file" data-name="{{ $row->display_name }}"  name="{{ $row->field }}">
@endif
