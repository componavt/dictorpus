<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Motype;

class Motive extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name_en', 'name_ru', 'motype_id', 'parent_id', 'code'];
    const CATEGORIES = [
        'А' => 'Происхождение определенной общности',
/*3. Происхождение фамилии (семьи) от
а) основателя селения
б) давнего поселенца 
в) переселенца*/

        'В' => 'Появление поселенцев в конкретной местности',
/*3. Появление аборигенов
4. Появление переселенцев из другой местности
6. Появление беглых
14. Появление исторических лиц*/

        'Г' => 'Пребывание (былое) данного персонажа или определенной общности в конкретной местности',
/*3. Пребывание аборигенов в конкретной местности
а) одного персонажа 
б) общности 
5. Пребывание первопоселенцев (давних поселенцев) в конкретной местности
а) одного персонажа 
б) общности 
 
7. Пребывание разбойников (социальных противников) в конкретной местности
а) одного персонажа 
10. Пребывание беглых в конкретной местности
а) одного персонажа 
б) общности */

        'Д' => 'Выбор места для основания селения (объекта культового назначения)',
/*4. Выбор места, на котором произошло спасение от смерти (болезни, по обету)
6. Выбор места по принципу удобства расположения 
8. Выбор места по принципу богатства промысловыми угодьями */

        'Е' => 'Основание селения',
/*1. Основание селения одним персонажем 
а) переселенцем 
б) беглым
2. Основание селения (двумя) соседями-первопоселенцами
4. Основание селения группой первопоселенцев 
а) переселенцами 
б) беглыми 
5. Переселение деревни на новое место */
        'Ж' => 'Основание (предполагаемое или осуществленное) строительного объекта',
/*3. Основание строительных объектов местными жителями, духовными лицами
а) объектов культового назначения 
6. Разыскания в связи со строительством (предполагаемым или осуществленным)*/

        'З' => 'Происхождение топонима',
/*1. Происхождение топонима от имени (прозвища)
а) аборигена
б) первопредка / основателя / первопоселенца
2. Происхождение топонима, обозначающего этническую принадлежность
а) аборигена 
3. Происхождение топонима, обозначающего социальную (половозрастную) принадлежность (титул)
а) первопоселенца, раннего поселенца, основателя селения либо лица, посетившего данную местность 
6. Происхождение топонима от наименования объекта культового назначения
7. Происхождение топонима от особенностей местности 
10. Происхождение топонима по случайному признаку (народная этимология) */

        'И' => 'Происхождение антропонима',
/*5. Происхождение антропонима от наименования места жительства родоначальника */

        'И2' => 'Происхождение коллективного прозвища',
/*    1. От особенностей хозяйственной деятельности 
    2. От особенностей речи 
    3. От особенностей характера и поведения 
    4. От физических/психических/умственных особенностей местных жителей 
    5. От особенностей образа жизни и быта 
    6. От особенностей расположения / местоположения / ландшафтных свойств селения */

        'К' => 'Хозяйственная деятельность персонажа (-ей)',
/*3. Хозяйственная деятельность аборигенов (лопарей)
а) ремесленные работы 
б) охотничий промысел 
в) рыболовство 
г) сельскохозяйственные работы 
д) скотоводство 
е) лесоразработки 
5. Хозяйственная деятельность первопоселенцев, местных жителей, владельцев местности, переселенцев 
а) ремесленные работы 
б) охотничий промысел 
в) рыболовство 
г) сельскохозяйственные работы 
д) скотоводство 
е) лесоразработки 
ж) батрачество 
з) торговля
7. Хозяйственная деятельность беглых
а) охотничий промысел 
б) сельскохозяйственные работы 
в) лесоразработки 
г) прошение милостыни 
д) обмен с местными жителями 
е) медицинская помощь местному населению*/

        'О' => 'Проявление магической силы (магических способностей)',
/*1. Предсказание 
2. Сон вещий 
5. Проклятие 
11. Перевоплощение
а) способность перевоплощать других 
12. Воскрешение 
14. Способность плыть на камне */

        'П' => 'Нападение антагониста(-ов)',
/*2. Нападение разбойников (социальных противников) 
а) пленение, лишение свободы 
3. Нападение внешнего врага (-ов) 
а) ограбление (осуществленное либо предполагаемое) 
б) пленение, лишение свободы 
5. Нападение этнического антагониста (иноплеменника) */

        'Р' => 'Избавление от антагониста (-ов) (мифического, мифолого-эпического, социального, этнического)',
/*1. Избавление от антагонистов посредством магической силы, в результате свершившегося чуда 
а) окаменение 
б) слепота ("темень") 
в) неуязвимость особого рода 
2. Избавление от антагонистов посредством военной хитрости 
а) мнимый проводник 
б) пороги и водопады 
3. Избавление от антагониста (-ов) посредством ловкости и предприимчивости 
    7. Избавление от антагониста (-ов) бегством */

        'С' => 'Борьба с антагонистом (-ами)',
/*3. Борьба с внешними врагами 
а) непосредственная борьба (бой, сражение) */

        'Т' => 'Победа над антагонистом (-ами)',
/*3. Победа над внешними врагами 
а) уничтожение (ранение) 
б) пленение 
5. Победа над этническим антагонистом (иноплеменником) */

        'У' => 'Исчезновение данного персонажа или определенной общности в конкретной местности',
/*2. Самозахоронение (в том числе временное)
а) местных жителей 
3. Окаменение 
а) социальных, этнических антагонистов / иноплеменников 
4. Уход (бегство, отступление) из данной местности в иные земли
а) мифических, мифолого-эпических персонажей, аборигенов 
5. Гибель
а) аборигенов 
б) местных жителей 
в) разбойников 
г) внешних врагов 
7. Самосожжение 
а) аборигенов */

        'Ф' => 'Оставление следов пребывания в конкретной местности',
/*1. Оставление следов пребывания в конкретной местности мифическими, полумифическими персонажами, аборигенами, "панами"
а) остатки строений 
3. Оставление следов пребывания в конкретной местности ранними поселенцами (прежними насельниками)
а) объекты культового назначения 
б) остатки строений 
в) клады 
8. Оставление следов пребывания в конкретной местности беглыми
а) остатки строений */

        'X' => 'Захоронение клада ("зачарованного", реального)',
/*1. Захоронение клада мифическими персонажами (аборигенами) 
а) на острове, полуострове 
3. Захоронение клада давними первопоселенцами
а) в горе (сопке, холме, кургане) 
7. Кладоискание
а) безуспешное 
б) успешное 
8. Условие для получения клада
а) съесть 16 кг соплей чужого человека 
б) бросить топор через пролив 
в) продуть зоб глухаря через пролив 
г) пройти на жеребенке, прожившем одну ночь, по льду, просуществовавшему три ночи / одну ночь 
*/
    ];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\ParentTrait;
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Texts;
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Children;
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;
    use \App\Traits\Methods\getNameByID;
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function motype()
    {
        return $this->belongsTo(Motype::class);
    }
    
    public function genreName($with_number=false)
    {
        if (!$this->motype) {
            return;
        }
        if (!$this->motype->genre) {
            return;
        }
        return ($with_number ? $this->motype->genre->numberInList() : '')
             . '. '. $this->motype->genre->name;
    }
    
    public function getFullCodeAttribute() {
        $code = $this->code;
        if ($this->parent) {
           $code = $this->parent->code. '.'. $code;
        }
        if ($this->motype) {
           $code = $this->motype->code. '.'. $code;
        }
        return $code;
    }
    
    public function getFullNameAttribute() {
        $name = $this->name;
        if ($this->parent) {
           $name = $this->parent->name. ' ('. $name. ')';
        }
        return $name;
    }
        
    public static function getList($motype_id=NULL, $parent_id='undef') {     
        $recs = self::orderBy('motype_id')->orderBy('code');
        
        if ($motype_id) {        
            $recs = $recs->whereMotypeId($motype_id);
        }
        
        if ($parent_id === NULL) {            
            $recs = $recs->whereNull('parent_id');
        } elseif ($parent_id != 'undef') {            
            $recs = $recs->whereParentId($parent_id);
        }
        
        $recs = $recs->get();
        
        $list = array();
        foreach ($recs as $row) {
            $list[$row->id] = $row->full_code. '. '. $row->full_name;
        }
        
        return $list;         
    }
    
    public static function getGroupedList()
    {
        $types = Motype::orderBy('code')->get();
        
        $list = [];
        
 //       $locale = LaravelLocalization::getCurrentLocale();
        
        foreach ($types as $type) {
            foreach (self::whereMotypeId($type->id)->get() as $parent) {
                $children = self::whereParentId($parent->id);
                if (!$children->count()) {
                    $list[$type->code.'. '.$type->name][$parent->id] = $parent->code.'. '.$parent->name;
                } else {
                    foreach ($children->get() as $child) {
                        $list[$type->code.'. '.$type->name][$child->id] = $parent->code.$child->code.'. '.$parent->name.' '.$child->name;                        
                    }
                }
            }
        }
        
        return $list;         
    }
    
    public static function urlArgs($request) {
        $url_args = url_args($request) + [
                    'search_genre'   => (array)$request->input('search_genre'),
                    'search_motype'   => (array)$request->input('search_motype'),
                    'search_id'  => (int)$request->input('search_id'),
                    'search_name' => $request->input('search_name'),
                ];
        
        if ($url_args['search_id']==0) {
            $url_args['search_id'] = '';
        }
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $recs = self::orderBy('motype_id')->orderBy('code');
        $recs = self::searchByGenres($recs, $url_args['search_genre']);
        $recs = self::searchById($recs, $url_args['search_id']);
        $recs = self::searchByName($recs, $url_args['search_name']);
        
        if ($url_args['search_motype']) {
            $recs->whereIn('motype_id', $url_args['search_motype']);
        }
        return $recs;
    }
    
    use \App\Traits\Methods\search\byID;
    use \App\Traits\Methods\search\byName;   
    
    public static function searchByGenres($objs, $genres) {
        if (!sizeof($genres)) {
            return $objs;
        }

        foreach (Genre::whereIn('parent_id', $genres)->get() as $g) {
            $genres[] = $g->id;
        }
        
        return $objs->whereIn('motype_id', function ($q) use ($genres) {
                        $q->select('id')->from('motypes')
                          ->whereIn('genre_id',$genres);
                    });
    }
    
}
