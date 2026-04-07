<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KxAdmin\Response\ApiResponse;
use Illuminate\Routing\Controller;
use KxAdmin\Structs\QueryStruct;

class AdminController extends Controller
{
    use ApiResponse;

    protected string $model;
    protected string $validate;

    /**
     * 后台管理首页
     */
    public function index(Request $request): JsonResponse
    {
        $query = QueryStruct::load($request->all());
        $search = $this->model::query();
        $count = $search->count();
        $this->beforeSearch($search);
        $list = $search->offset(($query->current - 1) * $query->size)->limit($query->size);

        return $this->success([
            'records' => $list->get(),
            'total' => $count,
            'current' => $query->current,
            'size' => $query->size,
        ]);
    }

    /**
     * 显示创建表单
     */
    public function create()
    {
        try {
            $request = app($this->validate);
            $params = $request->validated();
            $this->beforeCreate($params);
            $model = app($this->model);
            return $this->success($model->create($params), '创建成功');
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }

    /**
     * 更新资源
     */
    public function update(int $id): JsonResponse
    {
        $request = app($this->validate);
        $params = $request->validated();
        $this->beforeUpdate($params);
        $model = app($this->model);
        return $this->success($model->findOrFail($id)->update($params), '更新成功');
    }

    /**
     * 删除资源
     */
    public function destroy($id): JsonResponse
    {
        $model = app($this->model);
        $model = $model->findOrFail($id);
        $this->beforeDelete($model);
        return $this->success($model->delete(), '删除成功');
    }

    /**
     * 显示资源
     */
    public function show($id): JsonResponse
    {
        $model = app($this->model);
        return $this->success($model->findOrFail($id));
    }

    /**
     * 创建前
     * @return void
     */
    public function beforeCreate(array &$params)
    {

    }

    public function beforeUpdate(array &$params)
    {

    }

    public function beforeSearch(&$search)
    {

    }

    public function beforeDelete(&$model)
    {

    }
}
