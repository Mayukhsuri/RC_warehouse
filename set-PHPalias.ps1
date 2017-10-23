set-alias php 'C:\Program Files (x86)\PHP\v5.4\php.exe'

function check-php {
    dir *.php -recurse | foreach { php -l $_.FullName }
}
