<?php

namespace Lionix\SeoManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use Lionix\SeoManager\Models\SeoManager as SeoManagerModel;
use Lionix\SeoManager\Models\Translate;
use Lionix\SeoManager\Traits\SeoManagerTrait;

class ManagerController extends Controller
{
    use SeoManagerTrait;

    protected $locale;

    public function __construct()
    {
        if(Input::get('locale')){
            /** @scrutinizer ignore-call */
            app()->setLocale(Input::get('locale'));
            $this->locale = app()->getLocale();
        }
}
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return /** @scrutinizer ignore-call */ view('seo-manager::index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoutes()
    {
        $routes = SeoManagerModel::all();
        return /** @scrutinizer ignore-call */ response()->json(['routes' => $routes]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModels()
    {
        try {
            $models = $this->getAllModels();
            return /** @scrutinizer ignore-call */ response()->json(['models' => $models]);
        } catch (\Exception $exception) {
            return /** @scrutinizer ignore-call */ response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getModelColumns(Request $request)
    {
        try {
            $model = $request->get('model');
            $columns = $this->getColumns($model);
            return /** @scrutinizer ignore-call */ response()->json(['columns' => $columns]);
        } catch (\Exception $exception) {
            return /** @scrutinizer ignore-call */ response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeData(Request $request)
    {
        $allowedColumns = Schema::getColumnListing(config('seo-manager.database.table'));
        try {
            $id = $request->get('id');
            $type = $request->get('type');
            $seoManager = SeoManagerModel::find($id);
            if (in_array($type, $allowedColumns)) {
                $data = $request->get($type);
                if($type != 'mapping' && $this->locale !== /** @scrutinizer ignore-call */ config('seo-manager.locale')){
                    $translate = $seoManager->translation()->where('locale', $this->locale)->first();
                    if(!$translate){
                        $newInst = new Translate();
                        $newInst->locale = $this->locale;
                        $translate = $seoManager->translation()->save($newInst);
                    }
                    $translate->$type = $data;
                    $translate->save();
                }else{
                    $seoManager->$type = $data;
                    $seoManager->save();
                }
            }
            return /** @scrutinizer ignore-call */ response()->json([$type => $seoManager->$type]);
        } catch (\Exception $exception) {
            return /** @scrutinizer ignore-call */ response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExampleTitle(Request $request)
    {
        try {
            $manager = SeoManagerModel::find($request->id);
            $titles = $request->get('title_dynamic');
            $exampleTitle = $this->getDynamicTitle($titles, $manager);
            return /** @scrutinizer ignore-call */ response()->json(['example_title' => $exampleTitle]);
        } catch (\Exception $exception) {
            return /** @scrutinizer ignore-call */ response()->json(['status' => false, 'message' => $exception->getMessage()]);

        }
    }

    public function deleteRoute(Request $request)
    {
        try {
            SeoManagerModel::destroy($request->id);
            return /** @scrutinizer ignore-call */ response()->json(['deleted' => true]);
        } catch (\Exception $exception) {
            return /** @scrutinizer ignore-call */ response()->json(['status' => false, 'message' => $exception->getMessage()]);

        }
    }
}
