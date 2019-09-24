<div class="form-group">
<input id="{{isset($id_name) ? $id_name : ''}}" 
       class="btn btn-primary btn-default" type="button" 
       value="{{isset($title) ? $title: ''}}"
       {{isset($event) ? $event : ''}}
       onClick="{{isset($on_click) ? $on_click : ''}}">
</div>
