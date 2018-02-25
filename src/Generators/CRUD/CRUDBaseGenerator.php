<?php
namespace LaravelRocket\Generator\Generators\CRUD;

use LaravelRocket\Generator\Generators\TableBaseGenerator;

class CRUDBaseGenerator extends TableBaseGenerator
{
    protected function canGenerate(): bool
    {
        foreach ($this->excludePostfixes as $excludePostfix) {
            if (ends_with($this->table->getName(), $excludePostfix)) {
                return false;
            }
        }

        if( !$this->rebuild ){
            $path = $this->getPath();
            if( !empty($path) && file_exists($path) ){
                return false;
            }
        }

        return !$this->detectRelationTable($this->table);
    }
}
