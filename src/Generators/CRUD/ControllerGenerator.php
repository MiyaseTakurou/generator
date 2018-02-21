<?php
namespace LaravelRocket\Generator\Generators\CRUD;

use LaravelRocket\Generator\Objects\Column;
use function ICanBoogie\pluralize;

class ControllerGenerator extends CRUDBaseGenerator
{
    /**
     * @return array
     */
    protected function getVariables(): array
    {
        $modelName                       = $this->getModelName();
        $variables                       = $this->getFillableColumns();
        $variables['modelName']          = $modelName;
        $variables['variableName']       = camel_case($modelName);
        $variables['viewName']           = pluralize(kebab_case($modelName));
        $variables['pluralVariableName'] = pluralize(camel_case($modelName));
        $variables['className']          = $modelName.'Repository';
        $variables['tableName']          = $this->table->getName();

        return $variables;
    }

    protected function getFillableColumns()
    {
        $columnInfo = [
            'fillableColumns'      => [],
            'timestampColumns'     => [],
            'unixTimestampColumns' => [],
            'booleanColumns'       => [],
            'fileColumns'          => [],
            'imageColumns'         => [],
        ];

        $excludes = ['id', 'remember_token', 'created_at', 'deleted_at', 'updated_at'];

        foreach ($this->table->getColumns() as $column) {
            $name = $column->getName();
            $type = $column->getType();

            $columnDefinition             = $this->json->getColumnDefinition($this->table->getName(), $column->getName());
            $columnObject                 = new Column($column);
            list($ediFieldType, $options) = $columnObject->getEditFieldType([], $columnDefinition);

            if (in_array($name, $excludes)) {
                continue;
            }
            if ($ediFieldType === 'image') {
                $columnInfo['imageColumns'][] = $name;
                continue;
            }
            if ($ediFieldType === 'file') {
                $columnInfo['fileColumns'][] = $name;
                continue;
            }
            if (ends_with($name, '_at') && ($type === 'timestamp' || $type === 'timestamp_f')) {
                $columnInfo['timestampColumns'][] = $name;
                continue;
            }
            if (ends_with($name, '_at') && $type === 'int') {
                $columnInfo['unixTimestampColumns'][] = $name;
                continue;
            }
            if ((starts_with($name, 'is_') || starts_with($name, 'has_')) && $type === 'int') {
                $columnInfo['booleanColumns'][] = $name;
                continue;
            }

            $columnInfo['fillableColumns'][] = $name;
        }

        return $columnInfo;
    }
}
