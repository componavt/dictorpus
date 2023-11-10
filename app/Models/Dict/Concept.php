<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Dict\Lemma;

class Concept extends Model implements HasMediaConversions
{
    public $timestamps = false;
    protected $fillable = ['concept_category_id', 'pos_id', 'text_en', 'text_ru', 'wiki_photo', 'src', 'descr_en', 'descr_ru']; //'id', 
    const WIKI_API = 'https://en.wikipedia.org/w/api.php';
    const WIKI_URL = 'https://commons.wikimedia.org/wiki/File:';
    const WIKI_SRC = 'https://upload.wikimedia.org/wikipedia/commons/';
    const WIKI_PATH = 'https://commons.wikimedia.org/wiki/Special:FilePath/';
    const WIKI_SRC_THUMB  = 'https://upload.wikimedia.org/wikipedia/commons/thumb/';
    
    use HasMediaTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setWidth(200);
//             ->setManipulations(['w' => 200, 'h' => 200]);
//             ->performOnCollections('wikimedia');
    }
    
    public function getTextAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "text_" . $locale;
        $text = $this->{$column};
        
        if (!$text && $locale!='ru') {
            $text = $this->text_ru;
        }
        
        return $text;
    }
    
    public function getDescrAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "descr_" . $locale;
        $text = $this->{$column};
        
        if (!$text && $locale!='ru') {
            $text = $this->descr_ru;
        }
        
        return $text;
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\ConceptCategory;
    use \App\Traits\Relations\BelongsTo\POS;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Meanings;
    
    //Scopes
    use \App\Traits\Scopes\ConceptsForLdl;
    
    public function getSectionAttribute() : String
    {
        return trans("dict.concept_section_".substr($this->id, 0,1));
    }    

    public function getWikiPhotoEncodedAttribute() : String
    {
        return preg_replace("/\s/", "_",$this->wiki_photo);
    }    

    public static function getPOSCodes() {
        return ['NOUN', 'VERB', 'ADJ', 'ADV', 'PRON', 'NUM', 'SCONJ', 'AUX', 'PHRASE'];
    }

    public static function getPOSList()
    {     
        $list = [];
        foreach (self::getPOSCodes() as $code) {
            $pos = PartOfSpeech::getByCode($code);
            $list[$pos->id] = $pos->name;
        }
        return $list;         
    }    
    
    /** Gets list dropdown form
     * 
     * @return Array [<key> => <value>,..]
     */
    public static function getList($category_id=NULL, $pos_id=NULL)
    {     
        $objs = self::orderBy('id');
        
        if ($category_id) {                 
            $objs = $objs ->where('concept_category_id',$category_id);
        }
        
        if ($pos_id) {                 
            $objs = $objs ->where('pos_id',$pos_id);
        }
        
        $objs = $objs->get();
        $list = array();
        foreach ($objs as $row) {
            $list[$row->id] = $row->text;
        }
        
        return $list;         
    }
    
    public function countLemmas() {
        $concept_id = $this->id;
        return Lemma::whereIn('id', function($query) use ($concept_id) {
            $query->select('lemma_id')->from('meanings')
                  ->whereIn('id', function ($q) use ($concept_id) {
                      $q->select('meaning_id')->from('concept_meaning')
                        ->where('concept_id', $concept_id);
                  });
        })->count();
    }
    
    public function updateWikiSrc() {
        if (!$this->wiki_photo) {
            $this->src = '';
            $this->save();
            return;
        }
        $WikiInfo = self::getWikiInfo($this->wiki_photo);
        if (!$WikiInfo || !isset($WikiInfo['source'])) {
            return;
        }
        if (preg_match("/^".str_replace('/', '\/', self::WIKI_SRC)."(.+)$/", $WikiInfo['source'], $regs)) {
            $this->src = $regs[1];
            $this->save();
        }
    }

    public function photoInfo() {
        if (!$this->wiki_photo) {
            return;
        }
        $local_src = $this->getFirstMediaUrl('images', 'thumb');
        if (!$local_src) {
            if (!$this->src) {
                $this->updateWikiSrc();
            }
            $this->uploadImageToLibrary();
            $local_src = $this->getFirstMediaUrl('images');
        }

        if (!$local_src) {
            return null;
        }
        return ['url' => self::WIKI_URL.$this->wiki_photo_encoded,
                'source' => $local_src];            
/*        
        if ($this->src) {
            return ['url' => self::WIKI_URL.preg_replace("/\s/", "_",$this->wiki_photo),
                    'source' => self::WIKI_SRC.$this->src];
        }
        return self::getWikiInfo($this->wiki_photo);
*/    }
    
    public function uploadImageToLibrary() {
        if (!$this->wiki_photo) {
            return null;
        }
        ini_set( 'user_agent', 'VepKar/1.0 (http://dictorpus.krc.karelia.ru)' );
//        $this->addMediaFromUrl(self::WIKI_SRC.$this->src.'?width=200')
        $this->addMediaFromUrl(self::WIKI_PATH.$this->wiki_photo_encoded.'?width=1200')
             ->toCollection('images');
    }

    public static function getWikiInfo($filename) {
        $query_array = array (
            'action' => 'query',
            'titles' => 'File:'.$filename,
            'prop' => 'imageinfo',
            'format' => 'json',
            'iiprop' => 'url'
        );
        $query = http_build_query($query_array);
        $result = @file_get_contents(self::WIKI_API . '?' . $query);    
        if (!$result) {
            return;
        }
        $result = json_decode($result,true);
        $pages = $result['query']['pages'];
        if (!isset($pages[array_keys($pages)[0]]['imageinfo'][0])) {
            return;
        }
        $photo = $pages[array_keys($pages)[0]]['imageinfo'][0]; 
        if (!isset($photo['descriptionurl']) || !isset($photo['url'])) {
            return;
        }
        return ['url' => $photo['descriptionurl'],
                'source' => $photo['url']];                
    }

    public function photoPreview() {
        if (!$this->wiki_photo) {
            return;
        }
        if ($this->src) {
            return ['url' => self::WIKI_URL.preg_replace("/\s/", "_",$this->wiki_photo),
                    'source' => self::WIKI_SRC_THUMB.preg_replace("/\/([^\/]+)$/u","/\\1/50px-\\1",$this->src)];
        }
        $query_array = array (
            'action' => 'query',
            'titles' => 'File:'.$this->wiki_photo,
            'prop' => 'pageimages',
            'format' => 'json'
        );

        $query = http_build_query($query_array);
        $result = @file_get_contents(self::WIKI_API . '?' . $query);        
        if (!$result) {
            return;
        }
        $result = json_decode($result,true);
        $pages = $result['query']['pages'];
        if (!isset($pages[array_keys($pages)[0]])) {
            return;
        }
        $photo = $pages[array_keys($pages)[0]];
        if (!isset($photo['thumbnail']) || !isset($photo['thumbnail']['source'])) {
            return;
        }
        $url = $this->photoInfo();
        return ['url' => self::WIKI_URL.preg_replace("/\s/", "_",$this->wiki_photo),//isset($url['url']) ? $url['url'] : null,
                'source' => $photo['thumbnail']['source']];        
    }


    public static function urlArgs($request) {
//dd($request->all());        
        $url_args = Str::urlArgs($request) + [
                    'search_id'       => (int)$request->input('search_id'),
                    'search_category' => $request->input('search_category'),
                    'search_text'     => $request->input('search_text'),
                    'with_photos'     => (int)$request->input('with_photos'),
                ];
        
        if (!$url_args['search_id']) {
            $url_args['search_id'] = NULL;
        }
        
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $recs = self::orderBy('id');

        $recs = self::searchByID($recs, $url_args['search_id']);
        $recs = self::searchByCategory($recs, $url_args['search_category']);
        $recs = self::searchByText($recs, $url_args['search_text']);
        $recs = self::searchWithPhotos($recs, $url_args['with_photos']);
//dd($places->toSql());                                
        return $recs;
    }
    
    public static function searchByID($recs, $search_id) {
        if (!$search_id) {
            return $recs;
        }
        return $recs->where('id',$search_id);
    }
    
    public static function searchByCategory($recs, $category_id) {
        if (!$category_id) {
            return $recs;
        }
        return $recs->where('concept_category_id',$category_id);
    }
    
    public static function searchByText($recs, $text) {
        if (!$text) {
            return $recs;
        }
        return $recs->where(function($q) use ($text){
                            $q->where('text_en','like', $text)
                              ->orWhere('text_ru','like', $text);            
                });
    }
    
    public static function searchWithPhotos($recs, $with_photos) {
        if (!$with_photos) {
            return $recs;
        }
        return $recs->where('wiki_photo', '<>', '')->whereNotNull('wiki_photo');
    }
    
}
