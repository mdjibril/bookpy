ALTER TABLE `bookings`
ADD COLUMN `cancellation_token` VARCHAR(255) NULL DEFAULT NULL AFTER `status`,
ADD UNIQUE INDEX `cancellation_token_unique` (`cancellation_token`);
