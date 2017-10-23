IF  EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vwBoxHistory]'))
DROP VIEW [dbo].[vwBoxHistory]
GO
IF  EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vwBoxInventory]'))
DROP VIEW [dbo].[vwBoxInventory]
GO
IF  EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vwWarehouseBoxPickList]'))
DROP VIEW [dbo].[vwWarehouseBoxPickList]
GO
