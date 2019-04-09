<?php 
$features = [];
//dd($lemma->features->filledFeatures());
if ($lemma->features) {
    foreach (array_values($lemma->features->filledFeatures()) as $field) {
       if (is_array($field)) {
           $values = trans('dict.'.$field['title'].'s');
           if (isset($values[$field['value']])) {
               $value = $values[$field['value']];
               if ($field['title'] == 'degree') {
                   $value .= ' '. trans('dict.'.$field['title']);
               }
               $features[] = $value;
           }
       } else {
           $features[] = trans('dict.'.$field);
       }
    }
}  
?>  
@if (sizeof($features))
    ({{{join(', ', $features)}}})
@endif