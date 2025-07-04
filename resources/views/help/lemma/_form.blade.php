<div id='help-district-wordform' class='help-section'>
    <h2>Поле "Диалект для автозаполнения словоформ"</h2>
    <p>Это поле необходимо заполнить, только если в поле "Лемма" введен шаблон. 
       В таком случае в этом поле указывают диалект, для которого будут сгенерированы 
       словоформы по заданному шаблону.</p>
</div>

<div id='help-lemma' class='help-section'>
    <h2>Поле "Лемма"</h2>
    В это поле можно написать не только лемму, но и шаблон для генерации полной парадигмы леммы.<br>
    Не забудьте в таком случае в поле <b>"Диалект для автозаполнения словоформ"</b> выбрать соответствующий диалект.<br>
    Две вертикальные чёрточки (<b>||</b>) показывает части сложного слова.<br>
    Одна вертикальная чёрточка (<b>|</b>) показывает неизменяемую часть слова. В скобках следуют части слова, которые нужно присоединить к неизменяемой части для образования основы или словоформы.<br>
    
    <div id='help-lemma-lang-1' class='help-lemma-lang'>
        <h3>Вепсский язык</h3>
        <div id='help-lemma-1-pos-name' class='help-lemma-pos'>        
            <h4>Именные части речи</h4>
            <h5>Словарный шаблон</h5>
            <p>В <b>круглых</b> скобках представлены через запятую падежные формы</p>
            <dl> 
                <dt>для одноосновных имён</dt>
                <dd>генитива ед. числа, партитива мн. числа<br>
                    per|t’ (-tin, -tid)<br>
                    reg|i (-gen, -id)
                </dd>
                <dt>для одноосновных существительных, употребляющихся только в форме ед. числа</dt>
                <dd>генитива ед. числа<br>
                    eländ (-an)<br>
                    absurd (-an)
                </dd>
                <dt>для двухосновных имён</dt>
                <dd>генитива ед. числа, партитива ед. числа, партитива мн. числа<br>
                    lap|s’ (-psen, -st, -psid)<br>
                    u|mi (-men, -nt, -mid)
                </dd>
                <dt>для двухосновных существительных, употребляющихся только в форме ед. числа</dt>
                <dd>генитива ед. числа и партитива ед. числа<br>
                    abutami|ne (-žen, -št)<br>
                    ahven||keito|z (-sen, -st)<br>
                    Не забудьте в этом случае в поле <b>"Число"</b> выбрать "только ед.ч.".
                </dd>
                <dt>для существительных, употребляющихся только в форме мн. числа</dt>
                <dd>партитива мн. числа<br>
                    kaksjaiž|ed (-id)<br>
                    raudaiž|ed (-id)<br>
                    Не забудьте в этом случае в поле <b>"Число"</b> выбрать "только мн.ч.".
                </dd>
            </dl>
                
            <p>Дефис (-) заменяет удаленную из формы неизменяемую часть слова.</p>
        </div>
        
        <div id='help-lemma-1-pos-verb' class='help-lemma-pos'>        
            <h4>Глагол</h4>
            <h5>Словарный шаблон</h5>
            <p>В <b>круглых</b> скобках через запятую приводятся падежные формы 3 лица ед. числа презенса и имперфекта. 
                Или 3 лица ед. числа презенса, имперфекта и императива.<br>
               Дефис (-) заменяет удаленную из формы неизменяемую часть слова.
            </p>
            <p><b>Примеры:</b><br>
            lug|eda (-eb, -i)<br>
            le|ta (-ndab, -ndi, -kaha)<br>
            pagi|šta (-žeb, -ži, -škaha)<br>
            pais|to {-so : -to, -tuo, -toloi}<br>
            sa|das (-se, -ihe) - возвратный глагол, не забудьте поставить галочку в соответствующем поле;<br>
            pan|das (-ese, -ihe, -gahas) - возвратный глагол, не забудьте поставить галочку.</p>
        </div>
    </div>    
    
    <div id='help-lemma-lang-4' class='help-lemma-lang'>
        <h3>Cобственно карельское наречие</h3>
        <div id='help-lemma-4-pos-name' class='help-lemma-pos'>        
            <h4>Именные части речи</h4>
            <h5>Минимизированный шаблон</h5>
            <p>В <b>квадратных</b> скобках части основ (после удаления слева неизменяемой части) без дефиса, через запятую. 
                Два варианта гласной основы через слэш /.</p>
            <dl>
                <dt>mua []</dt>
                <dd>одноосновное слово, у которого слабая гласная основа совпадает с формой ном. ед. ч.;</dd>

                <dt>ran|ta [na]</dt>
                <dd>одноосновное слово, в скобках слабая гласная основа (ген. ед.ч. без окончания n);</dd>

                <dt>nuor|i [e, ]</dt>
                <dd>двуосновное слово, в скобках гласная и согласная (парт. ед.ч. без ta/tä) основы;

                <dt>ve|si [je/te, t]</dt>
                <dd>двуосновное слово с двумя вариантами гласной основы, в скобках слабая (ген. ед. ч. без n) 
                    <b>/</b> сильная (илл. ед.ч. без h))<b>,</b> согласная (парт. ед.ч. без ta/tä) основы</dd>
            </dl>
            <h5>Шаблон с полным списком основ</h5>
            <p>В <b>фигурных</b> скобках части основ (после удаления слева неизменяемой части) c дефисом, через запятую. 
                Варианты гласной основы и основы мн.ч. через двоеточие.
                Если варианты совпадают, второй опускается.<br> 
                {слаб. гласная (ген. ед.ч. без n) <b>:</b> сил. гласная (илл. ед.ч. без h)<b>:</b> 
                партитив ед.ч.<b>,</b> слаб. мн.ч. (ген. мн.ч. без n) <b>:</b> сил. мн.ч. (илл. мн.ч. без h) 
            </p>
            <p><b>Примеры:</b><br>
            pah|a {-a, -ua, -oi}<br>
            paik|ka {-a : -ka, -kua, -oi : -koi}<br>
            pakkaškuu {-, -da, -loi}<br>
            pais|to {-so : -to, -tuo, -toloi}</p>
        </div>
        <div id='help-lemma-4-pos-verb' class='help-lemma-pos'>        
            <h4>Глагол</h4>
            <h5>Минимизированный шаблон</h5>
            <p>В <b>квадратных</b> скобках часть слабой гласной основы (презенс 1 л., ед. ч. после удаления слева неизменяемой части и справа окончания n) без дефиса.</p>
            <p><b>Примеры:</b> it|kie [e], an|tua [na], ju|uvva [o], kapaloi|ja [če], rua|tua [], ivual’|l’a [e]</p>
            
            <h5>Шаблон с полным списком основ</h5>
            <p>В <b>фигурных</b> скобках части основ (после удаления слева неизменяемой части) c дефисом, через двоеточие и запятую.
                Если основы через двоеточие совпадают, то вторую можно опустить.</p>
            <p> гл. слаб.(1 л. ед. ч. през. без n) <b>:</b><br>
                гл. сильн. (3 л. ед. ч. през. без u/y)<b>,</b><br>
                гл. слаб. имп.(1 л. ед. ч. имп. без n) <b>:</b><br>
                гл. сильн. имп.(3 л. ед. ч. имп.)<b>,</b><br>
                гл. сильн. или согл.(перфект: актив, 2 прич., крат. ф. без n/[nlršs][uy]n)<b>,</b><br>
                пасс. слаб. (3 л. мн. ч. през. без h)<b>,</b><br>
                пасс. сильн. (3 л. мн. ч. имп. без ih)
            </p>
            <p><b>Примеры:</b><br>
            pak|ota {-kuo, -koi, -on, -ota, -ott}<br>
            pais|tua {-sa : -ta, -soi : -to, -ta, -seta, -sett}<br>
            painu|o {-, -i : -, -, -ta, -tt}</p>
        </div>
    </div>    
    
    <div id='help-lemma-lang-5' class='help-lemma-lang'>
        <h3>Ливвиковское наречие</h3>
        <div id='help-lemma-5-pos-name' class='help-lemma-pos'>        
            <h4>Именные части речи</h4>
            <h5>Минимизированный шаблон</h5>
            <p>В <b>квадратных</b> скобках части основ (после удаления слева неизменяемой части) без дефиса, через запятую. 
                Два варианта гласной основы через слэш /.</p>
            <dl>
                <dt>mua []</dt>
                <dd>одноосновное слово, у которого слабая гласная основа совпадает с формой ном. ед. ч.;</dd>

                <dt>ran|du [na]</dt>
                <dd>одноосновное слово, в скобках слабая гласная основа (ген. ед.ч. без окончания n);</dd>

                <dt>nuor|i [e, ]</dt>
                <dd>двуосновное слово, в скобках гласная и согласная (парт. ед.ч. без ta/tä) основы;

                <dt>ve|zi [ie/ede, et]</dt>
                <dd>двуосновное слово с двумя вариантами гласной основы, в скобках слабая (ген. ед. ч. без n) 
                    <b>/</b> сильная (илл. ед.ч. без h))<b>,</b> согласная (парт. ед.ч. без ta/tä) основы</dd>
            </dl>
            <h5>Словарный шаблон</h5>
            <p>В <b>круглых</b> скобках представлены через запятую падежные формы генитива и партитива ед. числа, а после точки с запятой – партитива мн. числа.
                Дефис (-) заменяет удаленную из формы неизменяемую часть слова.
                Если слово используется только в ед. ч., то форма мн. ч. опускается и наоборот для слов, употребляющихся только в форме мн. числа, даются формы генитива и партитива мн. числа. 
                Не забудьте в таких случаях в поле <b>"Число"</b> выбрать "только ед.ч." или "только мн.ч.".
                Если существует вторая форма, то она дается через слэш (/).
            </p>
            <p><b>Примеры:</b><br>
            ran|du (-nan, -dua; -doi)<br>
            mua (-n, -du; -loi)<br>
            nuor|i (-en, -du; -ii)<br>
            v|ezi (-ien, -etty; -ezii/-ezilöi)<br>
            keriččem|et (-ien, -ii) - только мн.ч.
            </p>
        </div>
        
        <div id='help-lemma-5-pos-verb' class='help-lemma-pos'>        
            <h4>Глагол</h4>
            <h5>Словарный шаблон</h5>
            <p>В <b>круглых</b> скобках через запятую приводятся формы 1 и 3 лица ед. числа, а после точки с запятой – 3 лица мн. числа настоящего времени
                Дефис (-) заменяет удаленную из формы неизменяемую часть слова.
                Если существует вторая форма, то она дается через слэш (/).
            </p>
            <p><b>Примеры:</b><br> 
                kirjut|tua (-an, -tau; -etah),<br> 
                lu|gie (-ven, -gou; -gietah),<br>
                kačaht|uakseh (-ammos, -ah/-ahes; -etahes)
            </p>            
        </div>
    </div>    
    
    <div id='help-lemma-lang-6' class='help-lemma-lang'>
        <h3>Людиковское наречие</h3>
        <div id='help-lemma-6-pos-name' class='help-lemma-pos'>        
            <h4>Именные части речи</h4>
            <h5>Минимизированный шаблон</h5>
            <p>В <b>квадратных</b> скобках части основ (после удаления слева неизменяемой части) без дефиса, через запятую.</p>
            <p>Для одноосновных имен шаблон пустой или с одним показателем в тех случаях когда словарная форма и гласная основа не совпадают (d’og|i [e], ast|ii [’ai], pedä|i [jä]).
               <br>Чередования геминат не указываем.</p>
            <p>Для двуосновных имен в шаблоне гласная и согласная härki|n [me, n] (нулевая в случае совпадения с неизм. hiir|i [e, ] )</p>
            <p><b>Примеры:</b><br> 
            <dl>
                <dt>mua []</dt>
                <dd>одноосновное слово, у которого слабая гласная основа совпадает с формой ном. ед. ч.;</dd>

                <dt>leib|e [ä]</dt>
                <dd>одноосновное слово, в скобках слабая гласная основа (ген. ед.ч. без окончания n);</dd>

                <dt>tuoh|i [e, ]; </dt>
                <dd>двуосновные слова, в скобках гласная и согласная (парт. ед.ч. без ta/tä) основы;

            </dl>
        </div>
        <div id='help-lemma-6-pos-verb' class='help-lemma-pos'>        
            <h4>Глагол</h4>
            <h5>Минимизированный шаблон</h5>
            <p>В <b>квадратных</b> скобках часть слабой гласной основы (презенс 1 л., ед. ч. после удаления слева неизменяемой части и справа окончания n) без дефиса.</p>
            <p><b>Примеры:</b> 
                <br>kač|čoda [o], 
                <br>kyzy|dä [], 
                <br>itke|i [], 
                <br>pagi|šta [že], 
                <br>maga|ta [da], 
                <br>suvai|ta [če]</p>            
        </div>
    </div>    
</div>