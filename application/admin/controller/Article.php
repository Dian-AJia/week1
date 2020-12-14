<?php

namespace app\admin\controller;

use think\Cache;
use think\Controller;
use think\Request;

class Article extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 接收数据
        $search = input('search');
        // 查询数据
        $data = \app\common\model\Article::where("title","like","%$search%")->order('create_time desc')->paginate(10);
        $page = $data->render();
        $article = $data->toArray()['data'];
        //print_r($article);
        return view('show',['article'=>$article,'page'=>$page,'search'=>$search]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        //接收数据
        $data['title'] = input('title');
        $data['desc'] = input('desc');
        $data['content'] = input('content');
        //做验证
        $result = $this->validate($data,
            [
                'title|文章标题'  => 'require|max:50',
            ]);
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result,'admin/Article/create');
        }
        // 文件上传
        // 获取表单上传文件
        $files = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        foreach($files as $file){
            // 移动到指定地点
            $info = $file->validate(['size'=>5*1024*1024,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $path = DS . 'uploads' . DS . $info->getSaveName();
                $data['image'] = $path;
            }else{
                // 上传失败获取错误信息
                $this->error($file->getError());
            }
        }
        // 执行SQL语句
        $res = \app\common\model\Article::create($data);
        if ($res){
            $options = [
                // 缓存类型为File
                'type'  =>  'File',
                // 缓存有效期为永久有效
                'expire'=>  0,
                //缓存前缀
                'prefix'=>  'think',
                // 指定缓存目录
                'path'  =>  APP_PATH.'runtime/cache/',
            ];
            Cache::connect($options);
            return redirect('admin/Article/index');
        }else{
            $this->error("添加失败，请重试","admin/Article/create");
        }

    }


    /**
     * 搜索关键字的跳转方法
     *
     * @param
     * @return \think\Response
     */
    public function search()
    {
        // 接收数据
//        $data = input('search');
//        $param = \app\common\model\Article::where("title","like","%$data%")->paginate(10);
//        $page = $data->render();
//        $article = $param->toArray()['data'];
//        return view('show',['article'=>$article,'page'=>$page]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        $id=input('id');
        if($id)
        {
            $res=\app\common\model\Article::destroy($id);
            if($res)
            {
                $this->success('删除成功!');
            }
            else
            {
                $this->error('删除失败!');
            }
        }
    }
}
