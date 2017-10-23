function toascii ($inf, $outf) {
    get-content $inf | out-file -encoding ASCII $outf
}


$outfile = './allcode.temp'
$outphp = ($outfile -replace '\.temp','.php')

if (test-path $outphp) {
    rm $outphp
}

echo $null > $outfile

dir *.php -recurse | foreach {
    write-host -f green $_.FullName
    echo "---- $($_.FullName) ----" >> $outfile
    cat $_.FullName >> $outfile
}
toascii $outfile $outphp

