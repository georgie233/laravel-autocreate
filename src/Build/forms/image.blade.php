<div class="form-group row">
    <label for="{column['name']}" class="col-12 col-sm-3 col-form-label text-md-right">{column['title']}</label>
    <div class="col-12 col-lg-9 img-up">
        <div id="{column['name']}-btn" class="btn btn-success">
            上传图片
        </div>
        <input id="{column['name']}-input" name="{column['name']}" accept=".jpg,.png,.bmp,.jpeg" type="file" hidden />
        <img style="max-width: 120px;" id="{column['name']}-img" src="" alt="">
        <div class="{{ $errors->has('{column['name']}') ? ' is-invalid' : '' }}"></div>
        @if ($errors->has('{column['name']}'))
            <span class="text-danger">
                <strong>{{ $errors->first('{column['name']}') }}</strong>
            </span>
        @endif
    </div>
</div>
