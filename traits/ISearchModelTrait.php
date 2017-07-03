<?php

namespace extpoint\yii2\traits;

use yii\data\ArrayDataProvider;

interface ISearchModelTrait
{
    /**
     * @var ArrayDataProvider
     */
    public function getDataProvider();

    /**
     * @var array
     */
    public function getMeta();

    /**
     * @return array
     */
    public function fields();

    /**
     * @return array
     */
    public function toFrontend();

}
