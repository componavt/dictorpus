                <div class="row">
                  <div class="col-xs-3">
                        @include('widgets.form.formitem._select',
                                ['name' => 'new_relation_'.$meaning->id,
                                 'values' => $meaning->missingRelationsList(),
                                 'attributes' => ['id'=>'new_relation_'.$meaning->id]])
                  </div>
                  <div class="col-xs-3">
                      <button type="button" class="btn btn-info add-new-relation" 
                              data-for='{{ $meaning->id}}'>
                          {{trans('dict.add_new_relation')}}
                      </button>
                  </div>
                </div>
