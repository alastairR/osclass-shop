<?php

class ShopDownloads extends DAO {

    private static $instance;

    public static function newInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {
        parent::__construct();
        $this->setTableName('t_item_files');
        $this->setPrimaryKey('pk_i_id');
        $this->setFields(array('fk_i_item_id', 's_name', 's_code', 'i_downloads'));
    }

    public function getTableShopDownloads() {
        return sprintf('%st_shop_downloads', DB_TABLE_PREFIX);
    }
    
    public function getFileByItemCode($item, $code) {
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_item_id', $item);
        $this->dao->where('s_code', $code);

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->row();
    }
    
    public function deleteFile($id, $code) {
        $dg = $this->getFile($id);
        @unlink(osc_get_preference('upload_path', 'shop') . $dg['s_name']);
        $this->dao->query(sprintf("DELETE FROM %s WHERE pk_i_id = %d and s_code = '%s'", $this->getTableName(), $id, $code));
    }
    
    public function deleteItem($itemId) {
        $dg = $this->getFile($itemId);
        @unlink(osc_get_preference('upload_path', 'shop') . $dg['s_name']);
        $this->dao->query(sprintf("DELETE FROM %s WHERE fk_i_item_id = '$itemId'", $this->getTableName()));
    }

    public function findFiles($itemIds) {
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->whereIn('fk_i_item_id', $itemIds);

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->resultArray();
    }
    
    public function checkDigital($itemIds, $all = false) {
        $this->dao->select("COUNT(DISTINCT fk_i_item_id) as num_items");
        $this->dao->from($this->getTableName());
        $this->dao->whereIn('fk_i_item_id', $itemIds);

        $result = $this->dao->get();
        if (!$result) {
            return false;
        }
        $num_items = $result->row()['num_items'];
        if ($all) {
            return ($num_items >= count($itemIds));
        } else {
            return ($num_items > 0);
        }
    }
    
    public function getFile($id) {
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->where('pk_i_id', $id);

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->row();
    }

    public function getFilesFromItem($itemId) {
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->where('fk_i_item_id', $itemId);

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->result();
    }

    public function getAllFiles() {
        $this->dao->select();
        $this->dao->from($this->getTableName());

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->result();
    }

    public function getStats() {
        $result = $this->dao->query(sprintf("SELECT * FROM %st_item_files f, %st_item_downloads d WHERE d.fk_i_file_id = f.pk_i_id ORDER BY f.fk_i_item_id ASC, f.pk_i_id ASC", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
        if (!$result) {
            return array();
        }

        return $result->result();
    }

    public function removeAllFiles() {
        $dgs = $this->getAllFiles();
        foreach ($dgs as $dg) {
            @unlink(osc_get_preference('upload_path', 'shop') . $dg['s_name']);
            @rmdir(osc_get_preference('upload_path', 'shop'));
        }
        $this->dao->query('DROP TABLE %s', $this->getTableName());
    }

    public function removeItem($id, $code) {
        $dgs = $this->getFilesFromItem($itemId);
        foreach ($dgs as $dg) {
            @unlink(osc_get_preference('upload_path', 'shop') . $dg['s_name']);
            $sql = sprintf("DELETE FROM %s WHERE fk_i_id = %d", $this->getTableName(), $dg['pk_i_id']);
            if (!empty($code)) {
                $sql .= sprintf(" and s_code = '%s'",$code);
            }
            $this->dao->query($sql);
        }
    }

    public function insertFile($itemId, $filename, $date) {
        $downloadDays = osc_get_preference('downloadDays', 'shop');
        $aSet = array();
        $aSet['fk_i_item_id'] = $itemId;
        $aSet['s_name'] = $filename;
        $aSet['s_code'] = osc_genRandomPassword();
        return $this->dao->insert($this->getTableName(), $aSet);
    }

    public function updateDownloads($id, $downloads) {
        $this->dao->from($this->getTableName());
        $this->dao->set(array('i_downloads' => $downloads));
        $this->dao->where(array('pk_i_id' => $id));
        return $this->dao->update();
    }
    
    public function updateUserDownloads($id, $set) {
        $this->dao->from($this->getTableShopDownloads());
        $this->dao->set($set);
        $this->dao->where(array('pk_i_id' => $id));
        return $this->dao->update();
    }
    
    public function logSale($userId, $itemId, $invoiceId) {
        $downloadDays = osc_get_preference('downloadDays', 'shop');
        $set = array('fk_i_user_id'=>$userId, 'fk_i_item_id'=>$itemId, 
            'fk_i_invoice_id' => $invoiceId, 's_secret'=> osc_genRandomPassword(20),
            'i_download_count'=>0, 'dt_expires'=> date('Y-m-d H:i:s', strtotime(' + '. $downloadDays .' days')));
        $this->dao->insert($this->getTableShopDownloads(), $set);
    }

    public function getDownloadStats($userId) {
        $this->dao->select('count(*) as total, sum(i_download_count) as times');
        $this->dao->from($this->getTableShopDownloads());
        $this->dao->where('fk_i_user_id', $userId);

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->row();
    }

    public function getAvailableDownloads($userId, $all = false) {
        $maxDownloads = osc_get_preference('maxDownloads', 'shop');
        $downloadDays = osc_get_preference('downloadDays', 'shop');
        $this->dao->select();
        $this->dao->from($this->getTableName());
        $this->dao->join($this->getTableShopDownloads(),$this->getTableName().'.fk_i_item_id = '.$this->getTableShopDownloads().'.fk_i_item_id');
        $this->dao->where('fk_i_user_id', $userId);
        if (!empty($downloadDays) && !$all) {
            $this->dao->where('dt_purchase > DATE(NOW() - INTERVAL '.$downloadDays.' DAY)');
        }
        if (!empty($maxDownloads) && !$all) {
            $this->dao->where('i_download_count < '.$maxDownloads);
        }

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->resultArray();
    }

    public function getDownloadByItemSecret($itemId, $secret, $code) {
        $this->dao->select('sd.*, fi.fk_i_item_id, fi.s_name, fi.pk_i_id as fileId, fi.i_downloads, i.i_price');
        $this->dao->from($this->getTableName().' fi');
        $this->dao->join(Item::newInstance()->getTableName().' i','i.pk_i_id =  fi.fk_i_item_id','LEFT');
        $this->dao->join($this->getTableShopDownloads().' sd','fi.fk_i_item_id =  sd.fk_i_item_id','LEFT');
        $this->dao->where('fi.fk_i_item_id', $itemId);
        $this->dao->where('sd.s_secret', $secret);
        $this->dao->orWhere('fi.s_code', $code);

        $result = $this->dao->get();
        if (!$result) {
            return array();
        }

        return $result->row();
    }
    
    public function resetLimit($invoiceId) {
        ShopOrders::newInstance()->setOrderStatus($invoiceId, SHOP_ORDER_PAID);
        $downloadDays = osc_get_preference('downloadDays', 'shop');
        $set = array('i_download_count' => 0, 'dt_expires'=>date('Y-m-d H:i:s', strtotime(' + '. $downloadDays .' days')));
        return $this->dao->update($this->getTableShopDownloads(), $set, array('fk_i_invoice_id'=>$invoiceId));        
    }
}
