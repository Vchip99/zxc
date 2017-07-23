<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;

class BlogTag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'blog_id'];

    protected static function addTags($tags, $blogId){
    	$insertTags = [];
        foreach($tags as $tag){
            $insertTags[] = ['name'=> InputSanitise::inputString($tag), 'blog_id' => InputSanitise::inputInt($blogId)];
        }
        if(count($insertTags) > 0){
        	DB::table('blog_tags')->insert($insertTags);
        }
    }
}
