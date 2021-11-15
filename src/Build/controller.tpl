<?php
namespace {NAMESPACE_CONTROLLER};
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use {NAMESPACE_MODEL};
use {NAMESPACE_REQUEST};
class {CONTROLLE_NAME} extends BaseController
{
    //显示列表 GET: /{SMODEL}
    public function index(Request $request,{MODEL} ${SMODEL})
    {
        if ($this->isAjax){
            $data = $this->handleAjaxIndex($request,${SMODEL});
            return $this->responseSuccess($data);
        }

        $data = {MODEL}::paginate(10);
        return view(($this->isMobile() || $this->isWeixin())?'{VIEW_NAME}.mobile':'{VIEW_NAME}.index', compact('data'));
    }

    //创建视图 GET: /{SMODEL}/create
    public function create({MODEL} ${SMODEL})
    {
        if ($this->isAjax)return $this->responseIllegal();
        return view('{VIEW_NAME}.create',compact('{SMODEL}'));
    }

    //保存数据 POST: /{SMODEL}
    public function store({MODEL}Request $request,{MODEL} ${SMODEL})
    {
        $data = $request->all();

        {STOREINSERT}


        ${SMODEL}->fill($data);
        ${SMODEL}->save();

        if ($this->isAjax) return $this->responseSuccess(${SMODEL});
        return redirect('/{ROUTE_ROOT}')->with('success', '保存成功');
    }

    //显示记录 GET: /{SMODEL}/id
    public function show({MODEL} $field)
    {
        if ($this->isAjax) return $this->responseSuccess($field);

        return view('{VIEW_NAME}.show', compact('field'));
    }

    //编辑视图 GET: /{SMODEL}/id/edit
    public function edit({MODEL} ${SMODEL})
    {
        if ($this->isAjax)return $this->responseIllegal();
        return view('{VIEW_NAME}.edit', compact('{SMODEL}'));
    }

    //更新数据 PUT: /{SMODEL}/id
    public function update({MODEL}Request $request, {MODEL} ${SMODEL})
    {
        $data = $request->all();

        {UPDATEINSERT}

        ${SMODEL}->update($data);

        if ($this->isAjax) return $this->responseSuccess(${SMODEL});
        return redirect('/{ROUTE_ROOT}')->with('success','更新成功');
    }

    //删除模型 DELETE: /{SMODEL}/id
    public function destroy({MODEL} ${SMODEL})
    {
        {DELETEINSERT}
        ${SMODEL}->delete();

        if ($this->isAjax) return $this->responseSuccess([]);
        return redirect('{ROUTE_ROOT}')->with('success','删除成功');
    }
}
