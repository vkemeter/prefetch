CREATE TABLE pages (
    tx_prefetch_enable tinyint(1) DEFAULT '0' NOT NULL,
    tx_prefetch_type tinyint(1) DEFAULT '0' NOT NULL,
    tx_prefetch_eagerness tinyint(1) DEFAULT '0' NOT NULL
);
