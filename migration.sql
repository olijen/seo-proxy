 CREATE TABLE IF NOT EXISTS `seo_proxy` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
   `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `seo_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `seo_h1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   `seo_text` text COLLATE utf8_unicode_ci NOT NULL,
   `seo_description` text COLLATE utf8_unicode_ci NOT NULL,
   `seo_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Defoult table for SeoProxy class (by olijen)'  AUTO_INCREMENT=3 ;
