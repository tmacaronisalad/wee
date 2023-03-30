<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
CREATE TABLE IF NOT EXISTS `ns_file` (
	`idx` int(4) unsigned NOT NULL AUTO_INCREMENT,
	`category` enum('INLINE', 'IMAGE', 'FILE') NOT NULL,
	`file_key` char(32) NOT NULL,
	`file_name` char(128) NOT NULL,
	`file_path` char(128) NOT NULL,
	`file_url` char(128) NOT NULL,
	`file_type` char(16) NOT NULL,
	`file_size` char(16) NOT NULL,
	`orig_name` char(128) NOT NULL,
	`reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY (`idx`),
	KEY `key_file` (`file_key`, `category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
*/
class File_model extends NS_Model
{
	public function __construct()
	{
		$this->table = 'ns_file';
	}

	public function deleteItem($in_data)
	{
		$items = parent::getItems($in_data);
		foreach ($items as $item) {
			@unlink($item->file_path.$item->file_name);
			@unlink($item->file_path.'crop/'.$item->file_name);
			@unlink($item->file_path.'thumb/'.$item->file_name);
			@unlink($item->file_path.'small/'.$item->file_name);
		}

		return parent::deleteItem($in_data);
	}
}
