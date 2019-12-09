

#!/bin/bash

find . -type f -name "*.sql" -print0 | xargs -0 sed -i '' "s/PRIMARY KEY AUTOINCREMENT UNIQUE/AUTO_INCREMENT PRIMARY KEY/"
find . -type f -name "*.sql" -print0 | xargs -0 sed -i '' "s/(datetime('now', 'localtime'))/CURRENT_TIMESTAMP/"
find . -type f -name "*.sql" -print0 | xargs -0 sed -i '' "s/TEXT NOT NULL UNIQUE/varchar(80) NOT NULL UNIQUE/"
