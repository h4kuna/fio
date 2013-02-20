<?php

namespace h4kuna\fio\reader;

use \Nette\Object;

require_once 'IFile.php';

abstract class File extends Object implements IFile {

// Pohyby na účtu za určené období
// https://www.fio.cz/ib_api/rest/periods/aGEMQB9Idh35fh1g51h3ekkQwyGlQ/2012-08-25/2012-08-31/transactions.xml
// Oficiální výpisy pohybů z účtu
// https://www.fio.cz/ib_api/rest/by-id/aGEMtmwcsg5EbfIjqIhunibjhuvfdtsersxexdtgMR9Idh6u3/2012/1/transactions.xml
// Pohyby na účtu od posledního stažení
// https://www.fio.cz/ib_api/rest/last/aGEMtmwcsWAjPzhg3bPH3j7Iu15g56d66AdEbfIjqIgMR9Idh6u3/transactions.xml
// Na ID posledního úspěšně staženého pohybu
// https://www.fio.cz/ib_api/rest/set-last-id/Pu5CMBu5nYBtWAk4gsj0FaUlY7JIjUnYBthKaquSWf1eUl/1147608196/
// Na datum posledního neúspěšně staženého pohybu
// https://www.fio.cz/ib_api/rest/set-last-date/Pu5CMBu5nYBthKaqM0FaUlY7JIjUnY0FaUlY7JIjU1eUl/2012-07-27/
    public function __toString() {
        return $this->getExtension();
    }

}
