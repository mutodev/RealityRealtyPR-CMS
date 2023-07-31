<?php

/**
 * Contract
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Contract extends BaseContract
{
    public function getSaleCommissionCalculated() {
        if ($this->status !== 'Closed') {
            return 0;
        }

        if ($this->sale_commission === 'Percentage') {
            return $this->sale_agreed * ($this->sale_value / 100);
        }

        if ($this->sale_commission === 'Fixed') {
            return $this->sale_value;
        }

        return 0;
    }
}