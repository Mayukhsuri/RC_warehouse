IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = N'staging')
EXEC sys.sp_executesql N'CREATE SCHEMA [staging] AUTHORIZATION [dbo]'

GO
