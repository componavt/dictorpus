<?php
/**
 * Created by PhpStorm.
 * User: Dmitriy Pivovarov aka AngryDeer http://studioweb.pro
 * Date: 25.01.16
 * Time: 4:46
 */?>
<div class="form-group">
{!! Form::submit($title, ['class' => 'btn btn-primary btn-default', 
                          'name' => $name ?? null]) !!}
</div>