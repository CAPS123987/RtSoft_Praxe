-- Přidání oprávnění deleteUser do tabulky permissions
-- Spustit: make migrate-delete-user
-- Nebo ručně v phpMyAdmin (http://localhost:8090):
INSERT INTO `permissions` (`name`) SELECT 'deleteUser' FROM DUAL
    WHERE NOT EXISTS (SELECT 1 FROM `permissions` WHERE `name` = 'deleteUser');

-- Po přidání je nutné přiřadit oprávnění admin roli:
-- Admin → Seznam rolí → Upravit roli → zaškrtnout checkbox "deleteUser" → Upravit
