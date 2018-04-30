<?php
namespace Specialtactics\L5Api\Models;

use Illuminate\Database\Eloquent\Model;

class RestfulModel extends Model {
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

}
