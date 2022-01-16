<?php

/*
 * Osclass - software for creating and publishing online classified advertising platforms
 * Maintained and supported by Mindstellar Community
 * https://github.com/mindstellar/Osclass
 * Copyright (c) 2021.  Mindstellar
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *                     GNU GENERAL PUBLIC LICENSE
 *                        Version 3, 29 June 2007
 *
 *  Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
 *  Everyone is permitted to copy and distribute verbatim copies
 *  of this license document, but changing it is not allowed.
 *
 *  You should have received a copy of the GNU Affero General Public
 *  License along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Model database for Currency table
 *
 * @package    Osclass
 * @subpackage Model
 * @since      unknown
 */
class ShopCurrency extends DAO
{
    /**
     * It references to self object: Currency.
     * It is used as a singleton
     *
     * @access private
     * @since  unknown
     * @var Currency
     */
    private static $instance;
    private $currencies;
    private $default;

    /**
     * Set data related to t_currency table
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTableName('t_shop_currencies');
        $this->setPrimaryKey('pk_c_code');
        $this->setFields(array('pk_c_code', 'i_rate'));
        $this->currencies = array();
        $this->default = Session::newInstance()->_get('currency');
        if (empty($this->default )) {
            $this->default = osc_currency();
        }
    }

    /**
     * It creates a new Currency object class ir if it has been created
     * before, it return the previous object
     *
     * @access public
     * @return Currency
     * @since  unknown
     */
    public static function newInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    
    public function getDefault() {
        return $this->default;
    }

    public function defaultSymbol() {
        return $this->currencies[$this->default]['s_symbol'];
    }

    public function setDefault($curr) {
        $oldCurr = $this->default;
        $this->default = $curr;
        Session::newInstance()->_set('currency', $curr);
        
        ShopCart::newInstance()->convertCart($oldCurr, $curr);
        
    }

    public function formatRate($rate) {
        return number_format($rate/1000000,6,'.',',');
    }
    
    public function updateRate($curr, $rate, $symbol) {
        $this->dao->replace( $this->getTableName(), 
                array('fk_c_code'  => $curr, 'i_rate' => $rate*1000000, 's_symbol'=>$symbol));

    }
    
    public function convert($from, $amount, $to) {
        if (empty($from) || empty($to) || $amount == 0 || ($from == $this->default && $to == $this->default)) {
            return $amount;
        }
        if ($from == $this->default) {
            $calc = $amount / ($this->currencies[$to]['i_rate'] / 1000000);
            return intval($calc);
        }
        $calc = $amount * ($this->currencies[$from]['i_rate']
                / $this->currencies[$to]['i_rate']);
        return intval($calc);
    }
    
    public function item_price($item) {
        $curr = $item['fk_c_currency_code'];
        if (!isset($curr)) {
            $curr = $this->default;
        }
        if (!isset($item['i_price'])) {
            $item['i_price'] = 0;
        }
        if (isset($item['currency'][$this->default])) {
            $price = $item['currency'][$this->default];
        } else {
            $price = $this->convert($curr,$item['i_price'],$this->default);
        }
        return $price;
    }
    
    public function formatted_price($item = null) {
        return osc_format_price($this->item_price($item), $this->currencies[$this->default]['s_symbol']);
    }
    
    public function format_price($price, $symbol = null) {
        if ($symbol == null) {
            $symbol = osc_item_currency_symbol();
        }

        $price /= 1000000;

        $currencyFormat = osc_locale_currency_format();
        $currencyFormat = str_replace(
            '{NUMBER}',
            number_format($price, osc_locale_num_dec(), osc_locale_dec_point(), osc_locale_thousands_sep()),
            $currencyFormat
        );
        $currencyFormat = str_replace('{CURRENCY}', $symbol, $currencyFormat);

        return osc_apply_filter('item_price', $currencyFormat);
        
    }
    
    public function deleteItem($itemId) {
        $this->dao->query(sprintf("DELETE FROM %st_item_prices WHERE fk_i_item_id = '$itemId'", DB_TABLE_PREFIX));
    }

    public function getTableNamePrices() {
        return sprintf('%st_item_prices',DB_TABLE_PREFIX);
    }

    public function getAll($refresh = false)
    {
        if (!$refresh && count($this->currencies) > 0) {
            return $this->currencies;
        }

        $rootCurrencies = Currency::newInstance()->getTableName();
        $this->dao->select();
        $this->dao->from($rootCurrencies);
        $this->dao->join($this->getTableName(),$this->getTableName().".fk_c_code = ".$rootCurrencies.".pk_c_code", 'LEFT') ;
        $result = $this->dao->get();

        if ($result === false) {
            return false;
        }

        foreach ($result->resultArray() as $row) {
            $this->currencies[$row['pk_c_code']] = $row;
            if (empty($row['i_rate']) || $row['i_rate'] == 1) {
                $this->currencies[$row['pk_c_code']]['i_rate'] = 1000000;
            }
        }

        return $this->currencies;
    }

    /**
     * @param string $value
     *
     * @return bool|mixed
     */
    public function findByPrimaryKey($value)
    {
        if (isset($this->currencies[$value])) {
            return $this->currencies[$value];
        }

        $this->dao->select($this->fields);
        $this->dao->from($this->getTableName());
        $this->dao->where($this->getPrimaryKey(), $value);
        $result = $this->dao->get();

        if ($result === false) {
            return false;
        }

        if ($result->numRows() !== 1) {
            return false;
        }

        $this->currencies[$value] = $result->row();

        return $this->currencies[$value];
    }

    public function findPrices($itemIds) {
        $this->dao->select();
        $this->dao->from($this->getTableNamePrices());
        $this->dao->whereIn('fk_i_item_id', $itemIds);

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->resultArray();
    }
    
    public function updateItemPrices($item, $prices) {
        foreach ($prices as $price) {
            if ($price['price'] == 0) {
                $this->dao->query(sprintf("DELETE FROM %st_item_prices where fk_i_item_id = %d and fk_c_code = '%s'", DB_TABLE_PREFIX, $item['pk_i_id'],$price['code']));
            } else {
                if (!$this->dao->update( $this->getTableNamePrices(), 
                        array('i_price' => $price['price']), 
                        array('fk_i_item_id' => $item['pk_i_id'], 'fk_c_code'=>$price['code']))) {
                    // update fail, so try insert
                    $this->dao->insert( $this->getTableNamePrices(), 
                        array('fk_i_item_id' => $item['pk_i_id'], 'fk_c_code'=>$price['code'], 'i_price' => $price['price']));
                }
            }
        }        
    }

}
