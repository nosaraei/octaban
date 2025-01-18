<?php namespace System\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;

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
}
