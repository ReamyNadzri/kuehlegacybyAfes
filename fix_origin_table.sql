-- Fix ORIGIN table column names to match application code
-- Run this script to update the database schema

USE kuehlegacy;

-- Drop the foreign key constraint temporarily
ALTER TABLE kueh DROP FOREIGN KEY FK_KUEH_ORIGIN;

-- Rename the columns in ORIGIN table
ALTER TABLE origin 
  CHANGE COLUMN ORIGINID ORIGINCODE INT(11) NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN ORIGINNAME NAMESTATE VARCHAR(100) DEFAULT NULL;

-- Recreate the foreign key constraint with the correct column name
ALTER TABLE kueh 
  ADD CONSTRAINT FK_KUEH_ORIGIN 
  FOREIGN KEY (ORIGINID) REFERENCES origin (ORIGINCODE);

-- Verify the changes
DESCRIBE origin;
SHOW CREATE TABLE kueh;
