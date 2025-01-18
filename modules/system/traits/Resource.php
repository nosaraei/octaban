<?php namespace System\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use System\Helpers\Utility;

trait Resource
{
    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        $morphMap = Relation::morphMap();

        if (! empty($morphMap) && in_array(get_parent_class(), $morphMap)) {
            return array_search(get_parent_class(), $morphMap, true);
        }

        return get_parent_class();
    }

    public function scopeBasicFilter($query, $request){

        $filters = Utility::getValidList($request->basic_filters);

        foreach ($filters as $filter_key => $filter) {

            if(method_exists(get_called_class(), "scope" . studly_case($filter_key))){

                $query->{camel_case($filter_key)}($filter->values, $filter); // , ["filters" => $filters]

            }
            else{

                switch ($filter->type) {
                    case 'multi':
                        $query->whereIn($filter_key, $filter->values);
                        break;

                    case 'boolean':
                        $query->where($filter_key, $filter->values[0]);
                        break;

                    case 'less_than':
                        $query->where($filter_key, '<=', $filter->values[0]);
                        break;

                    case 'more_than':
                        $query->where($filter_key, '>=', $filter->values[0]);
                        break;

                    case 'between':
                        $query->whereBetween($filter_key, $filter->values);
                        break;

                }

            }

        }

        return $query;
    }

    public function scopeSorting($query, $request){

        if($request->sort_by){
            $sort_options = get_called_class()::$sortable;

            if(isset($sort_options[$request->sort_by])){

                $query->orderBy($request->sort_by, $request->sort_order ?: "DESC");

            }

        }

        return $query;
    }

}
