IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spImportDestroyData]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[spImportDestroyData]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spImportInventoryData]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[spImportInventoryData]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spImportMoveData]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[spImportMoveData]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spUpdateBoxRequests]') AND type in (N'P', N'PC'))
DROP PROCEDURE [dbo].[spUpdateBoxRequests]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[spStageDestroyData]') AND type in (N'P', N'PC'))
DROP PROCEDURE [staging].[spStageDestroyData]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[spStageInventoryData]') AND type in (N'P', N'PC'))
DROP PROCEDURE [staging].[spStageInventoryData]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[spStageMoveData]') AND type in (N'P', N'PC'))
DROP PROCEDURE [staging].[spStageMoveData]
GO
