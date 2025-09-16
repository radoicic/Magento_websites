<?php

/**
 * Product:       Xtento_XtCore (2.3.0)
 * ID:            ujUJn/Vth6o24dYqG/9u9lsOOEMJEJPHsCxn5DbLMtc=
 * Packaged:      2018-10-11T07:25:23+00:00
 * Last Modified: 2017-08-16T08:52:13+00:00
 * File:          app/code/Xtento/XtCore/Model/ResourceModel/Config/Collection.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\ResourceModel\Config;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Xtento\XtCore\Model\Config', 'Xtento\XtCore\Model\ResourceModel\Config');
    }
}
