IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[batches]') AND type in (N'U'))
DROP TABLE [dbo].[batches]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[boxes]') AND type in (N'U'))
DROP TABLE [dbo].[boxes]
GO
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[dbo].[FK__BoxReques__BoxID__59FA5E80]') AND parent_object_id = OBJECT_ID(N'[dbo].[BoxRequests]'))
ALTER TABLE [dbo].[BoxRequests] DROP CONSTRAINT [FK__BoxReques__BoxID__59FA5E80]
GO
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[dbo].[FK__BoxReques__Reque__5AEE82B9]') AND parent_object_id = OBJECT_ID(N'[dbo].[BoxRequests]'))
ALTER TABLE [dbo].[BoxRequests] DROP CONSTRAINT [FK__BoxReques__Reque__5AEE82B9]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[BoxRequests]') AND type in (N'U'))
DROP TABLE [dbo].[BoxRequests]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[currentdb]') AND type in (N'U'))
DROP TABLE [dbo].[currentdb]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[departments]') AND type in (N'U'))
DROP TABLE [dbo].[departments]
GO
IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[ActivateDate_def]') AND type = 'D')
BEGIN
ALTER TABLE [dbo].[devices] DROP CONSTRAINT [ActivateDate_def]
END

GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[devices]') AND type in (N'U'))
DROP TABLE [dbo].[devices]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[locations]') AND type in (N'U'))
DROP TABLE [dbo].[locations]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[properties]') AND type in (N'U'))
DROP TABLE [dbo].[properties]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[records]') AND type in (N'U'))
DROP TABLE [dbo].[records]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[recordtypes]') AND type in (N'U'))
DROP TABLE [dbo].[recordtypes]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[requests]') AND type in (N'U'))
DROP TABLE [dbo].[requests]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Testdata]') AND type in (N'U'))
DROP TABLE [dbo].[Testdata]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[transactions]') AND type in (N'U'))
DROP TABLE [dbo].[transactions]
GO
IF  EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[SysAdmin_def]') AND type = 'D')
BEGIN
ALTER TABLE [dbo].[usergroups] DROP CONSTRAINT [SysAdmin_def]
END

GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[usergroups]') AND type in (N'U'))
DROP TABLE [dbo].[usergroups]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[users]') AND type in (N'U'))
DROP TABLE [dbo].[users]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[warehouses]') AND type in (N'U'))
DROP TABLE [dbo].[warehouses]
GO
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[BoxScanData]') AND type in (N'U'))
DROP TABLE [staging].[BoxScanData]
GO
