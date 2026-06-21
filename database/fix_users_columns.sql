ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `google_id` varchar(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `email_verified_at` timestamp NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `remember_token` varchar(100) DEFAULT NULL;
