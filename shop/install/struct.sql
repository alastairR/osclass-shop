CREATE TABLE /*TABLE_PREFIX*/t_item_files (
    pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT UNSIGNED NOT NULL,
    s_name VARCHAR(250) NOT NULL,
    s_code VARCHAR(14) NOT NULL,
    i_downloads INT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (pk_i_id),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_item_prices (
    pk_i_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT UNSIGNED NOT NULL,
    fk_c_code VARCHAR(3) NOT NULL,
    i_price INT UNSIGNED NOT NULL,
        PRIMARY KEY (pk_i_id),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_shop_currencies (
    fk_c_code char(3) NOT NULL,
    s_symbol varchar(5) NOT NULL,
    i_rate INT UNSIGNED,
        PRIMARY KEY (fk_c_code)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
ALTER TABLE /*TABLE_PREFIX*/t_shop_currencies
  ADD KEY `shop_cart_user` (`fk_c_code`) USING BTREE;

CREATE TABLE /*TABLE_PREFIX*/t_shop_orders (
    pk_i_id int unsigned NOT NULL AUTO_INCREMENT,
    fk_i_invoice_id INT NOT NULL,
    dt_order datetime NOT NULL CURRENT_TIMESTAMP,
    i_status int NOT NULL,
    s_email varchar(100),
    s_name varchar(100),
    s_company varchar(100),
    s_country varchar(40),
    s_address varchar(100),
    s_city_area varchar(100),
    s_city varchar(100),
    s_zip varchar(15),
    s_region varchar(100),
    s_delivery_country varchar(40),
    s_delivery_address varchar(100),
    s_delivery_city_area varchar(100),
    s_delivery_city varchar(100),
    s_delivery_zip varchar(15),
    s_delivery_region varchar(100),
    s_billing_country varchar(40),
    s_billing_address varchar(100),
    s_billing_city_area varchar(100),
    s_billing_city varchar(100),
    s_billing_zip varchar(15),
    s_billing_region varchar(100),
    s_currency_code varchar(5),
    s_shipping_method varchar(5),
    dt_despatched DATETIME,
    i_amount INT,
    i_shipping INT,
    i_amount_tax INT,
    i_amount_total INT,
        PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
ALTER TABLE /*TABLE_PREFIX*/t_shop_orders
  ADD KEY `shop_order_customer` (`s_email`) USING BTREE;

CREATE TABLE /*TABLE_PREFIX*/t_shop_order_history (
    pk_i_id INT unsigned NOT NULL AUTO_INCREMENT,
    fk_i_invoice_id INT NOT NULL,
    dt_status datetime NOT NULL CURRENT_TIMESTAMP,
    s_status varchar(50),
    b_notified tinyint NOT NULL,
    s_comment varchar(255),
        PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
ALTER TABLE /*TABLE_PREFIX*/t_shop_order_history
  ADD KEY `shop_order_history` (`fk_i_order_id`) USING BTREE;

CREATE TABLE /*TABLE_PREFIX*/t_shop_downloads (
    pk_i_id INT unsigned NOT NULL AUTO_INCREMENT,
    fk_i_user_id INT NOT NULL,
    fk_i_item_id INT NOT NULL,
    fk_i_invoice_id INT NOT NULL,
    s_secret varchar(50),
    dt_purchase DATETIME NOT NULL CURRENT_TIMESTAMP,
    dt_expires DATETIME NOT NULL CURRENT_TIMESTAMP,
    i_download_count INT NOT NULL,
        PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
ALTER TABLE /*TABLE_PREFIX*/t_shop_downloads
  ADD KEY `shop_downloads` (`fk_i_user_id`) USING BTREE;

CREATE TABLE /*TABLE_PREFIX*/t_user_addresses (
    pk_i_id INT unsigned NOT NULL AUTO_INCREMENT,
    fk_i_user_id INT NOT NULL,
    s_type varchar(10),
    s_name varchar(100),
    s_company varchar(100),
    s_country varchar(40),
    s_address varchar(100),
    s_city_area varchar(100),
    s_city varchar(100),
    s_zip varchar(15),
    s_region varchar(100),
        PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
ALTER TABLE /*TABLE_PREFIX*/t_user_addresses
  ADD KEY `user_address_user` (`fk_i_user_id`) USING BTREE;

CREATE TABLE /*TABLE_PREFIX*/t_user_cart (
    pk_i_id int unsigned NOT NULL AUTO_INCREMENT,
    fk_i_item_id INT UNSIGNED,
    s_source VARCHAR(5),
    s_product_type varchar(30),
    s_description VARCHAR(100),
    i_quantity INT UNSIGNED,
    i_price INT UNSIGNED,
    fk_i_user_id INT UNSIGNED,
        PRIMARY KEY (pk_i_id)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';
ALTER TABLE /*TABLE_PREFIX*/t_user_cart
  ADD KEY `user_cart_user` (`fk_i_user_id`) USING BTREE;

