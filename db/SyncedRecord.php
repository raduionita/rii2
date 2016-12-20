<?php

namespace common\db;

use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * Class SyncedRecord
 * @package common\db
 */
abstract class SyncedRecord extends ActiveRecord
{
    /** @var null|bool */
    private $_synched = null;
    /** @var bool */
    private $_lazy    = true;

    /**
     * SyncedRecord constructor. Expanded configuration.
     * @example new Person()
     * @example new Person(1, 'NoName')
     * @example new Person([1, 'NoName'])
     * @example new Person(['id' => 1, 'name' => 'NoName'])
     * @override ActiveRecord::__construct()
     * @param mixed $config
     */
    public function __construct($config = null)
    {
        $config = is_array($config) ? $config : func_get_args();
        if (!empty($config) && is_numeric(key($config))) {
            $attributes = $this->attributes();
            foreach ($config as $i => $value) {
                unset($config[$i]);
                $config[$attributes[$i]] = $value;
            }
        }
        parent::__construct($config);
    }

    /**
     * @param array $pks
     * @param array $attributes
     * @return object
     */
    public static function findByPk(array $pks, array $attributes = [])
    {
        // @todo make this make more sence - easier to use
        return self::find()->where($pks)->select(empty($attributes) ? '*' : $attributes)->one();
    }

    /**
     * Sync record with database. Use pk to find row.
     * @return bool if it managed to sync object
     */
    public function sync()
    {
        // eary exit - prevent multiple ::synch()
        if (!is_null($this->_synched)) {
            return $this->_synched;
        }
        // synching starts with the primary keys
        $pks = self::primaryKey();
        // if the pks were not set...don't bother synching
        foreach ($pks as $attribute) {
            if ($status = !isset($this->$attribute)) {
                // sync fail if pk not set
                return $this->_synched = !$status;
            }
        }
        // [0=>field] to [field=>value]
        $pks = array_flip($pks);
        foreach ($pks as $pk => &$value) {
            $value = $this->$pk;
        }
        // @todo search in cache

        // else .. seach in db
        $this->_synched = !!self::find()->where($pks)->exists();
        // @todo save in cache

        // mask as update & pks are not dirty any more
        if ($this->_synched === true) {
            // @todo find a faster|better way
            foreach ($pks as $pk => $value) {
                $this->setOldAttribute($pk, $value);
            }
        }
        // just so that the user knows
        return $this->_synched;
    }

    /**
     * Lazy loading attributes. Works ONLY with synched records
     * @override
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $this->hydrate();
        return parent::__get($name);
    }

    /**
     * Convert into an array.
     * If record has been synched, un-lazy it first to get all fields from the db.
     * @override ArrayableTrait::toArray();
     * @param array $fields
     * @param array $expand
     * @param bool $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $this->hydrate();
        return parent::toArray($fields, $expand, $recursive);
    }

    /**
     * De-lazy the record. Populate it with data from the db.
     * Only works if the record has been synched.
     */
    private function hydrate()
    {
        // lazy only for synched records
        if ($this->_synched === true && $this->_lazy === true) {
            if ($data = (new Query())->from(static::tableName())->where($this->getPrimaryKey(true))->one()) {
                $this->setAttributes(array_merge($data, $this->getDirtyAttributes()), false);
                $this->setOldAttributes($data);
            }
            // only once, stop your lazyness
            $this->_lazy = false;
        }
    }
}
