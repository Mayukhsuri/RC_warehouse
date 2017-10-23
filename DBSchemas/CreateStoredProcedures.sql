SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spImportDestroyData]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'
-- ======================================================================
-- Author: Thomas O''Connor 
--
-- Create date: 8/25/17 
--
-- Description: Import Box Destroy data into the box database.  Unlike the
-- import of move data, we don''t assume the box actually exists here.  Like 
-- the inventory, we use a merge statement to add a new box entry if not 
-- already exists.
--
-- Status values:
--      Box Table:
--          [W]arehouse -- Shelved in a warehouse
--          [C]ourier -- In the posession of a courier, in transit from one location to another
--          [O]utlet -- At an outlet, some place other than a warehouse
--          [P]ending -- In the process of being created, not yet finalized
--          [T]emporary -- In a temporary staging/processing area, usually at a warehouse
--          [D]estroyed -- Sent off for destruction
--      Location Table:
--          W - Warehouse, shelving area
--          C - Courier -- Deprecated, we aren''t considering people as locations, 
--              we can track this by the Badge ID from the scan
--          O - Outlet
--          T - Temporary - temporary outlet, staging area, usually in a warehouse
--      Request Statuses
--          REQUEST_CANCELED,-1
--          REQUEST_HOLD,0
--          REQUEST_STANDARD,1
--          REQUEST_URGENT,2
--          REQUEST_PULLING'',3
--          REQUEST_ENROUTE,4
--          REQUEST_DELIVERED,7
--          REQUEST_RETURN_REQUEST,8
--          REQUEST_COMPLETE,9


-- ======================================================================
CREATE PROCEDURE [dbo].[spImportDestroyData]
AS
BEGIN
--execute [dbo].[spImportDestroyData]

--
-- Because new transactions could be being added while we are executing
-- this procedure, for data integrety purposes, we get a snapshot of new 
-- scan data, and use this for the remainder of the procedure.
--
declare @NewTransaction Table (ID int, Timestamp datetime, Operation varchar(50), BoxBarcode varchar(50));

INSERT INTO @NewTransaction (ID, Timestamp, Operation, BoxBarcode) 
  select ID, DateTime, Operation, BoxBarcode from [staging].[BoxScanData] 
  where IsNull(Processed,0) = 0 AND
        Operation = ''Destroy''
;

--
-- When destroying a box, there isn''t a warehouse location, but we have (hopefully) 
-- defined a destroy location.  Get the highest ID of said destroy location
--
declare @DestroyLocationID int;
select @DestroyLocationID = max(ID) from dbo.locations where locationtype = ''D'';

--
-- Add new transactions to the transaction table (de-dupe on on 
-- timestamp, location and barcode)
--

INSERT INTO [dbo].[transactions] (
        [Barcode]
        ,[Location]
        ,[BatchID]
        ,[TimeStamp]
        ,[DeviceID]
        ,[Active]
        ,[Operation]
        ,[BadgeID]
        ,[DataSourceName]
        ,[DataSourceLine]
        ,[ProcessDate]
) 

SELECT 
        S.[BoxBarcode] as Barcode
        ,0 as Location                               -- Destroyed boxes don''t have a location
        ,null as BatchID                                -- Null because this is deprecated with batch loading
        ,dbo.sql2unixTime(S.[DateTime]) as [TimeStamp]
        ,null as DeviceID                               -- Null because this is deprecated with batch loading
        ,0 as Active
        ,S.[Operation]
        ,S.[BadgeID] as BadgeID
        ,S.[DataSourceName]
        ,S.[DataSourceLine]
        ,Convert(DateTime, S.[ProcessDate], 21) as ProcessDate
  -- the following Join limits selection to only what is matched in the NewTransaction table variable
  FROM [staging].[BoxScanData] S join @NewTransaction N on S.ID = N.ID 
  -- De-dupe: Don''t insert duplicate rows from a prior run
  WHERE NOT EXISTS(SELECT ID FROM [dbo].[transactions] T
                    where T.Barcode  = S.[BoxBarcode]
                        and  T.[TimeStamp] = dbo.sql2unixTime(S.[DateTime])
                        and  T.[BadgeID] = S.[BadgeID]
                    )
;

--
-- If the box exists, we need to update it.  If it doesn''t exist, we create it.
-- 

MERGE [dbo].[boxes] DEST --as in the destination
using (	

        -- 
        -- This select retrieves the current status from the last BoxScanData transaction.
        --
        SELECT  
                null as [Token]
                ,left(SD.[BoxOrigin] + '' - '' + SD.[Contents] + '' - '' + SD.[Start] + '' - '' + SD.[End], 49) as [Name]
                ,SD.[BoxBarcode] as [Barcode]
                ,''D'' as [Status] -- assuming if we are inventoring, it is in the Warehouse
                ,@DestroyLocationID  as [LocationID] -- The LocationID of a hopefully active "destroy" field location
                ,dbo.sql2unixTime(SD.[DateTime]) as [LastActivity]
                ,dbo.sql2unixTime(SD.[DateTime]) as [DestroyDate]
                ,SD.[BadgeID] as [StartedBy]
                ,''Destroy entry Barcode Scan'' as [Comments]
                ,null as [IronMtnBox]
                ,1 as [Inventoried]
        FROM [staging].[BoxScanData] SD  
        where SD.ID in ( SELECT LATEST.ID
                        FROM (
                                SELECT BoxBarcode, MAX(TimeStamp) as DateTime
                                FROM @NewTransaction
                                GROUP BY BoxBarcode
                            ) LS join @NewTransaction LATEST on LATEST.BoxBarcode = LS.BoxBarcode and LATEST.TimeStamp = LS.DateTime -- LT as in Last Transaction
                        )

    ) as SRC on SRC.Barcode = DEST.Barcode
		
WHEN MATCHED THEN
  UPDATE
  SET 
      DEST.[Status] =           SRC.[Status]
      ,DEST.[LocationID] =      SRC.[LocationID]
      ,DEST.[LastActivity] =    SRC.[LastActivity]
      ,DEST.[Inventoried] =     SRC.[Inventoried]
      ,DEST.[DestroyDate] =     SRC.[Inventoried]
      ,DEST.[Comments] =        SRC.[Comments]
	
WHEN NOT MATCHED BY TARGET THEN
    INSERT (
        [Token]
        ,[Name]
        ,[Barcode]
        ,[Status]
        ,[LocationID]
        ,[LastActivity]
        ,[DestroyDate]
        ,[StartedBy]
        ,[Comments]
        ,[IronMtnBox]
        ,[Inventoried]
) VALUES (
        SRC.[Token]
        ,SRC.[Name]
        ,SRC.[Barcode]
        ,SRC.[Status]
        ,SRC.[LocationID]
        ,SRC.[LastActivity]
        ,SRC.[DestroyDate]
        ,SRC.[StartedBy]
        ,SRC.[Comments]
        ,SRC.[IronMtnBox]
        ,SRC.[Inventoried]
)
   output $action , inserted.*
          ;


--
-- Finally, we mark these BoxScanData records as processed so we don''t redo them next run
--
update [staging].[BoxScanData] set [Processed] = 1 where ID in (select ID from @NewTransaction);



END
--execute  [dbo].[ImportMovementData]
' 
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spImportInventoryData]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'
-- ======================================================================
-- Author: Thomas O''Connor 
--
-- Create date: 8/25/17 
--
-- Description: Import Box Inventory data into the box database.  Unlike the
-- import of move data, we don''t assume the box actually exists here.  
-- We use a merge statement to address the situation, updating if the 
-- box exists, creating if it doesn''t.
--
-- Status values:
--      Box Table:
--          [W]arehouse -- Shelved in a warehouse
--          [C]ourier -- In the posession of a courier, in transit from one location to another
--          [O]utlet -- At an outlet, some place other than a warehouse
--          [P]ending -- In the process of being created, not yet finalized
--          [T]emporary -- In a temporary staging/processing area, usually at a warehouse
--          [D]estroyed -- Sent off for destruction
--      Location Table:
--          W - Warehouse, shelving area
--          C - Courier -- Deprecated, we aren''t considering people as locations, 
--              we can track this by the Badge ID from the scan
--          O - Outlet
--          T - Temporary - temporary outlet, staging area, usually in a warehouse
--      Request Statuses
--          REQUEST_CANCELED,-1
--          REQUEST_HOLD,0
--          REQUEST_STANDARD,1
--          REQUEST_URGENT,2
--          REQUEST_PULLING'',3
--          REQUEST_ENROUTE,4
--          REQUEST_DELIVERED,7
--          REQUEST_RETURN_REQUEST,8
--          REQUEST_COMPLETE,9


-- ======================================================================
CREATE PROCEDURE [dbo].[spImportInventoryData]
AS
BEGIN
--execute [dbo].[spImportInventoryData]

--
-- Because new transactions could be being added while we are executing
-- this procedure, for data integrety purposes, we get a snapshot of new 
-- scan data, and use this for the remainder of the procedure.
--
declare @NewTransaction Table (ID int, Timestamp datetime, Operation varchar(50), BoxBarcode varchar(50));

INSERT INTO @NewTransaction (ID, Timestamp, Operation, BoxBarcode) 
  select ID, DateTime, Operation, BoxBarcode from [staging].[BoxScanData] 
  where IsNull(Processed,0) = 0 AND
        Operation = ''Inventory''
;

--
-- Add new transactions to the transaction table (de-dupe on on 
-- timestamp, location and barcode)
--
INSERT INTO [dbo].[transactions] (
        [Barcode]
        ,[Location]
        ,[BatchID]
        ,[TimeStamp]
        ,[DeviceID]
        ,[Active]
        ,[Operation]
        ,[BadgeID]
        ,[DataSourceName]
        ,[DataSourceLine]
        ,[ProcessDate]
) 

SELECT 
        S.[BoxBarcode] as Barcode
        ,s.[LocationBarcode] as Location
        ,null as BatchID                                -- Null because this is deprecated with batch loading
        ,dbo.sql2unixTime(S.[DateTime]) as [TimeStamp]
        ,null as DeviceID                               -- Null because this is deprecated with batch loading
        ,1 as Active
        ,S.[Operation]
        ,S.[BadgeID] as BadgeID
        ,S.[DataSourceName]
        ,S.[DataSourceLine]
        ,Convert(DateTime, S.[ProcessDate], 21) as ProcessDate
  -- the following Join limits selection to only what is matched in the NewTransaction table variable
  FROM [staging].[BoxScanData] S join @NewTransaction N on S.ID = N.ID 
  -- De-dupe: Don''t insert duplicate rows from a prior run
  WHERE NOT EXISTS(SELECT ID FROM [dbo].[transactions] T
                    where T.Barcode  = S.[BoxBarcode]
                        and  T.[TimeStamp] = dbo.sql2unixTime(S.[DateTime])
                        and  T.[Location] = S.[LocationBarcode]
                    )
;

--
-- We need to update the box status based on the latest transaction for that box.  
-- We rely on the timestamp of the transaction to determine the latest transaction, 
-- not the order in which data necessarily comes in.
-- 

MERGE [dbo].[boxes] DEST --as in the destination
using (	

        -- 
        -- This select retrieves the current status from the last BoxScanData transaction.
        --
        SELECT  
                null as [Token]
                ,left(SD.[BoxOrigin] + '' - '' + SD.[Contents] + '' - '' + SD.[Start] + '' - '' + SD.[End], 49) as [Name]
                ,SD.[BoxBarcode] as [Barcode]
                ,''W'' as [Status] -- assuming if we are inventoring, it is in the Warehouse
                ,LOC.ID as [LocationID]
                --,null as [LocationID] -- Destroyed boxes don''t have a location
                ,dbo.sql2unixTime(SD.[DateTime]) as [LastActivity]
                ,null as [DestroyDate]
                ,SD.[BadgeID] as [StartedBy]
                ,''Added by Inventory'' as [Comments]
                ,null as [IronMtnBox]
                ,1 as [Inventoried]
      FROM [staging].[BoxScanData] SD  join [dbo].[locations] LOC on SD.[LocationBarcode] = LOC.[Barcode]
        where SD.ID in ( SELECT LATEST.ID
                        FROM (
                                SELECT BoxBarcode, MAX(TimeStamp) as DateTime
                                FROM @NewTransaction
                                GROUP BY BoxBarcode
                            ) LS join @NewTransaction LATEST on LATEST.BoxBarcode = LS.BoxBarcode and LATEST.TimeStamp = LS.DateTime -- LT as in Last Transaction
                        )

    ) as SRC on SRC.Barcode = DEST.Barcode
		
WHEN MATCHED THEN
  UPDATE
  SET 
      DEST.[Status] =           SRC.[Status]
      ,DEST.[LocationID] =      SRC.[LocationID]
      ,DEST.[LastActivity] =    SRC.[LastActivity]
      ,DEST.[Inventoried] =     SRC.[Inventoried]
      ,DEST.[DestroyDate] =     SRC.[Inventoried]
      ,DEST.[Comments] =        SRC.[Comments]
	
WHEN NOT MATCHED BY TARGET THEN
    INSERT (
        [Token]
        ,[Name]
        ,[Barcode]
        ,[Status]
        ,[LocationID]
        ,[LastActivity]
        ,[DestroyDate]
        ,[StartedBy]
        ,[Comments]
        ,[IronMtnBox]
        ,[Inventoried]
) VALUES (
        SRC.[Token]
        ,SRC.[Name]
        ,SRC.[Barcode]
        ,SRC.[Status]
        ,SRC.[LocationID]
        ,SRC.[LastActivity]
        ,SRC.[DestroyDate]
        ,SRC.[StartedBy]
        ,SRC.[Comments]
        ,SRC.[IronMtnBox]
        ,SRC.[Inventoried]
)
   output $action , inserted.*
          ;



--
-- Finally, we mark these BoxScanData records as processed so we don''t redo them next run
--
update [staging].[BoxScanData] set [Processed] = 1 where ID in (select ID from @NewTransaction);



END
--execute  [dbo].[ImportMovementData]
' 
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spImportMoveData]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'
-- ======================================================================
-- Author: Thomas O''Connor 
--
-- Create date: 8/25/17 
--
-- Description: Import barcode scanner data into the box database, updating the transaction 
-- table and the current box location.
--
--  Movement data includes box pickup, dropoff, shelve (put-a-way) and pick (pull for 
-- shipment).  Only box status and location are modified here.  See ImportInventoryData for 
-- inveintory, which can create or update box contents.
--
-- Status values:
--      Box Table:
--          [W]arehouse -- Shelved in a warehouse
--          [C]ourier -- In the posession of a courier, in transit from one location to another
--          [O]utlet -- At an outlet, some place other than a warehouse
--          [P]ending -- In the process of being created, not yet finalized
--          [T]emporary -- In a temporary staging/processing area, usually at a warehouse
--          [D]estroyed -- Sent off for destruction
--      Location Table:
--          W - Warehouse, shelving area
--          C - Courier -- Deprecated, we aren''t considering people as locations, 
--              we can track this by the Badge ID from the scan
--          O - Outlet
--          T - Temporary - temporary outlet, staging area, usually in a warehouse
--      Request Statuses
--          REQUEST_CANCELED,-1
--          REQUEST_HOLD,0
--          REQUEST_STANDARD,1
--          REQUEST_URGENT,2
--          REQUEST_PULLING'',3
--          REQUEST_ENROUTE,4
--          REQUEST_DELIVERED,7
--          REQUEST_RETURN_REQUEST,8
--          REQUEST_COMPLETE,9


-- ======================================================================
CREATE PROCEDURE [dbo].[spImportMoveData]
AS
BEGIN
--execute [dbo].[ImportMoveData]

--
-- We need to do several things here, so we start by getting
-- a list of new move transactions.
--
declare @NewTransaction Table (ID int, Operation varchar(50), BoxBarcode varchar(50));

INSERT INTO @NewTransaction (ID, Operation, BoxBarcode) 
  select ID,Operation,BoxBarcode from [staging].[BoxScanData] 
  where IsNull(Processed,0) = 0 AND
        Operation in (''DropOff'',''PickUp'',''Pick'',''Shelve'');


--
-- Add new transactions to the transaction table (de-dupe on on 
-- timestamp, location and barcode)
--
INSERT INTO [dbo].[transactions] (
        [Barcode]
        ,[Location]
        ,[BatchID]
        ,[TimeStamp]
        ,[DeviceID]
        ,[Active]
        ,[Operation]
        ,[BadgeID]
        ,[DataSourceName]
        ,[DataSourceLine]
        ,[ProcessDate]
) 

SELECT 
        S.[BoxBarcode] as Barcode
        ,s.[LocationBarcode] as Location
        ,null as BatchID                                -- Deprecated with batch loading
        ,dbo.sql2unixTime(S.[DateTime]) as [TimeStamp]
        ,null as DeviceID                               -- Deprecated with batch loading
        ,1 as Active
        ,S.[Operation]
        ,S.[BadgeID] as BadgeID
        ,S.[DataSourceName]
        ,S.[DataSourceLine]
        ,Convert(DateTime, S.[ProcessDate], 21) as ProcessDate
  -- the following Join limits selection to only what is matched in the NewTransaction table variable
  FROM [staging].[BoxScanData] S join @NewTransaction N on S.ID = N.ID 
  -- Don''t update rows we''ve already updated
  WHERE NOT EXISTS(SELECT ID FROM [dbo].[transactions] T
                    where T.Barcode  = S.[BoxBarcode]
                        and  T.[TimeStamp] = dbo.sql2unixTime(S.[DateTime])
                        and  T.[Location] = S.[LocationBarcode]
                    )
;

--
-- We need to update the box status based on the latest transaction for that box.  
-- We rely on the timestamp of the transaction to determine the latest transaction, 
-- not the order in which data necessarily comes in.
-- 

update dbo.Boxes set 
    --  
    -- The Status describes the status of a box.  The Operation describs how the
    -- barcode scanner is being used.  We need to translate between the operation, 
    -- and actual box status here.
    --
    [Status] = (case LastMove.[Operation] 
                    WHEN ''Pickup'' THEN ''C''                                  -- In the posession of a courier
                    WHEN ''Dropoff'' THEN (case LastMove.[LocationType]
                                            WHEN ''W'' then ''W''               -- Shelved in a werhouse
                                            WHEN ''T'' then ''W''               -- Temporary, as in staged at a warehouse for processing, but not shelved
                                            WHEN ''C'' then ''T''               -- In case something in the PHP code still assgns couriers, this is temporary
                                            WHEN ''O'' then ''O''               -- At an Outlet
                                        END)
                    WHEN ''Pick'' THEN ''T''                                    -- Temporary, as in staged at a warehouse for processing, inbound or but, not shelved
                    WHEN ''Shelve'' THEN ''W''                                  -- Shelved in a werhouse
                END
               ) 
    ,[LocationID] = LastMove.[LocationID]                               -- where the box should be now (Location record number, not barcode)
    ,[LastActivity] = LastMove.[DateTime]                               -- When was the last time it was touched
from ( 
        -- 
        -- This select retures the current status from the last BoxMovement transaction.
        --
        SELECT  M.[Operation] as Operation
                ,L.[ID] as LocationID                       -- Location row number, not barcode
                ,dbo.sql2unixTime(M.[DateTime]) as DateTime -- As a Unix timestamp
                ,L.[LocationType] as LocationType           -- See comments above for explanation
                ,M.[BoxBarcode] as Barcode                    -- Box (not location) barcode
        FROM [staging].[BoxScanData] M  join [dbo].[locations] L on M.[LocationBarcode] = L.[Barcode]
                                        join  @NewTransaction New on New.BoxBarcode = M.BoxBarcode
        WHERE
            M.[DateTime] = (
                            --
                            -- This subselect gets ensures we get just the most recent transaction for this box.
                            --
                            SELECT MAX([DateTime]) FROM [staging].[BoxScanData] WHERE [BoxBarcode] = M.[BoxBarcode]
                            )
         
     ) LastMove join [dbo].[Boxes] Boxes on [LastMove].[Barcode] = Boxes.[Barcode]
;

--
-- If the box is part of a request, and the operation was a drop-off, we mark that box as having been delivered in the 
-- BoxRequests join table.  
--
update dbo.BoxRequests set Status = 7
where ID in (
    SELECT 
        R.ID as BoxRequestID
    -- The join with @NewTransaction limits updates to only those related to new transactions
    FROM @NewTransaction N  join dbo.Boxes B on N.BoxBarcode = B.[Barcode]
                            join dbo.BoxRequests R on B.ID = R.BoxID
    where N.Operation = ''Dropoff''
)
;

--
-- Now let''s see if, by marking those boxes as deliverd, we have closed out a request.  In other words, 
-- if that was the last box in a request which neeed to be delivered, we can mark the whole request as 
-- delivered.

--
Update [dbo].[Requests] set Status = 7 
where ID in (
    SELECT  distinct BS.[RequestID] 
      FROM (
            -- The subselect here gets the IDs of Requests which are still open,
            -- for for which all boxes have been delivered.  That is the Box Deliver
            -- status is 7 for all boxes on the request.
            SELECT [RequestID]              
            FROM [dbo].[BoxRequests]
            group by [RequestID], BoxID, ID, Status
            having Status = 7  -- All boxes in the group have status of 7, delivered
            ) BS join [dbo].[Requests] R on R.ID = BS.RequestID
            where R.Status in (1,2,3,4)
)

--
-- Finally, we mark these BoxScanData records as processed so we don''t redo them next run
--
update [staging].[BoxScanData] set [Processed] = 1 where ID in (select ID from @NewTransaction);

END
--execute  [dbo].[ImportMovementData]
' 
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[spUpdateBoxRequests]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'-- =============================================
-- Author:		Thomas O''Connor
-- Create date: August 25, 2017
-- Description:	spGetWarehousePickList -- Get a 
--              pick list of boxes to be pulled 
--              from the warehouse based on open 
--              box requests.
-- =============================================
--execute [dbo].[spUpdateBoxRequests]



CREATE PROCEDURE [dbo].[spUpdateBoxRequests]

AS
BEGIN
-- SET NOCOUNT ON added to prevent extra result sets from
-- interfering with SELECT statements.

SET NOCOUNT ON;

-- 
-- This query will pull box data from the request table, and 
-- expand the BoxData comma delimite field in the requsts table, 
-- createing a separate row for each box to be picked.  This is saved in 
-- a temporary table to be joined with other tables later on.
--
-- Don''t ask me how this works, I don''t fully understand the funciton of the with statement.  
-- See: https://stackoverflow.com/questions/5493510/turning-a-comma-separated-string-into-individual-rows
-- for the source and partial explanation of this technique.
--
--

;with tmp(
        [ID]
        ,[Status]
        ,[RequestedBy]
        ,[RequestTime]
        ,[DeliverTo]
        ,[Location]
        ,BoxID      -- The artificial field we are going to create
        ,[BoxData]  -- based on comma delimited field here
        ) as (
        SELECT  
            [ID]
            ,[Status]
            ,[RequestedBy]
            ,[RequestTime]
            ,[DeliverTo]
            ,[Location]
                ,LEFT([BoxData], CHARINDEX('','',[BoxData]+'','')-1),
                        STUFF([BoxData], 1, CHARINDEX('','',[BoxData]+'',''), '''')
        FROM [RCWarehouse].[dbo].[requests]
    union all
        SELECT
            [ID]
            ,[Status]
            ,[RequestedBy]
            ,[RequestTime]
            ,[DeliverTo]
            ,[Location]
            ,LEFT([BoxData], CHARINDEX('','',[BoxData]+'','')-1),
                  STUFF([BoxData], 1, CHARINDEX('','',[BoxData]+'',''), '''')
        FROM tmp
        where [BoxData] > ''''
)--;

        select * into #ExpandedRequests
        from tmp 
        order by ID;

--
-- Now insert rows into the BoxRequests table, which is a many-to-many join table 
-- between requests and boxes.
--

INSERT INTO dbo.BoxRequests (RequestID, BoxID, Status)
--select *
SELECT ID as RequestID, BoxID, Status
FROM #ExpandedRequests E 
WHERE NOT EXISTS(SELECT [ID] FROM dbo.BoxRequests B
                 where E.[ID] = B.[RequestID]
                   and E.[BoxID] = B.[BoxID]
                 )

;

--
-- Yes, SQL Server will clean this up, but we help give a little shove here
--
drop table #ExpandedRequests;

END
--execute [dbo].[spUpdateBoxRequests]

' 
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[spStageDestroyData]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'
-- ======================================================================
-- Author: Thomas O''Connor 
--
-- Create date: 7/11/16 
--
-- Description: Merges Oracle Approve and Paid Invoice data.
-- ======================================================================
CREATE PROCEDURE [staging].[spStageDestroyData]
AS
BEGIN

INSERT INTO staging.BoxScanData (
      [Operation]
      ,[BadgeID]
      ,[BoxBarcode]
      ,[Property]
      ,[BoxOrigin]
      ,[Contents]
      ,[Start]
      ,[End]
      ,[LocationBarcode]
      ,[DateTime]
      ,[DataSourceName]
      ,[DataSourceLine]
      ,[ProcessDate]
      ,[Processed]
) SELECT 
        [Operation]
        ,[Badge ID]
        ,[Box Code]
        ,[Property]
        ,[Origin]
        ,[Contents]
        ,[Start Date]
        ,[End Date]
        ,null as [LocationID]
        ,[DateTime]
        ,[DataSourceName]
        ,[DataSourceLine]
        ,[ProcessDate]
        ,0 as [Processed]
  --FROM dbo.TempBoxScanData T --for testing
  FROM #TempBoxScanData T
  WHERE NOT EXISTS(SELECT [Box Code] FROM staging.BoxScanData S
                    where T.[Operation] = S.[Operation]
                        and T.[Badge ID] = S.[BadgeID]
                        and T.[Box Code] = S.[BoxBarcode]
                        and T.[DateTime] = S.[DateTime]
                    )
;

END
--execute [staging].[StageDestroyData]

--Delete from dbo.TempBoxScanData;
--Delete from staging.BoxScanData;
' 
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[spStageInventoryData]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'
-- ======================================================================
-- Author: Thomas O''Connor 
--
-- Create date: 7/11/16 
--
-- Description: Merges Oracle Approve and Paid Invoice data.
-- ======================================================================
CREATE PROCEDURE [staging].[spStageInventoryData]
AS
BEGIN

INSERT INTO staging.BoxScanData (
      [Operation]
      ,[BadgeID]
      ,[BoxBarcode]
      ,[Property]
      ,[BoxOrigin]
      ,[Contents]
      ,[Start]
      ,[End]
      ,[LocationBarcode]
      ,[DateTime]
      ,[DataSourceName]
      ,[DataSourceLine]
      ,[ProcessDate]
      ,[Processed]
) SELECT 
        [Operation]
        ,[Badge ID]
        ,[Box Code]
        ,[Property]
        ,[Box Origin]
        ,[Box Contents]
        ,[Start]
        ,[End]
        ,[Location ID]
        ,[DateTime]
        ,[DataSourceName]
        ,[DataSourceLine]
        ,[ProcessDate]
        ,0 as [Processed]
  --FROM dbo.TempBoxScanData T --for testing
  FROM #TempBoxScanData T
  WHERE NOT EXISTS(SELECT [Box Code] FROM staging.BoxScanData S
                    where T.[Operation] = S.[Operation]
                        and T.[Badge ID] = S.[BadgeID]
                        and T.[Box Code] = S.[BoxBarcode]
                        and T.[DateTime] = S.[DateTime]
                    )
;
 
END
--execute  [staging].[StageInventoryData]
' 
END
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[staging].[spStageMoveData]') AND type in (N'P', N'PC'))
BEGIN
EXEC dbo.sp_executesql @statement = N'
-- ======================================================================
-- Author: Thomas O''Connor 
--
-- Create date: 7/11/16 
--
-- Description: Merges Oracle Approve and Paid Invoice data.
-- ======================================================================
CREATE PROCEDURE [staging].[spStageMoveData]
AS
BEGIN

INSERT INTO staging.BoxScanData (
      [BadgeID]
      ,[LocationBarcode]
      ,[DateTime]
      ,[BoxBarcode]
      ,[Start]
      ,[End]
      ,[Property]
      ,[BoxOrigin]
      ,[Operation]
      ,[Contents]
      ,[DataSourceName]
      ,[DataSourceLine]
      ,[ProcessDate]
      ,[Processed]
) SELECT 
      [Badge ID]
      ,[Location ID]
      ,[DateTime]
      ,[Box Code]
      ,null as [StartDate]
      ,null as [EndDate]
      ,null as [Property]
      ,null as [BoxOrigin]
      ,[Operation]
      ,null as [BoxContents]
      ,[DataSourceName]
      ,[DataSourceLine]
      ,[ProcessDate]
      ,0 as [Processed]
  --FROM dbo.TempBoxScanData T --for testing
  FROM #TempBoxScanData T
  WHERE NOT EXISTS(SELECT [Box Code] FROM staging.BoxScanData S
                    where T.[Operation] = S.[Operation]
                        and T.[Badge ID] = S.[BadgeID]
                        and T.[Box Code] = S.[BoxBarcode]
                        and T.[DateTime] = S.[DateTime]
                    )
;

--drop table #BoxScanData;

END
--execute [staging].[StageMoveData]

--Delete from [dbo].[TempBoxScanData]
' 
END
GO
