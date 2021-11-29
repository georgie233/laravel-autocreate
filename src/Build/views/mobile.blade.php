@extends('admin::layouts.master')
@section('content')
    <div class="card" id="app">
        <div class="card-header">{MODEL_TITLE}管理</div>
        <div class="tab-container">
            <ul role="tablist" class="nav nav-tabs">
                <li class="nav-item"><a href="/{SMODULE}/{SMODEL}" class="nav-link active">{MODEL_TITLE}列表</a></li>
                <li class="nav-item"><a href="/{SMODULE}/{SMODEL}/create" class="nav-link">添加{MODEL_TITLE}</a></li>
            </ul>
            <div class="card card-contrast card-border-color-success">
                <div class="card-body">

                    @foreach($data as $d)
                        <div class="card">
                            <div class="card-body">
                                <div data-toggle="collapse" data-target="#coll{{$d['id']}}">
                                    {{-- main start --}}
                                    {COLUMNS_VALUE}
                                    {{-- main end --}}
                                </div>
                                <div id="coll{{$d['id']}}" class="collapse">

                                    {{-- insert your secondary --}}

                                    <div class="card-footer m-0 p-1 border-0 text-right">
                                        <a href="/{ROUTE_ROOT}/{{$d['id']}}/edit" class="btn btn-secondary">编辑</a>
                                        <button type="button" class="btn btn-secondary btn-danger"
                                                onclick="del({{$d['id']}},this)">删除
                                        </button>
                                        <form action="/{ROUTE_ROOT}/{{$d['id']}}" hidden method="post">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex card-footer m-0 p-0 justify-content-between">
                                <div class="ellipsis" style="flex:1;">
                                    <span class="pl-1" style="font-size: 17px;">...</span>
                                </div>
                                <div class="text-right" style="font-size: 12px;color: #666">
                                    <div style="margin-right: 5px;">创建时间：{!! $d['created_at'] !!}</div>
                                    <div style="margin-right: 5px;">修改时间：{!! $d['updated_at'] !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="float-right">
        {!!  $data->links() !!}
    </div>
@endsection
@section('scripts')
    <script>
        function del(id, el) {
          //  if (confirm('确定删除吗？')) {
          //      $(el).next('form').trigger('submit')
          //  }
        }
    </script>
@endsection
