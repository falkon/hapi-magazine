<img src="@if( !filter_var($dataTypeContent->{$dataType->field}, FILTER_VALIDATE_URL)){{ Admin::image( $dataTypeContent->{$dataType->field} ) }}@else{{ $dataTypeContent->{$dataType->field} }}@endif" style="width:100px">