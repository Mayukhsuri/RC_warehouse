param(
    [switch]$list
    )

set-psdebug -off

function list-snapshots {
    $fnames = @()

    get-childitem ".\Snapshots\*" | sort -property name | foreach {
        if ($_.name -match $regex) {
            $this_rev = [int]$matches[1]
            $fnames += $_.name
        }
    }
    for  ($i = 0; $i -lt $fnames.count; $i++) {
        "$($i+1):`t$($fnames[$i])"
    }
}

function make-snapshot {
    if (-not ($zip = (get-command zip.exe).path)) {
        throw "Cannot find a zip executable"
    }
    
    $this_name = (get-item .).name
    
    $max_rev = 0
    get-childitem ".\Snapshots\*" | foreach {
        if ($_.name -match $regex) {
            $this_rev = [int]$matches[1]
            if ($this_rev -gt $max_rev) {
                $max_rev = $this_rev
            }
        }
    }

    $max_rev++
    $date = get-date -format "yyyy-MM-dd_HH-mm-ss"
    $fname =  "{0}-({1:d3})-{2}" -f $this_name, $max_rev, $date
    
    & $zip ".\Snapshots\$fname" -r * -x .\Snapshots\* *.zip #> $null 
    #$($zip ".\Snapshots\$fname" * -x .\Snapshots\*)
}    

pushd .

#
# Look for the snapshot folder, go up the tree if not found
#
while (-not (Test-Path ".\Snapshots" -pathType container)) {
    if ((get-item .).parent) {
        cd ..
    } else {
        #
        # We got to the root folder, so backup and assume we need to
        # create a folder.
        #
        popd     # back to our start
        mkdir ".\Snapshots"
        pushed . # push again since we will pop at the end
        break
    }
}

$this_name = (get-item .).name
$regex = "$this_name-\((\d{3})\)-"

if ($list.IsPresent) {
    list-snapshots
    exit
} else {
    make-snapshot
    list-snapshots
}

popd
