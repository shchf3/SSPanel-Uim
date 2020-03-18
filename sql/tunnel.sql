ALTER TABLE `ss_node`
	ADD COLUMN `tunnel_server` VARCHAR(128) NULL DEFAULT '' AFTER `mu_only`;
