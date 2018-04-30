<?php
namespace Specialtactics\L5Api\Models;

use Illuminate\Database\Eloquent\Model;
use Uuid;

class RestfulModel extends Model {
    /**
     * @var int Auto increments integer key
     */
    public $primaryKey = 'user_id';

    /**
     * @var string UUID key
     */
    public $uuidKey = 'user_uuid';

    /**
     * @var array These attributes (in addition to primary & uuid keys) are not allowed to be updated.
     */
    public $immutableAttributes = ['created_at'];

    /**
     * @var array Acts like $with (eager loads relations), however only for immediate controller requests for that object
     */
    public $localWith = [];

    /**
     * Boot the model
     */
    public static function boot()
    {
        parent::boot();

        // If the PK(s) are missing, generate them
        static::creating(function(RestfulModel $model) {
            $uuidKeyName = $model->getUuidKeyName();

            if (!array_key_exists($uuidKeyName, $model->getAttributes())) {
                $model->$uuidKeyName = Uuid::generate(4);
            }
        });
    }

    /**
     * Get the UUID key for the model.
     *
     * @return string
     */
    public function getUuidKeyName()
    {
        return $this->uuidKey;
    }

    /**
     * Get the value of the model's UUID key.
     *
     * @return mixed
     */
    public function getUuidKey()
    {
        return $this->getAttribute($this->getUuidKeyName());
    }

}
