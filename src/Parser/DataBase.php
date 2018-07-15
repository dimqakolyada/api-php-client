<?php
/**
 * Created by PhpStorm.
 * User: dimqakolyada
 * Date: 12.07.18
 * Time: 20:26
 */

namespace SimaLand\API\Parser;

use Models\Category;
use Models\CategoryQuery;
use Models\AttrQuery;
use Models\Attr;
use SimaLand\API\BaseObject;
use SimaLand\API\Record;

class DataBase extends BaseObject implements StorageInterface
{
    public $tableName;


    /**
     * Сохранить строку сущности.
     *
     * @param mixed $item
     */
    public function save(Record $item)
    {
        $classQueryName = 'Models\\' . ucfirst($this->tableName) . 'Query';
        $className = 'Models\\' . ucfirst($this->tableName);

        $row = $classQueryName::create()->findOneById($item->data['id']);

        if($row === null){
            $row = new $className();
        }
        foreach ($item->data as $key => $value){
            $key = 'set'.$this->undescoresToCamelCase($key);
            $row->$key($value);
        }
        $row->save();


        // TODO: Implement save() method.
    }

    public function undescoresToCamelCase($string, $capitalizeFirstCharacter = true)
    {

        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}