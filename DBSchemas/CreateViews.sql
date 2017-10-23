SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vwBoxHistory]'))
EXEC dbo.sp_executesql @statement = N'


CREATE VIEW [dbo].[vwBoxHistory]
AS

SELECT --TOP 1000 
      T.[Barcode]
      ,IsNull(L.[Name],''Location Unknown'') as Location
      ,dbo.unix2SQLTime(T.[TimeStamp]) as TimeStamp
      ,case IsNull(T.[Active],0)
            when 1 then ''Active''
            Else ''Inactive''
        end as Active
      ,IsNull(T.[Operation], ''Unknown Operation'') as Operation
      ,IsNull(T.[BadgeID], ''Unknown Handler'') as LastHandler
  FROM [dbo].[transactions] T left outer join dbo.Locations L on T.Location = L.Barcode

' 
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vwBoxInventory]'))
EXEC dbo.sp_executesql @statement = N'
CREATE VIEW [dbo].[vwBoxInventory]
AS

SELECT TOP 1000 
      B.[Name]
      ,B.[Barcode] as BoxBarcode
      ,dbo.unix2sqltime(B.[LastActivity]) as LastActivity
      ,B.[Comments]
      ,L.[Name] as Location
      ,W.[Name] as Warehouse
      ,L.[Row]
      ,L.[Bay]
      ,L.[Shelf]
      ,P.[Name] as Department
      ,RT.[Description] as RecordType
      ,dbo.unix2sqltime(R.[StartDate]) as StartDate
      ,dbo.unix2sqltime(R.[EndDate]) as EndDate

  FROM [dbo].[boxes] B join [dbo].[records] R on B.ID = R.BoxID
        join dbo.Properties P on R.[PropertyID] = P.ID
        join dbo.Departments D on R.[DepartmentID] = D.ID
        join dbo.RecordTypes RT on R.[RecordTypeID] = RT.ID
        join [dbo].[Locations] L on B.LocationID = L.ID
        join dbo.Warehouses W on L.[Warehouse] = W.ID
  where --B.[Status] = ''W'' and
        B.[Inventoried] = 1



' 
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vwWarehouseBoxPickList]'))
EXEC dbo.sp_executesql @statement = N'

CREATE VIEW [dbo].[vwWarehouseBoxPickList]
AS

select 
      Req.[ID] as RequestID
      ,case Req.[Status]
            WHEN -1 THEN ''Canceled''
            WHEN 0 THEN ''On-Hold''
            WHEN 1 THEN ''Standard''
            WHEN 2 THEN ''Urgent''
            WHEN 3 THEN ''Pulling''
            WHEN 4 THEN ''Enroute''
            WHEN 7 THEN ''Delivered''
            WHEN 8 THEN ''Return Requested''
            WHEN 9 THEN ''Complete''
        ELSE ''Unknown''
        END as ReqStatus
      ,users.[FirstName] + '' '' + users.[LastName] as RequestBy
      ,dbo.Unix2SqlTime(req.[RequestTime]) as RequestTime
      ,loc.[Barcode] LocationBarcode
      ,whs.[Name] as Warehouse
      ,loc.[Row]
      ,loc.[Bay]
      ,loc.[Shelf]
      ,loc.[Name] LocationName
      ,box.[Barcode] BoxBarcode
      ,case BoxReq.Status 
            WHEN -1 THEN ''Canceled''
            WHEN 0 THEN ''On-Hold''
            WHEN 1 THEN ''Standard''
            WHEN 2 THEN ''Urgent''
            WHEN 3 THEN ''Pulling''
            WHEN 4 THEN ''Enroute''
            WHEN 7 THEN ''Delivered''
            WHEN 8 THEN ''Return Requested''
            WHEN 9 THEN ''Complete''
        ELSE ''Unknown''
        END as [BoxStatus]

  FROM dbo.[BoxRequests] BoxReq join [dbo].requests Req on BoxReq.RequestID = Req.ID
        join [dbo].[boxes] Box on BoxReq.BoxID = Box.ID
        join [dbo].[locations] Loc on Box.locationID = Loc.ID
        join [dbo].[users] Users on Users.[EmployeeID] = Req.RequestedBy
        left join [dbo].[warehouses] Whs on Loc.Warehouse = Whs.ID
where Box.Status = ''W''
  
;
                         


  
  















' 
GO
