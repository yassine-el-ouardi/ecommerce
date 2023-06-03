<?php

namespace App\Http\Controllers;

use App\Models\EditorImage;
use App\Models\FooterLink;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Page;
use App\Models\PageWysiwygImage;
use App\Models\WysiwygImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\Helper\FileHelper;

class PagesController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'page.view')){
            return $can;
        }

        $query = Page::query()->orderBy($request->orderby, $request->type);

        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        if($request->q){
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }

        $data = $query->select('id', 'title', 'slug', 'page_from_component', 'created_at')
            ->paginate(Config::get('constants.api.PAGINATION'));

        foreach ($data as $item){
            $item['created'] = Utils::formatDate($item->created_at);
        }
        return response()->json(new Response($request->token, $data));
    }

    public function allPages(Request $request)
    {
        $data = Page::orderBy('created_at', 'ASC')
            ->select('id', 'title')
            ->get();

        return response()->json(new Response($request->token, $data));
    }

    public function action(Request $request, Page $page)
    {
        $validate = Validation::page($request);
        if($validate){
            return response()->json($validate);
        }

        $page_by_slug = Page::where('slug', $request['slug'])->get()->first();

        if($page->id){
            if($can = Utils::userCan($this->user, 'page.edit')){
                return $can;
            }

            $existing = Page::find($page->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            if($page_by_slug && $page_by_slug['id'] != $page->id){
                return response()->json(Validation::error($request->token,
                    __('api.slug_exists')
                ));
            }

            if(trim($request->description == '')){
                $request->description = '-1';
            }

            $filtered = array_filter($request->all(), function ($element) {
                return  !is_array($element) && '' !== trim($element);
            });

            if($request->description == '-1'){
                $filtered['description'] = '';
            }

            $page->update($filtered);

        }else{
            if($can = Utils::userCan($this->user, 'page.create')){
                return $can;
            }

            if($page_by_slug){
                return response()->json(Validation::error($request->token,
                    __('api.slug_exists')
                ));
            }

            $request['admin_id'] = $request->user()->id;
            $page = Page::create($request->all());

            if($request['wysiwyg_image_id']){
                foreach ($request['wysiwyg_image_id'] as $item){
                    WysiwygImage::where('id', $item)->update(array('item_id' => $page['id']));
                }
            }
        }

        $page['created'] = Utils::formatDate($page->created_at);
        return response()->json(new Response($request->token, $page));
    }


    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'page.view')){
            return $can;
        }
        $page = Page::find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $page)) {
            return $isOwner;
        }

        if (is_null($page)){
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $page));
    }

    public function delete(Request $request, $id)
    {


        try{

            if($can = Utils::userCan($this->user, 'page.delete')){
                return $can;
            }

            $page = Page::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $page)) {
                return $isOwner;
            }

            if (is_null($page)){
                return response()->json(Validation::noData());
            }

            $footer_link = FooterLink::where(['id' => $id])->first();

            if(!is_null($footer_link)){
                return response()->json(Validation::error($request->token,
                    __('api.delete_footer')
                ));
            }

            $descriptionImages = PageWysiwygImage::where(['page_id' => $id])->get();
            foreach ($descriptionImages as $di){
                $di->delete();
                FileHelper::deleteFile($di->image);
            }

            if ($page->delete()){
                return response()->json(new Response($request->token, $page));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }



    }
}
