
<div class="form-group">
    <label>@lang('quickadmin.category.fields.name')</label>

    <div class="input-group">
      <input type="text" class="form-control " name="name" value="{{ isset($category) ? $category->name : old('name') }}" id="name" autocomplete="true">
    </div>
</div>

<button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>



