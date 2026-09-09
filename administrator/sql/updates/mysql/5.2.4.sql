-- JMM 5.2.4 Update Schema - Add layout_type, chart_type and custom_css to templates

ALTER TABLE `#__jmm_templates` ADD COLUMN `layout_type` varchar(20) NOT NULL DEFAULT 'table' AFTER `title`;
ALTER TABLE `#__jmm_templates` ADD COLUMN `chart_type` varchar(20) NOT NULL DEFAULT 'PieChart' AFTER `layout_type`;
ALTER TABLE `#__jmm_templates` ADD COLUMN `custom_css` text DEFAULT NULL AFTER `chart_type`;

UPDATE `#__jmm_templates` SET `layout_type` = 'table' WHERE `title` IN ('default', 'table', 'liste-societes', 'liste-secteurs', 'liste-coeurdevillage', 'liste-tourismeetpatrimoine', 'jtable', 'jtable2');
UPDATE `#__jmm_templates` SET `layout_type` = 'cards' WHERE `title` IN ('cards', 'blog');
UPDATE `#__jmm_templates` SET `layout_type` = 'chart' WHERE `title` IN ('chart', 'camembert', 'piechart', 'camenbert-totaux');