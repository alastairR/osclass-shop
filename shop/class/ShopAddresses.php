<?php

class ShopAddresses extends DAO {

    /**
     * It references to self object: ModelCart.
     * It is used as a singleton
     * 
     * @access private
     * @since 3.0
     * @var Currency
     */
    private static $instance;

    public static function newInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Construct
     */
    function __construct() {
        parent::__construct();
        $this->setTableName('t_user_addresses');
        $this->setPrimaryKey('pk_i_id');
        $array_fields = array(
            'pk_i_id',
            'fk_i_user_id',
            's_type',
            's_name',
            's_company',
            's_country',
            's_address',
            's_city_area',
            's_city',
            's_zip',
            's_region');
        $this->setFields($array_fields);
    }

    public function findByUserId($userId) {
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_user_id', $userId);
        $result = $this->dao->get();
        if ($result == false) {
            return array();
        }

        return $result->resultArray();
    }
    
    function deleteUser($userId) {
        $this->dao->query(sprintf("DELETE FROM %st_user_addresses WHERE fk_i_user_id = '$userId'", DB_TABLE_PREFIX));
    }
    
    public function format($address) {
        $company = '';
        if (isset($address['s_company'])) {
            $company = $address['s_company'];
        }
        $addr = array_filter(array($address['s_name'], $company, $address['s_address'], 
                        $address['s_city_area'], $address['s_city'], $address['s_region'], 
                        $address['s_zip'], $address['s_country']));
        $addr = implode(', ', $addr);
        
        return $addr;
    }

    public function getUserAddresses($userId)
    {
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_user_id', $userId) ;
        $result = $this->dao->get();

        if ($result === false) {
            return array();
        }

        return $result->resultArray();
    }

    public function updateAddress ($addrId, $addr) {
        $set = array('fk_i_user_id'=>$addr['userId'], 's_type'=>$addr['type'], 's_name'=>$addr['name'],
            's_company'=>$addr['company'], 's_country'=> $addr['country'], 's_address'=>$addr['address'],
            's_city_area'=>$addr['suburb'], 's_city'=>$addr['city'], 's_zip'=>$addr['postcode'],
            's_region'=>$addr['state']);
        if (empty($addrId) || $this->update($set, array('pk_i_id'=>$addrId)) == false) {
            $this->insert($set);
        }
    }

    public function deleteAddress ($addrId) {
        $this->delete(array('pk_i_id'=>$addrId));
    }

}
