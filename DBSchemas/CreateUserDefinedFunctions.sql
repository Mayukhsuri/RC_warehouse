SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[sql2unixTime]') AND type in (N'FN', N'IF', N'TF', N'FS', N'FT'))
BEGIN
execute dbo.sp_executesql @statement = N'-- =============================================
-- Author:		Thomas O''Connor
-- Create date: 2017-08-17
-- Description:	Convert SQL timestamp to a unix 
--      time stamp. Google Unix timestamps for 
--      more information.
-- =============================================
CREATE FUNCTION [dbo].[sql2unixTime] (@SqlTime as DateTime)
RETURNS BigInt 
AS
BEGIN
	declare @UnixEpoch as datetime = ''1970-01-01 00:00:00'';
    return DATEDIFF(SECOND, @UnixEpoch, @SqlTime)
    --return DATEDIFF(SECOND, {d ''1970-01-01''}, @SqlTime)

END
' 
END

GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[unix2sqlTime]') AND type in (N'FN', N'IF', N'TF', N'FS', N'FT'))
BEGIN
execute dbo.sp_executesql @statement = N'-- =============================================
-- Author:		Thomas O''Connor
-- Create date: 2017-08-17
-- Description:	To convert a unix time stamp, the 
--      number of seconds since the unix epoc, 
--      1970-01-01 05:00:00, one uess the DATEADD 
--      function.  Only one problem, the current 
--      unix time stamp is larger than allowed by 
--      the DATEADD function (limted to an int), 
--      so we subtract the unix time stamp for the 
--      milinium, and calculate from there.
-- =============================================
CREATE FUNCTION dbo.unix2sqlTime (@unixTime as bigint)
RETURNS datetime 
AS
BEGIN
	declare @MeliniaUnixTimeStamp as bigint = 946684801,
        @MeliniaSQLTimeStamp as datetime = ''2000-01-01 00:00:01'';

    return DATEADD(s,(@unixTime - @MeliniaUnixTimeStamp), @MeliniaSQLTimeStamp)

END
' 
END

GO
