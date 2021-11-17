@extends('admin::layouts.master')
@section('content')
    <div class="card" id="app">
        <div class="card-header">{MODEL_TITLE}管理</div>
        <ul role="tablist" class="nav nav-tabs">
            <li class="nav-item"><a href="{{ route("{SMODULE}_{SMODEL}.index") }}" class="nav-link">{MODEL_TITLE}列表</a></li>
            <li class="nav-item"><a href="#" class="nav-link active">添加{MODEL_TITLE}</a></li>
        </ul>
        <form action="{{ route("{SMODULE}_{SMODEL}.store") }}" method="post" enctype="multipart/form-data">
            <div class="card-body card-body-contrast">
                @csrf
                {HTML}
            </div>
            <div class="card-footer text-muted">
                <button class="btn btn-primary offset-sm-2">保存提交</button>
            </div>
        </form>
    </div>
@endsection
