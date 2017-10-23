SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[batches]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[batches](
	[ID] [int] IDENTITY(1,55) NOT NULL,
	[TimeStamp] [int] NOT NULL,
	[DeviceID] [varchar](25) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[BatchText] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Active] [binary](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[boxes]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[boxes](
	[ID] [int] IDENTITY(1,13636) NOT NULL,
	[Token] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Name] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Barcode] [varchar](20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Status] [varchar](1) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[LocationID] [int] NULL,
	[LastActivity] [int] NOT NULL,
	[DestroyDate] [int] NULL,
	[StartedBy] [int] NULL,
	[Comments] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[IronMtnBox] [int] NULL,
	[Inventoried] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[dbo].[boxes]') AND name = N'idxBoxesBarcode')
CREATE UNIQUE NONCLUSTERED INDEX [idxBoxesBarcode] ON [dbo].[boxes] 
(
	[Barcode] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[BoxRequests]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[BoxRequests](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[BoxID] [int] NULL,
	[RequestID] [int] NULL,
	[Status] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[dbo].[FK__BoxReques__BoxID__59FA5E80]') AND parent_object_id = OBJECT_ID(N'[dbo].[BoxRequests]'))
ALTER TABLE [dbo].[BoxRequests]  WITH CHECK ADD FOREIGN KEY([BoxID])
REFERENCES [dbo].[boxes] ([ID])
GO
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[dbo].[FK__BoxReques__Reque__5AEE82B9]') AND parent_object_id = OBJECT_ID(N'[dbo].[BoxRequests]'))
ALTER TABLE [dbo].[BoxRequests]  WITH CHECK ADD FOREIGN KEY([RequestID])
REFERENCES [dbo].[requests] ([ID])
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[currentdb]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[currentdb](
	[Barcode] [varchar](20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Property] [varchar](25) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[BeginDate] [varchar](25) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[EndDate] [varchar](25) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Area] [varchar](250) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Descriptor] [varchar](250) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Comments] [varchar](250) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[TransferDate] [varchar](25) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[TextDesc] [varchar](255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[Barcode] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[departments]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[departments](
	[ID] [int] IDENTITY(1,23) NOT NULL,
	[Name] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Active] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[devices]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[devices](
	[ID] [int] IDENTITY(1,35) NOT NULL,
	[DeviceID] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[DeviceName] [varchar](250) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[ApplicationType] [int] NOT NULL,
	[Token] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[ActivateDate] [int] NOT NULL,
	[Active] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[ActivateDate_def]') AND type = 'D')
BEGIN
ALTER TABLE [dbo].[devices] ADD  CONSTRAINT [ActivateDate_def]  DEFAULT ((-1)) FOR [ActivateDate]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[locations]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[locations](
	[ID] [int] IDENTITY(6000,1) NOT NULL,
	[Barcode] [varchar](20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Name] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[LocationType] [varchar](1) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Warehouse] [int] NOT NULL,
	[Row] [int] NOT NULL,
	[Bay] [int] NOT NULL,
	[Shelf] [int] NOT NULL,
	[Active] [int] NOT NULL,
	[LabelPrinted] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[properties]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[properties](
	[ID] [int] IDENTITY(1,27) NOT NULL,
	[Code] [varchar](3) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Name] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Active] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[records]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[records](
	[ID] [int] IDENTITY(1,14318) NOT NULL,
	[BoxID] [int] NOT NULL,
	[PropertyID] [int] NOT NULL,
	[DepartmentID] [int] NOT NULL,
	[RecordTypeID] [int] NOT NULL,
	[StartDate] [int] NOT NULL,
	[EndDate] [int] NOT NULL,
	[DestructionDate] [int] NOT NULL,
	[ContactPerson] [varchar](250) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Active] [binary](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[dbo].[records]') AND name = N'records_idx01')
CREATE NONCLUSTERED INDEX [records_idx01] ON [dbo].[records] 
(
	[BoxID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[recordtypes]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[recordtypes](
	[ID] [int] IDENTITY(1,86) NOT NULL,
	[Description] [varchar](250) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[RetainFor] [int] NOT NULL,
	[Active] [binary](1) NOT NULL,
	[DepartmentID] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[requests]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[requests](
	[ID] [int] IDENTITY(1,132) NOT NULL,
	[RequestedBy] [int] NOT NULL,
	[RequestTime] [int] NOT NULL,
	[DeliverTo] [varchar](255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Location] [varchar](255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Comments] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Urgency] [int] NOT NULL,
	[BoxData] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[BoxCount] [int] NOT NULL,
	[Status] [int] NULL,
	[DeliveredBy] [int] NOT NULL,
	[DeliverTime] [int] NOT NULL,
	[LastActivity] [int] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[Testdata]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[Testdata](
	[SomeID] [int] NULL,
	[OtherID] [int] NULL,
	[Data] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[transactions]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[transactions](
	[ID] [int] IDENTITY(1,9524) NOT NULL,
	[Barcode] [varchar](20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Location] [varchar](20) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[BatchID] [int] NULL,
	[TimeStamp] [int] NOT NULL,
	[DeviceID] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Active] [binary](1) NOT NULL,
	[Operation] [varchar](30) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[BadgeID] [varchar](100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[DataSourceName] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[DataSourceLine] [int] NULL,
	[ProcessDate] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[usergroups]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[usergroups](
	[ID] [int] IDENTITY(1,21) NOT NULL,
	[GroupName] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Permissions] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Locations] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[RecordTypes] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Properties] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Departments] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[SysAdmin] [binary](1) NOT NULL,
	[Active] [binary](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
IF NOT EXISTS (SELECT * FROM dbo.sysobjects WHERE id = OBJECT_ID(N'[SysAdmin_def]') AND type = 'D')
BEGIN
ALTER TABLE [dbo].[usergroups] ADD  CONSTRAINT [SysAdmin_def]  DEFAULT ((0)) FOR [SysAdmin]
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[users]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[users](
	[ID] [int] IDENTITY(1,25) NOT NULL,
	[EmployeeID] [bigint] NOT NULL,
	[FirstName] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[LastName] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[UserGroupID] [int] NOT NULL,
	[Password] [varchar](max) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[PasswordResetDate] [int] NOT NULL,
	[Active] [binary](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[dbo].[users]') AND name = N'users_idx01')
CREATE NONCLUSTERED INDEX [users_idx01] ON [dbo].[users] 
(
	[EmployeeID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[warehouses]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[warehouses](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[Name] [varchar](50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
	[Active] [binary](1) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[BoxScanData]') AND type in (N'U'))
BEGIN
CREATE TABLE [staging].[BoxScanData](
	[ID] [int] IDENTITY(1,1) NOT NULL,
	[BadgeID] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[LocationBarcode] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[DateTime] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[BoxBarcode] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Start] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[End] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Property] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[BoxOrigin] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Operation] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Contents] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[DataSourceName] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[DataSourceLine] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[ProcessDate] [varchar](1000) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
	[Processed] [bit] NULL
) ON [PRIMARY]
END
GO
