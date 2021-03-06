@php $relationshipDetails = json_decode($relationship['details']); @endphp
<div class="row row-dd row-dd-relationship">
    <div class="col-xs-2">
        <h4><i class="admin-heart"></i><strong>{{ $relationship->display_name }}</strong></h4>
        <div class="handler admin-handle"></div>
        <strong>{{ __('admin.database.type') }}:</strong> <span>{{ __('admin.database.relationship.relationship') }}</span>
        <div class="handler admin-handle"></div>
        <input class="row_order" type="hidden" value="{{ $relationship['order'] }}" name="field_order_{{ $relationship['field'] }}">
    </div>
    <div class="col-xs-2">
        <input type="checkbox" name="field_browse_{{ $relationship['field'] }}" @if(isset($relationship->browse) && $relationship->browse){{ 'checked="checked"' }}@elseif(!isset($relationship->browse)){{ 'checked="checked"' }}@endif>
        <label for="field_browse_{{ $relationship['field'] }}"> {{ __('admin.database.relationship.browse') }}</label><br>
        <input type="checkbox" name="field_read_{{ $relationship['field'] }}" @if(isset($relationship->read) && $relationship->read){{ 'checked="checked"' }}@elseif(!isset($relationship->read)){{ 'checked="checked"' }}@endif>
        <label for="field_read_{{ $relationship['field'] }}"> {{ __('admin.database.relationship.read') }}</label><br>
        <input type="checkbox" name="field_edit_{{ $relationship['field'] }}" @if(isset($relationship->edit) && $relationship->edit){{ 'checked="checked"' }}@elseif(!isset($relationship->edit)){{ 'checked="checked"' }}@endif>
        <label for="field_edit_{{ $relationship['field'] }}"> {{ __('admin.database.relationship.edit') }}</label><br>
        <input type="checkbox" name="field_add_{{ $relationship['field'] }}" @if(isset($relationship->add) && $relationship->add){{ 'checked="checked"' }}@elseif(!isset($relationship->add)){{ 'checked="checked"' }}@endif>
        <label for="field_add_{{ $relationship['field'] }}"> {{ __('admin.database.relationship.add') }}</label><br>
        <input type="checkbox" name="field_delete_{{ $relationship['field'] }}" @if(isset($relationship->delete) && $relationship->delete){{ 'checked="checked"' }}@elseif(!isset($relationship->delete)){{ 'checked="checked"' }}@endif>
        <label for="field_delete_{{ $relationship['field'] }}"> {{ __('admin.database.relationship.delete') }}</label><br>
    </div>
    <div class="col-xs-2">
        <p>{{ __('admin.database.relationship.relationship') }}</p>
    </div>
    <div class="col-xs-2">
        <input type="text" name="field_display_name_{{ $relationship['field'] }}" class="form-control relationship_display_name" value="{{ $relationship['display_name'] }}">
    </div>
    <div class="col-xs-4">
        <div class="admin-relationship-details-btn">
            <i class="admin-angle-down"></i><i class="admin-angle-up"></i> 
            <span class="open_text">{{ __('admin.database.relationship.open') }}</span>
            <span class="close_text">{{ __('admin.database.relationship.close') }}</span> 
            {{ __('admin.database.relationship.relationship_details') }}
        </div>
    </div>
    <div class="col-md-12 admin-relationship-details">
        <a href="{{ route('admin.database.crud.delete_relationship', $relationship['id']) }}" class="delete_relationship"><i class="admin-trash"></i> {{ __('admin.database.relationship.delete') }}</a>
        <div class="relationship_details_content">
            <p class="relationship_table_select">{{ str_singular(ucfirst($table)) }}</p>
            <select class="relationship_type select2" name="relationship_type_{{ $relationship['field'] }}">
                <option value="hasOne" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasOne'){{ 'selected="selected"' }}@endif>{{ __('admin.database.relationship.has_one') }}</option>
                <option value="hasMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'hasMany'){{ 'selected="selected"' }}@endif>{{ __('admin.database.relationship.has_many') }}</option>
                <option value="belongsTo" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsTo'){{ 'selected="selected"' }}@endif>{{ __('admin.database.relationship.belongs_to') }}</option>
                <option value="belongsToMany" @if(isset($relationshipDetails->type) && $relationshipDetails->type == 'belongsToMany'){{ 'selected="selected"' }}@endif>{{ __('admin.database.relationship.belongs_to_many') }}</option>
            </select>
            <select class="relationship_table select2" name="relationship_table_{{ $relationship['field'] }}">
                @foreach($tables as $table)
                    <option value="{{ $table }}" @if(isset($relationshipDetails->table) && $relationshipDetails->table == $table){{ 'selected="selected"' }}@endif>{{ ucfirst($table) }}</option>
                @endforeach
            </select>
            <span><input type="text" class="form-control" name="relationship_model_{{ $relationship['field'] }}" placeholder="{{ __('admin.database.relationship.namespace') }}" value="@if(isset($relationshipDetails->model)){{ $relationshipDetails->model }}@endif"></span>
        </div>
        <div class="relationshipField">
            <div class="relationship_details_content margin_top belongsTo @if($relationshipDetails->type == 'belongsTo'){{ 'flexed' }}@endif">
                <label>{{ __('admin.database.relationship.which_column_from') }} <span>{{ str_singular(ucfirst($table)) }}</span> {{ __('admin.database.relationship.is_used_to_reference') }} <span class="label_table_name"></span>?</label>
                <select name="relationship_column_belongs_to_{{ $relationship['field'] }}" class="new_relationship_field select2">
                    @foreach($fieldOptions as $data)
                        <option value="{{ $data['field'] }}" @if($relationshipDetails->column == $data['field']){{ 'selected="selected"' }}@endif>{{ $data['field'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relationship_details_content margin_top hasOneMany @if($relationshipDetails->type == 'hasOne' || $relationshipDetails->type == 'hasMany'){{ 'flexed' }}@endif">
                <label>{{ __('admin.database.relationship.which_column_from') }} <span class="label_table_name"></span> {{ __('admin.database.relationship.is_used_to_reference') }} <span>{{ str_singular(ucfirst($table)) }}</span>?</label>
                <select name="relationship_column_{{ $relationship['field'] }}" class="new_relationship_field select2 rowDrop" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="{{ $relationshipDetails->column }}">
                </select>
            </div>
        </div>
        <div class="relationship_details_content margin_top relationshipPivot @if($relationshipDetails->type == 'belongsToMany'){{ 'visible' }}@endif">
            <label>{{ __('admin.database.relationship.pivot_table') }}:</label>
            <select name="relationship_pivot_table_{{ $relationship['field'] }}" class="select2">
                @foreach($tables as $tbl)
                    <option value="{{ $tbl }}" @if(isset($relationshipDetails->pivot_table) && $relationshipDetails->pivot_table == $tbl){{ 'selected="selected"' }}@endif>{{ str_singular(ucfirst($tbl)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="relationship_details_content margin_top">
            <label>{{ __('admin.database.relationship.display_the') }} <span class="label_table_name"></span></label>
            <select name="relationship_label_{{ $relationship['field'] }}" class="rowDrop select2" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->label)){{ $relationshipDetails->label }}@endif">
            </select>
            <label class="relationship_key" style="@if($relationshipDetails->type == 'belongsTo' || $relationshipDetails->type == 'belongsToMany'){{ 'display:block' }}@endif">{{ __('admin.database.relationship.store_the') }} <span class="label_table_name"></span></label>
            <select name="relationship_key_{{ $relationship['field'] }}" class="rowDrop select2 relationship_key" style="@if($relationshipDetails->type == 'belongsTo' || $relationshipDetails->type == 'belongsToMany'){{ 'display:block' }}@endif" data-table="@if(isset($relationshipDetails->table)){{ $relationshipDetails->table }}@endif" data-selected="@if(isset($relationshipDetails->key)){{ $relationshipDetails->key }}@endif">
            </select>
        </div>

        <div class="relationship_details">
            <label>{{ __('admin.database.optional_details') }}</label>
            <textarea class="resizable-editor form-control" data-editor="json" name="relationship_details_{{ $relationship['field'] }}">@if(isset($relationshipDetails->details)){{ $relationshipDetails->details }}@endif</textarea>
        </div>
    </div>
    <input type="hidden" value="0" name="field_required_{{ $relationship['field'] }}" checked="checked">
    <input type="hidden" name="field_input_type_{{ $relationship['field'] }}" value="relationship">
    <input type="hidden" name="field_{{ $relationship['field'] }}" value="{{ $relationship['field'] }}">
    <input type="hidden" name="relationships[]" value="{{ $relationship['field'] }}">
</div>
