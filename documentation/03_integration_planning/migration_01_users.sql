-- =====================================================
-- DATABASE MIGRATION SCRIPT - Phase 1: User Migration  
-- =====================================================
-- Purpose: Migrate web application users to unified backend database
-- Source: astacalarescue database (penggunas + admins tables)
-- Target: astacala_rescue database (users table)
-- Date: August 3, 2025
-- Status: DEVELOPMENT - DO NOT RUN IN PRODUCTION

-- =====================================================
-- PRE-MIGRATION VALIDATION
-- =====================================================

-- Check source database connection
SELECT 'Source Database Check' AS step, COUNT(*) AS pengguna_count 
FROM astacalarescue.penggunas;

SELECT 'Source Database Check' AS step, COUNT(*) AS admin_count 
FROM astacalarescue.admins;

-- Check target database connection
SELECT 'Target Database Check' AS step, COUNT(*) AS current_users 
FROM astacala_rescue.users;

-- =====================================================
-- BACKUP CREATION
-- =====================================================

-- Create backup of target users table
CREATE TABLE IF NOT EXISTS astacala_rescue.users_backup_pre_migration AS 
SELECT * FROM astacala_rescue.users;

-- Create migration tracking table
CREATE TABLE IF NOT EXISTS astacala_rescue.user_migration_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_table ENUM('penggunas', 'admins') NOT NULL,
    source_id INT NOT NULL,
    target_user_id BIGINT,
    source_username VARCHAR(255),
    target_email VARCHAR(255),
    migration_status ENUM('success', 'failed', 'duplicate') NOT NULL,
    error_message TEXT,
    migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_source (source_table, source_id),
    INDEX idx_target (target_user_id)
);

-- =====================================================
-- DATA VALIDATION BEFORE MIGRATION
-- =====================================================

-- Check for potential username conflicts
SELECT 'Conflict Check' AS step, 'penggunas' AS source_table, 
       username_akun_pengguna, COUNT(*) as count
FROM astacalarescue.penggunas 
GROUP BY username_akun_pengguna 
HAVING COUNT(*) > 1;

SELECT 'Conflict Check' AS step, 'admins' AS source_table, 
       username_akun_admin, COUNT(*) as count
FROM astacalarescue.admins 
GROUP BY username_akun_admin 
HAVING COUNT(*) > 1;

-- Check for existing emails in target database that might conflict
SELECT 'Email Conflict Check' AS step, 
       p.username_akun_pengguna as web_username,
       u.email as backend_email
FROM astacalarescue.penggunas p
JOIN astacala_rescue.users u ON CONCAT(p.username_akun_pengguna, '@web.local') = u.email;

-- =====================================================
-- MIGRATION EXECUTION - PENGGUNAS (VOLUNTEERS)
-- =====================================================

-- Migrate penggunas to users table
INSERT INTO astacala_rescue.users (
    name, 
    email, 
    phone, 
    role, 
    is_active,
    created_at, 
    updated_at
)
SELECT 
    COALESCE(NULLIF(TRIM(nama_lengkap_pengguna), ''), 'Web User') AS name,
    CONCAT(username_akun_pengguna, '@web.local') AS email,
    CASE 
        WHEN TRIM(no_handphone_pengguna) != '' THEN no_handphone_pengguna
        ELSE NULL 
    END AS phone,
    'VOLUNTEER' AS role,
    1 AS is_active,
    COALESCE(created_at, NOW()) AS created_at,
    COALESCE(updated_at, NOW()) AS updated_at
FROM astacalarescue.penggunas
WHERE username_akun_pengguna IS NOT NULL 
  AND TRIM(username_akun_pengguna) != ''
  AND NOT EXISTS (
      SELECT 1 FROM astacala_rescue.users 
      WHERE email = CONCAT(username_akun_pengguna, '@web.local')
  );

-- Log penggunas migration
INSERT INTO astacala_rescue.user_migration_log (
    source_table, source_id, target_user_id, source_username, target_email, migration_status
)
SELECT 
    'penggunas' AS source_table,
    p.id AS source_id,
    u.id AS target_user_id,
    p.username_akun_pengguna AS source_username,
    u.email AS target_email,
    'success' AS migration_status
FROM astacalarescue.penggunas p
JOIN astacala_rescue.users u ON u.email = CONCAT(p.username_akun_pengguna, '@web.local')
WHERE u.role = 'VOLUNTEER';

-- =====================================================
-- MIGRATION EXECUTION - ADMINS 
-- =====================================================

-- Migrate admins to users table
INSERT INTO astacala_rescue.users (
    name, 
    email, 
    phone, 
    role, 
    is_active,
    created_at, 
    updated_at
)
SELECT 
    COALESCE(NULLIF(TRIM(nama_lengkap_admin), ''), 'Web Admin') AS name,
    CONCAT(username_akun_admin, '@admin.local') AS email,
    CASE 
        WHEN TRIM(no_handphone_admin) != '' THEN no_handphone_admin
        ELSE NULL 
    END AS phone,
    'ADMIN' AS role,
    1 AS is_active,
    COALESCE(created_at, NOW()) AS created_at,
    COALESCE(updated_at, NOW()) AS updated_at
FROM astacalarescue.admins
WHERE username_akun_admin IS NOT NULL 
  AND TRIM(username_akun_admin) != ''
  AND NOT EXISTS (
      SELECT 1 FROM astacala_rescue.users 
      WHERE email = CONCAT(username_akun_admin, '@admin.local')
  );

-- Log admins migration
INSERT INTO astacala_rescue.user_migration_log (
    source_table, source_id, target_user_id, source_username, target_email, migration_status
)
SELECT 
    'admins' AS source_table,
    a.id AS source_id,
    u.id AS target_user_id,
    a.username_akun_admin AS source_username,
    u.email AS target_email,
    'success' AS migration_status
FROM astacalarescue.admins a
JOIN astacala_rescue.users u ON u.email = CONCAT(a.username_akun_admin, '@admin.local')
WHERE u.role = 'ADMIN';

-- =====================================================
-- POST-MIGRATION VALIDATION
-- =====================================================

-- Validate migration results
SELECT 'Migration Summary' AS step, 
       source_table, 
       migration_status, 
       COUNT(*) as count 
FROM astacala_rescue.user_migration_log 
GROUP BY source_table, migration_status;

-- Show new users added
SELECT 'New Users Added' AS step, 
       id, name, email, role, created_at
FROM astacala_rescue.users 
WHERE email LIKE '%@web.local' OR email LIKE '%@admin.local'
ORDER BY created_at DESC;

-- Verify total user count
SELECT 'Final Count' AS step, 
       role, 
       COUNT(*) as user_count 
FROM astacala_rescue.users 
GROUP BY role;

-- Check for any migration errors
SELECT 'Migration Errors' AS step, 
       source_table, 
       source_username, 
       error_message 
FROM astacala_rescue.user_migration_log 
WHERE migration_status = 'failed';

-- =====================================================
-- ROLLBACK SCRIPT (IF NEEDED)
-- =====================================================

/*
-- EMERGENCY ROLLBACK - Remove migrated users
DELETE FROM astacala_rescue.users 
WHERE email LIKE '%@web.local' OR email LIKE '%@admin.local';

-- Restore from backup
INSERT INTO astacala_rescue.users 
SELECT * FROM astacala_rescue.users_backup_pre_migration;

-- Clear migration log
DROP TABLE IF EXISTS astacala_rescue.user_migration_log;
*/

-- =====================================================
-- MIGRATION COMPLETION
-- =====================================================

SELECT 'MIGRATION COMPLETE' AS status, 
       NOW() AS completed_at,
       'Ready for Phase 2: Report Migration' AS next_step;
