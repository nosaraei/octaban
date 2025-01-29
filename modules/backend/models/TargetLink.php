<?php namespace Backend\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use System\Classes\PluginManager;

class TargetLink extends Model
{
    use Validation;

    public $table = 'backend_target_links';

    // <editor-fold desc="Fields">

    public $rules = [
        "target_type" => "required"
    ];

    public $attributeNames = [];

    protected $jsonable = [
        "extra_data"
    ];
    
    public $timestamps = false;

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    // </editor-fold>

    // <editor-fold desc="Define Relations">

    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    // </editor-fold>

    // <editor-fold desc="Dropdown Options">
    
    public function getTargetTypeOptions($value)
    {
        $target_types = array_map(function($item){ return $item["label"]; }, $this->prepareTargetTypes());
        
        return array_merge([
            'home' => 'خانه',
            'url' => 'آدرس اینترنتی',
            'telegram' => 'صفحه تلگرام',
            'instagram' => 'صفحه اینستاگرام',
        ], $target_types);
        
    }
    
    // </editor-fold>

    // <editor-fold desc="Get Attributes Functions">

    // </editor-fold>

    // <editor-fold desc="Events">
    
    public function beforeSave(){
    
        $description = "";
        
        switch ($this->target_type){
            case "home":
                $description = "لینک به خانه";
                break;
                
            case "url":
                $description = "لینک به آدرس وب (" . $this->main_value . ")";
                break;
                
            case "telegram":
                $description = "لینک به تلگرام (" . $this->main_value . ")";
                break;
    
            case "instagram":
                $description = "لینک به پیج اینستاگرام (" . $this->main_value . ")";
                break;
    
    
            default:
        
                $target_types = $this->prepareTargetTypes();
                $description = $target_types[$this->target_type]["description"]($this);
        }
        
        $this->description = $description;
    }

    // </editor-fold>
    

    // <editor-fold desc="Extend">

    public function filterFields($fields, $context = null)
    {
        switch ($fields->target_type->value) {
            case "home":
        
                $fields->main_value->hidden = true;
                $fields->main_value->type = "text";
        
                break;
            case "url":
                
                $fields->main_value->hidden = false;
                $fields->main_value->label = "آدرس لینک";
                $fields->main_value->type = "text";
    
                break;
            case "telegram":
            case "instagram":
        
                $fields->main_value->hidden = false;
                $fields->main_value->label = "نام کاربری";
                $fields->main_value->type = "text";
                break;
                
            default:
                
                $target_types = $this->prepareTargetTypes();
                $target_types[$fields->target_type->value]["extendFields"]($fields);
        }
    }

    // </editor-fold>
    
    protected function prepareTargetTypes()
    {
        $pluginManager = PluginManager::instance();
        $plugins = $pluginManager->getPlugins();
        
        $targetTypes = [];
        foreach ($plugins as $plugin) {
            $types = $plugin->registerTargetTypes();
            if (!is_array($types)) {
                continue;
            }
    
            $targetTypes = array_merge($targetTypes, $types);
        }
        
        return $targetTypes;
        
    }
    
}
