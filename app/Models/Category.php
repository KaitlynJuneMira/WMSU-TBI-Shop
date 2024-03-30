<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function section(){
        return $this->belongsTo('App\Models\Section','section_id')->select('id','name');
    }

    public function parentcategory(){
        return $this->belongsTo('App\Models\Category','parent_id')->select('id','category_name');
    }

    public function subcategories(){
        return $this->hasMany('App\Models\Category','parent_id')->where('status',1);
    }

    // Fetch category informations
    public static function categoryDetails($url){
        $categoryDetails = Category::select('id','parent_id','category_name','url','description','meta_title','meta_keywords','meta_description')->with(['subcategories'=>function($query){
            $query->select('id','parent_id','category_name','url','description','meta_title','meta_keywords','meta_description');
        }])->where('url',$url)->first()->toArray();
        /*dd($categoryDetails);*/
        $catIds = array();
        $catIds[] = $categoryDetails['id'];

        if($categoryDetails['parent_id']==0){
            // Only show main category in breadcrumb
            $breadcrumbs = '<li class="is-marked">
                <a href="'.url($categoryDetails['url']).'">'.$categoryDetails['category_name'].'</a>
            </li>';
        }else{
            // Show main and sub cateogory in breadcrumb
            $parentCategory = Category::select('category_name','url')->where('id',$categoryDetails['parent_id'])->first()->toArray();
            $breadcrumbs = '<li class="has-separator">
                <a href="'.url($parentCategory['url']).'">'.$parentCategory['category_name'].'</a>
            </li><li class="is-marked">
                <a href="'.url($categoryDetails['url']).'">'.$categoryDetails['category_name'].'</a>
            </li>';
        }
        foreach ($categoryDetails['subcategories'] as $key => $subcat) {
            $catIds[] = $subcat['id'];
        }
        $resp = array('catIds'=>$catIds,'categoryDetails'=>$categoryDetails,'breadcrumbs'=>$breadcrumbs);
        return $resp;
    }

    // Get category name
    public static function getCategoryName($category_id){
        $getCategoryName = Category::select('category_name')->where('id',$category_id)->first();
        return $getCategoryName->category_name;
    }

    // Get category status
    public static function getCategoryStatus($category_id){
        $getCategoryStatus = Category::select('status')->where('id',$category_id)->first();
        return $getCategoryStatus->status;
    }
}
