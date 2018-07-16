<?php
/**
 * Created by PhpStorm.
 * User: dimqakolyada
 * Date: 12.07.18
 * Time: 20:26
 */

namespace SimaLand\API\Parser;

use Models\Category;
use Models\CategoryPriority;
use Models\CategoryQuery;
use Models\CategoryPriorityQuery;
use SimaLand\API\BaseObject;
use SimaLand\API\Entities\CategoryList;
use SimaLand\API\Record;

class DataBase extends BaseObject implements StorageInterface
{
    public $tableName;
    public $list;
    public $matches;


    /**
     * Сохранить строку сущности.
     *
     * @param mixed $item
     */
    public function save(Record $item)
    {
        $rows = $this->ormObjectsGeneration($item);
        $this->ormObjectsSet($rows, $item);
        var_dump($rows);
        // TODO: Implement save() method.
    }

    public function ormObjectsSet($rows, Record $item){
        foreach ($rows as $objectName => $row){
            foreach ($this->matches[$objectName] as $response_field => $db_field){
                if (is_array($db_field)){
                    $columnName = $db_field[0];
                } else {
                    $columnName = $db_field;
                }
                $method_name = 'set' . ucfirst($this->undescoresToCamelCase($columnName));
                $row['object']->$method_name($item->data[$response_field]);
            }
            $row['object']->save();
        }
    }

    public function ormObjectsGeneration(Record $item){
        $rows = [];
        foreach ($this->matches as $key => $value){
            $rows[$key]['className'] = 'Models\\' . ucfirst($this->undescoresToCamelCase($key));
            $rows[$key]['classQueryName'] = 'Models\\' . ucfirst($this->undescoresToCamelCase($key)) . 'Query';
            foreach ($value as $response_field => $db_field){
                if ($db_field[1] === 'pk'){
                    $method_name = 'findOneBy'.ucfirst($this->undescoresToCamelCase($db_field[0]));
                    $rows[$key]['object'] = $rows[$key]['classQueryName']::create()->$method_name($item->data[$response_field]);
                    if(!isset($rows[$key]['object'])){
                        $rows[$key]['object'] = new $rows[$key]['className']();
                    }
                }
            }
        }

        return $rows;
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