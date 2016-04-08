<?php

namespace extpoint\yii2\base;

use extpoint\yii2\exceptions\ModelSaveException;

class AppModel extends \yii\db\ActiveRecord {

    /**
     * @param string[]|null $attributeNames
     * @throws ModelSaveException
     */
    public function saveOrPanic($attributeNames = null) {
        if (!$this->save(true, $attributeNames)) {
            throw new ModelSaveException($this);
        }
    }

}
