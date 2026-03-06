-- X-Files Database Schema
-- Compatible with MySQL 8.0+ / MariaDB 10.5+

CREATE DATABASE IF NOT EXISTS `xfiles`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `xfiles`;

-- -----------------------------------------------
-- Table: users (with roles for CMS)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('agent', 'editor', 'admin') NOT NULL DEFAULT 'agent',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`),
    UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: categories (hierarchical with parent)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` INT UNSIGNED NULL DEFAULT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cat_slug` (`slug`),
    KEY `idx_cat_parent` (`parent_id`),
    CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`)
        REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: tags
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `tags` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tag_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: articles (multilingual blog with SEO)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lang` VARCHAR(5) NOT NULL DEFAULT 'en',
    `translation_group` INT UNSIGNED NULL DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `featured_image` VARCHAR(500) NULL,
    `category_id` INT UNSIGNED NULL,
    `author_id` INT UNSIGNED NULL,
    `status` ENUM('draft', 'scheduled', 'published', 'archived') NOT NULL DEFAULT 'draft',
    `seo_title` VARCHAR(255) NULL,
    `meta_description` VARCHAR(320) NULL,
    `seo_slug` VARCHAR(255) NULL,
    `og_title` VARCHAR(255) NULL,
    `og_description` VARCHAR(320) NULL,
    `published_at` DATETIME NULL,
    `scheduled_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_article_slug_lang` (`slug`, `lang`),
    KEY `idx_article_status` (`status`),
    KEY `idx_article_lang` (`lang`),
    KEY `idx_article_translation` (`translation_group`),
    KEY `idx_article_category` (`category_id`),
    KEY `idx_article_author` (`author_id`),
    KEY `idx_article_published` (`published_at`),
    KEY `idx_article_scheduled` (`scheduled_at`),
    CONSTRAINT `fk_article_category` FOREIGN KEY (`category_id`)
        REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_article_author` FOREIGN KEY (`author_id`)
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: article_tags (pivot)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `article_tags` (
    `article_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`article_id`, `tag_id`),
    KEY `idx_at_tag` (`tag_id`),
    CONSTRAINT `fk_at_article` FOREIGN KEY (`article_id`)
        REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_at_tag` FOREIGN KEY (`tag_id`)
        REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: cases (X-Files cases)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `cases` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `case_number` VARCHAR(20) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('open', 'closed', 'critical', 'archived') NOT NULL DEFAULT 'open',
    `threat_level` ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'low',
    `assigned_agent_id` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_case_number` (`case_number`),
    KEY `idx_status` (`status`),
    KEY `idx_assigned_agent` (`assigned_agent_id`),
    CONSTRAINT `fk_cases_agent` FOREIGN KEY (`assigned_agent_id`)
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: evidence
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `evidence` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `case_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `file_path` VARCHAR(500) NULL,
    `evidence_type` ENUM('photo', 'document', 'audio', 'video', 'physical', 'other') NOT NULL DEFAULT 'other',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_case` (`case_id`),
    CONSTRAINT `fk_evidence_case` FOREIGN KEY (`case_id`)
        REFERENCES `cases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Table: case_notes
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `case_notes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `case_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `content` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_case_notes_case` (`case_id`),
    KEY `idx_case_notes_user` (`user_id`),
    CONSTRAINT `fk_notes_case` FOREIGN KEY (`case_id`)
        REFERENCES `cases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Sample data: users
-- -----------------------------------------------
INSERT INTO `users` (`full_name`, `username`, `email`, `password`, `role`) VALUES
('Fox Mulder', 'mulder', 'mulder@fbi.gov', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor'),
('Dana Scully', 'scully', 'scully@fbi.gov', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editor'),
('Walter Skinner', 'skinner', 'skinner@fbi.gov', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Default password for all sample users: "password"

-- -----------------------------------------------
-- Sample data: cases
-- -----------------------------------------------
INSERT INTO `cases` (`case_number`, `title`, `description`, `status`, `threat_level`, `assigned_agent_id`) VALUES
('XF-1013', 'Unexplained lights over Nevada', 'Multiple witnesses report strange lights in formation over Nevada desert.', 'open', 'high', 1),
('XF-1012', 'Missing persons - Pine Barrens', 'Series of disappearances in the Pine Barrens area of New Jersey.', 'closed', 'medium', 2),
('XF-1011', 'Crop circle formation - Kansas', 'Complex geometric crop circle discovered in rural Kansas farmland.', 'open', 'low', 1),
('XF-1010', 'Unidentified signal - SETI array', 'Anomalous repeating signal detected by SETI monitoring station.', 'critical', 'critical', 3),
('XF-1009', 'Government facility break-in', 'Security breach at classified research facility in New Mexico.', 'closed', 'high', 2),
('XF-1008', 'Paranormal activity - Oregon', 'Reports of unexplained phenomena in rural Oregon community.', 'open', 'medium', 1);

-- -----------------------------------------------
-- Sample data: categories (hierarchical)
-- -----------------------------------------------
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`) VALUES
(1, NULL, 'UFO Sightings', 'ufo-sightings', 'Reports and analyses of unidentified flying object encounters worldwide.'),
(2, NULL, 'Government Conspiracies', 'government-conspiracies', 'Investigations into government cover-ups and classified operations.'),
(3, NULL, 'Alien Encounters', 'alien-encounters', 'First-hand accounts and case files of extraterrestrial contact.'),
(4, NULL, 'Paranormal Activity', 'paranormal-activity', 'Unexplained phenomena beyond conventional scientific understanding.'),
(5, NULL, 'Cold Cases', 'cold-cases', 'Unsolved X-Files cases reopened for fresh investigation.'),
(6, 1, 'Military Encounters', 'military-encounters', 'UFO sightings reported by military personnel.'),
(7, 1, 'Civilian Reports', 'civilian-reports', 'UFO sightings reported by civilians.'),
(8, 2, 'Cover-ups', 'cover-ups', 'Documented government cover-up operations.'),
(9, 2, 'Secret Programs', 'secret-programs', 'Classified government research programs.'),
(10, 3, 'Abductions', 'abductions', 'Alien abduction case files.'),
(11, 3, 'Close Encounters', 'close-encounters', 'Close encounters of various kinds.'),
(12, 4, 'Crop Circles', 'crop-circles-cat', 'Crop circle formations and analysis.'),
(13, 4, 'Unexplained Phenomena', 'unexplained-phenomena', 'Other unexplained events.');

-- -----------------------------------------------
-- Sample data: tags
-- -----------------------------------------------
INSERT INTO `tags` (`id`, `name`, `slug`) VALUES
(1, 'UFO', 'ufo'),
(2, 'Roswell', 'roswell'),
(3, 'Area 51', 'area-51'),
(4, 'Abduction', 'abduction'),
(5, 'Cover-up', 'cover-up'),
(6, 'Conspiracy', 'conspiracy'),
(7, 'Evidence', 'evidence'),
(8, 'Classified', 'classified'),
(9, 'Mulder', 'mulder'),
(10, 'Scully', 'scully'),
(11, 'Paranormal', 'paranormal'),
(12, 'Crop Circles', 'crop-circles'),
(13, 'Men in Black', 'men-in-black'),
(14, 'Phoenix Lights', 'phoenix-lights'),
(15, 'Rendlesham', 'rendlesham'),
(16, 'Navy', 'navy'),
(17, 'AATIP', 'aatip'),
(18, 'Pentagon', 'pentagon'),
(19, 'Disclosure', 'disclosure'),
(20, 'Witness', 'witness');

-- -----------------------------------------------
-- Sample data: articles (fan-made, English)
-- -----------------------------------------------
INSERT INTO `articles` (`title`, `slug`, `excerpt`, `content`, `featured_image`, `category_id`, `author_id`, `lang`, `translation_group`, `status`, `seo_title`, `meta_description`, `published_at`) VALUES

('The Roswell Incident: What They Don\'t Want You to Know',
'roswell-incident-what-they-dont-want-you-to-know',
'In the summer of 1947, something crashed in the New Mexico desert. The official story says weather balloon. The evidence says otherwise.',
'<p>On July 8, 1947, the Roswell Army Air Field issued a press release stating they had recovered a &ldquo;flying disc&rdquo; from a ranch near Roswell, New Mexico. Within hours, the story was retracted and replaced with the now-infamous &ldquo;weather balloon&rdquo; explanation.</p>
<p>But Agent Fox Mulder&rsquo;s investigation has uncovered documents suggesting a far more complex truth. Classified memos from the era reference &ldquo;non-terrestrial materials&rdquo; and &ldquo;biological specimens&rdquo; transported to Wright-Patterson Air Force Base in Ohio.</p>
<h3>The Evidence</h3>
<p>Multiple witnesses, including Major Jesse Marcel, the intelligence officer who first inspected the debris, maintained until their deaths that the materials were unlike anything manufactured on Earth. Marcel described metallic fragments that could not be bent, burned, or cut, and strange hieroglyphic-like symbols etched into I-beam structures.</p>
<p>In 1994, the Air Force released a report attributing the debris to Project Mogul, a classified program using high-altitude balloons to monitor Soviet nuclear tests. But this explanation fails to account for the numerous eyewitness testimonies of unusual materials and the intense military response.</p>
<h3>The Cover-Up</h3>
<p>Perhaps most telling is the government&rsquo;s own behavior. Why would a simple weather balloon require such an elaborate cover story? Why were witnesses threatened with imprisonment? Why were death certificates of the alleged alien bodies classified for over 50 years?</p>
<p>The truth about Roswell remains one of the most significant unsolved cases in X-Files history. As Agent Mulder often reminds us: the answers are out there, if we have the courage to look.</p>',
'roswell.jpg', 1, 1, 'en', 1, 'published',
'The Roswell Incident 1947 - X-Files Investigation',
'Detailed investigation into the 1947 Roswell UFO crash. Classified evidence, eyewitness testimonies, and government cover-up analysis.',
'2026-01-15 10:00:00'),

('Area 51: Inside America\'s Most Secret Base',
'area-51-inside-americas-most-secret-base',
'Deep in the Nevada desert lies a facility the government denied existed for decades. What are they hiding behind those restricted boundaries?',
'<p>For decades, the United States government refused to acknowledge the existence of Area 51, a highly classified Air Force facility within the Nevada Test and Training Range. It wasn&rsquo;t until 2013 that the CIA officially confirmed its existence through declassified documents.</p>
<h3>What We Know</h3>
<p>Officially, Area 51 is used for the development and testing of experimental aircraft and weapons systems. The U-2 spy plane, SR-71 Blackbird, and F-117 Nighthawk were all developed and tested here. But former employees and leaked documents suggest far more exotic programs.</p>
<p>Bob Lazar, a physicist who claims to have worked at the facility in the late 1980s, alleges that Area 51 houses recovered extraterrestrial spacecraft and that scientists there were engaged in reverse-engineering alien technology. While his claims remain controversial, certain technical details he provided have been corroborated by subsequent revelations.</p>
<h3>The Restricted Zone</h3>
<p>The base is surrounded by thousands of acres of restricted airspace and ground area. Motion sensors, security cameras, and armed guards patrol the perimeter. Trespassers face arrest and heavy fines.</p>
<p>Agent Scully has noted that the level of security surrounding Area 51 far exceeds what would be necessary for conventional military testing. &ldquo;You don&rsquo;t need this level of secrecy for experimental aircraft,&rdquo; she observed. &ldquo;You need it when the truth would fundamentally alter public perception of reality.&rdquo;</p>
<h3>Recent Developments</h3>
<p>In recent years, satellite imagery has revealed massive construction projects at the facility, including new hangars large enough to house aircraft significantly larger than anything in the current military inventory. The questions continue to multiply, and the answers remain locked behind the most secure fences in America.</p>',
'area51.jpg', 9, 2, 'en', 2, 'published',
'Area 51 Secrets Revealed - X-Files Investigation',
'Inside America''s most secret military base. Classified programs, reverse-engineered technology, and the truth about Area 51.',
'2026-01-22 14:30:00'),

('The Phoenix Lights: Mass UFO Sighting Over Arizona',
'phoenix-lights-mass-ufo-sighting-arizona',
'On March 13, 1997, thousands of people across Arizona witnessed a massive V-shaped formation of lights traverse the night sky.',
'<p>It remains one of the largest mass UFO sightings in recorded history. On the evening of March 13, 1997, a formation of lights appeared over the American Southwest, traversing a 300-mile corridor from Nevada through Arizona.</p>
<h3>The Witnesses</h3>
<p>Thousands of people &mdash; including the Governor of Arizona, Fife Symington &mdash; reported seeing either a massive V-shaped craft or a series of stationary lights in the sky. Witnesses described the object as enormous, some estimating it to be over a mile wide, completely silent, and moving at a slow, deliberate pace.</p>
<p>&ldquo;I&rsquo;m a pilot and I know just about every machine that flies,&rdquo; said truck driver Bill Greiner. &ldquo;It was bigger than anything that I&rsquo;ve ever seen. It just couldn&rsquo;t have been from this planet.&rdquo;</p>
<h3>The Official Response</h3>
<p>The U.S. Air Force eventually attributed the lights to flares dropped by A-10 Warthog aircraft during training exercises at the Barry Goldwater Range. However, this explanation only accounts for the second set of lights &mdash; not the massive V-shaped formation reported hours earlier.</p>
<p>Governor Symington initially mocked the sightings at a press conference. Years later, he admitted he had also witnessed the lights and described the experience as &ldquo;otherworldly.&rdquo;</p>
<h3>X-Files Analysis</h3>
<p>Agent Mulder&rsquo;s analysis draws attention to the military&rsquo;s delayed response and the deliberate conflation of two separate events. &ldquo;They used the flares to create plausible deniability for something they couldn&rsquo;t explain,&rdquo; he argues. &ldquo;Classic misdirection.&rdquo;</p>
<p>The Phoenix Lights remain an open case in the X-Files division.</p>',
'phoenix-lights.jpg', 7, 1, 'en', 3, 'published',
'Phoenix Lights 1997 - Mass UFO Sighting Investigation',
'Investigation into the 1997 Phoenix Lights mass UFO sighting. Thousands of witnesses, military cover-up, and unexplained aerial phenomena.',
'2026-02-03 09:00:00'),

('The Rendlesham Forest Incident: Britain\'s Roswell',
'rendlesham-forest-incident-britains-roswell',
'In December 1980, US military personnel at RAF Woodbridge encountered a craft of unknown origin in Rendlesham Forest.',
'<p>Often called &ldquo;Britain&rsquo;s Roswell,&rdquo; the Rendlesham Forest incident is one of the best-documented UFO encounters in history, involving multiple trained military observers over several nights.</p>
<h3>Night One: December 26, 1980</h3>
<p>Security patrolmen at RAF Woodbridge spotted unusual lights descending into nearby Rendlesham Forest. Sergeant Jim Penniston and Airman First Class John Burroughs found a triangular craft approximately 3 meters tall, sitting on the forest floor. It was smooth, black, and warm to the touch with hieroglyphic-like symbols on its surface.</p>
<h3>Night Two: December 28, 1980</h3>
<p>Lieutenant Colonel Charles Halt led a larger team into the forest, carrying a micro-cassette recorder that created a real-time audio document of the investigation. On the recording, Halt describes a pulsing red light that moved through the trees and split into multiple objects. His team detected radiation readings up to 10 times normal background levels.</p>
<h3>The Aftermath</h3>
<p>Despite the involvement of a high-ranking officer and multiple credible witnesses, the British Ministry of Defence dismissed the incident as having &ldquo;no defence significance.&rdquo;</p>
<p>Agent Scully has reviewed the radiation data and confirmed the readings are consistent with exposure to an unknown energy source. &ldquo;The physical evidence here is unusually strong,&rdquo; she noted. &ldquo;Whatever was in that forest left measurable traces that defy conventional explanation.&rdquo;</p>',
'rendlesham.jpg', 6, 2, 'en', 4, 'published',
'Rendlesham Forest UFO Incident 1980 - X-Files Report',
'The Rendlesham Forest incident: Britain''s most significant UFO encounter with military witnesses and physical evidence.',
'2026-02-10 11:00:00'),

('Crop Circles: Messages From Beyond or Elaborate Hoaxes?',
'crop-circles-messages-from-beyond',
'Complex geometric patterns appearing overnight in fields around the world continue to baffle researchers.',
'<p>Every summer, intricate patterns appear in crop fields across the globe &mdash; predominantly in southern England, but increasingly worldwide. These formations, some spanning hundreds of feet and displaying extraordinary mathematical precision, have sparked decades of debate.</p>
<h3>Beyond Simple Circles</h3>
<p>Modern crop formations bear little resemblance to the simple circles first reported in the 1970s. Today&rsquo;s patterns incorporate complex fractals, sacred geometry, and even apparent responses to messages sent into space. The Chilbolton formation of 2001, which appeared to reply to the Arecibo message, remains one of the most controversial.</p>
<p>Genuine formations show stalks bent at the nodes rather than broken &mdash; requiring precise application of heat. The soil often shows altered crystalline structures and elevated radiation levels.</p>
<h3>X-Files Field Research</h3>
<p>Agent Mulder has catalogued over 200 formations exhibiting characteristics inconsistent with human creation: formations appearing within impossibly short timeframes, those with no entry paths, and those displaying intentionally encoded mathematical relationships.</p>
<p>&ldquo;Someone &mdash; or something &mdash; is trying to communicate,&rdquo; Mulder insists. &ldquo;And until we decode the message, we won&rsquo;t understand the urgency behind it.&rdquo;</p>',
'crop-circle.jpg', 12, 1, 'en', 5, 'published',
'Crop Circles Investigation - Alien Messages or Hoaxes?',
'Investigation into crop circle phenomena. Scientific analysis, mathematical patterns, and the debate between alien communication and human hoaxes.',
'2026-02-18 08:00:00'),

('The Men in Black: Who Are They Really?',
'men-in-black-who-are-they-really',
'Eyewitnesses of UFO encounters frequently report visits from mysterious men in dark suits who warn them to stay silent.',
'<p>Since the 1950s, UFO witnesses have reported encounters with strange men in black suits who appear shortly after a sighting. These &ldquo;Men in Black&rdquo; deliver warnings, confiscate evidence, and then vanish. The real MIB reports are far more disturbing than any Hollywood portrayal.</p>
<h3>The Pattern</h3>
<p>MIB encounters share consistent characteristics across decades and continents. The men arrive in black sedans &mdash; often pristine vintage models. They wear identical black suits, speak in a flat, emotionless manner, and display uncanny knowledge of the witness&rsquo;s personal details.</p>
<p>Many witnesses describe them as looking &ldquo;not quite right&rdquo; &mdash; unnaturally pale skin, odd proportions, or a mechanical quality to their movements.</p>
<h3>Case File: Albert Bender</h3>
<p>The first widely reported MIB encounter involved Albert Bender, founder of the International Flying Saucer Bureau, in 1953. Three men in dark suits visited him and revealed a truth so terrifying it caused him to immediately shut down his organization.</p>
<h3>Government Agents or Something Else?</h3>
<p>Agent Mulder has documented 47 MIB encounters. &ldquo;The consistency of reports across time and geography suggests either a remarkably well-coordinated operation or a phenomenon that defies our understanding,&rdquo; he observes.</p>',
'men-in-black.jpg', 2, 1, 'en', 6, 'published',
'Men in Black - Real MIB Encounters Investigated',
'Who are the Men in Black? Investigation into real MIB encounters, witness testimonies, and the connection to UFO cover-ups.',
'2026-02-25 15:00:00'),

('The Nimitz Encounter: When the Navy Met the Unknown',
'nimitz-encounter-navy-met-unknown',
'In 2004, USS Nimitz fighter pilots encountered an object that defied the laws of physics. The Pentagon confirmed the footage is authentic.',
'<p>On November 14, 2004, the USS Nimitz Carrier Strike Group was conducting training exercises off Southern California when radar operators detected anomalous objects. What followed became the most significant military UFO encounter of the 21st century.</p>
<h3>The Encounter</h3>
<p>Commander David Fravor and Lieutenant Commander Alex Dietrich were vectored to investigate. They found a white, oblong object &mdash; approximately 40 feet long &mdash; hovering over an ocean disturbance. The &ldquo;Tic Tac&rdquo; had no visible wings, rotors, or propulsion system.</p>
<p>When Fravor descended, the object mirrored his movements then accelerated away at impossible speed. &ldquo;It accelerated like nothing I&rsquo;ve ever seen,&rdquo; Fravor stated.</p>
<h3>The FLIR Footage</h3>
<p>Pilot Chad Underwood captured infrared footage &mdash; the famous &ldquo;FLIR1&rdquo; video. In 2020, the Department of Defense officially released the footage confirming its authenticity.</p>
<h3>Analysis</h3>
<p>Agent Scully&rsquo;s analysis: &ldquo;The object demonstrated capabilities exceeding our understanding by several generations. Hypersonic speeds without sonic boom, instantaneous direction changes exceeding 600 G, and no detectable propulsion signature.&rdquo;</p>
<p>The question is no longer whether these objects exist &mdash; it&rsquo;s where they come from.</p>',
'ufo-nimitz.jpg', 6, 2, 'en', 7, 'published',
'USS Nimitz UFO Encounter 2004 - Pentagon Confirmed',
'The 2004 USS Nimitz UFO encounter: Pentagon-confirmed footage of an object defying known physics. Full investigation report.',
'2026-03-01 12:00:00'),

('Government UFO Programs: From Blue Book to AATIP',
'government-ufo-programs-blue-book-to-aatip',
'For decades, the US government secretly studied UFOs under classified programs. The recently revealed AATIP is just the latest chapter.',
'<p>The United States government&rsquo;s involvement with UFO investigation spans over seven decades, from Project Sign in 1947 to the All-domain Anomaly Resolution Office (AARO).</p>
<h3>Project Blue Book (1952-1969)</h3>
<p>The Air Force&rsquo;s Project Blue Book investigated 12,618 UFO sightings over 17 years. Of these, 701 were classified as &ldquo;unidentified.&rdquo; The Robertson Panel recommended in 1953 that Blue Book focus on debunking rather than investigating.</p>
<h3>AATIP and Beyond (2007-Present)</h3>
<p>Senator Harry Reid secured $22 million for the Advanced Aerospace Threat Identification Program, run by Luis Elizondo. When Elizondo resigned in 2017 to protest excessive secrecy, he brought the Navy UAP videos to public attention, triggering ongoing congressional investigations.</p>
<h3>What We&rsquo;ve Learned</h3>
<p>Every generation discovers that the previous one lied about the extent of government UFO knowledge. As Agent Mulder notes, &ldquo;The conspiracy isn&rsquo;t about hiding aliens. It&rsquo;s about hiding the fact that we&rsquo;ve known about them all along.&rdquo;</p>',
'conspiracy.jpg', 9, 1, 'en', 8, 'published',
'Government UFO Programs - Blue Book to AATIP Investigation',
'History of US government UFO investigation programs from Project Blue Book to AATIP. Classified programs and the fight for disclosure.',
'2026-03-05 10:00:00');

-- -----------------------------------------------
-- Article-tag associations
-- -----------------------------------------------
INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
(1, 2), (1, 7), (1, 5), (1, 8),
(2, 3), (2, 6), (2, 8), (2, 5),
(3, 1), (3, 14), (3, 7), (3, 5), (3, 20),
(4, 1), (4, 15), (4, 7), (4, 8), (4, 16),
(5, 12), (5, 11), (5, 7),
(6, 13), (6, 6), (6, 5), (6, 11),
(7, 1), (7, 7), (7, 10), (7, 8), (7, 16),
(8, 6), (8, 5), (8, 8), (8, 9), (8, 17), (8, 18), (8, 19);
