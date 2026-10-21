<?php
/**
 * Tests de QueryHelper, sans dependance : php tests/run.php
 *
 * Sort avec le code 1 des qu'un test echoue, pour servir tel quel en CI.
 */

define('_JEXEC', 1);

require __DIR__ . '/../administrator/src/Helper/QueryHelper.php';

use Saywhat49\Component\Jmm\Administrator\Helper\QueryHelper as Q;

$failures = 0;
$count    = 0;

function check(string $label, $expected, $actual): void
{
    global $failures, $count;
    $count++;

    if ($expected !== $actual) {
        $failures++;
        fwrite(STDERR, "ECHEC  $label\n   attendu : " . var_export($expected, true) . "\n   obtenu  : " . var_export($actual, true) . "\n");
    }
}

// --- Lecture seule : ce qui doit passer -------------------------------------
$readOnly = [
    'SELECT simple'                    => 'SELECT * FROM t',
    'SELECT avec point-virgule final'  => 'SELECT * FROM t;',
    'SELECT entre parentheses'         => '(SELECT a FROM t) UNION (SELECT a FROM u)',
    'commentaire avant'                => "-- liste\nSELECT 1",
    'commentaire bloc avant'           => '/* x */ SELECT 1',
    'indication optimiseur'            => 'SELECT /*+ MAX_EXECUTION_TIME(1000) */ * FROM t',
    'REPLACE() est une fonction'       => "SELECT REPLACE(nom, 'a', 'b') FROM t",
    'INSERT() est une fonction'        => "SELECT INSERT('abc', 1, 1, 'x')",
    'mot-cle dans une chaine'          => "SELECT * FROM t WHERE note = 'DROP TABLE x; DELETE FROM y'",
    'mot-cle dans un identifiant'      => 'SELECT `update`, `delete` FROM `drop`',
    'point-virgule dans une chaine'    => "SELECT 'a;b'",
    'guillemet double echappe'         => 'SELECT "a\"; DROP TABLE x" FROM t',
    'quote double dans une chaine'     => "SELECT 'it''s; fine'",
    'SHOW TABLES'                      => 'SHOW TABLES',
    'SHOW CREATE TABLE'                => 'SHOW CREATE TABLE t',
    'DESCRIBE'                         => 'DESCRIBE t',
    'DESC'                             => 'DESC t',
    'EXPLAIN SELECT'                   => 'EXPLAIN SELECT * FROM t',
    'EXPLAIN DELETE n execute rien'    => 'EXPLAIN DELETE FROM t',
    'EXPLAIN ANALYZE SELECT'           => 'EXPLAIN ANALYZE SELECT * FROM t',
    'WITH en lecture'                  => 'WITH x AS (SELECT 1 AS a) SELECT a FROM x',
    'WITH RECURSIVE'                   => 'WITH RECURSIVE n AS (SELECT 1 AS i UNION ALL SELECT i + 1 FROM n WHERE i < 5) SELECT i FROM n',
    'SELECT FOR UPDATE'                => 'SELECT * FROM t FOR UPDATE',
    'minuscules'                       => 'select * from t',
];

foreach ($readOnly as $label => $sql) {
    check("lecture seule : $label", true, Q::isReadOnly($sql));
}

// --- Lecture seule : ce qui doit etre refuse --------------------------------
$writes = [
    'INSERT'                           => "INSERT INTO t (a) VALUES (1)",
    'UPDATE'                           => 'UPDATE t SET a = 1',
    'DELETE'                           => 'DELETE FROM t',
    'DROP'                             => 'DROP TABLE t',
    'DROP DATABASE'                    => 'DROP DATABASE d',
    'TRUNCATE'                         => 'TRUNCATE TABLE t',
    'ALTER'                            => 'ALTER TABLE t ADD c INT',
    'CREATE'                           => 'CREATE TABLE t (a INT)',
    'GRANT'                            => "GRANT ALL ON *.* TO 'x'@'%'",
    'SET'                              => 'SET GLOBAL max_connections = 1',
    'CALL'                             => 'CALL proc()',
    'LOAD DATA'                        => "LOAD DATA INFILE '/etc/passwd' INTO TABLE t",
    'ANALYZE TABLE'                    => 'ANALYZE TABLE t',
    'plusieurs instructions'           => 'SELECT 1; DROP TABLE t',
    'plusieurs apres commentaire'      => "SELECT 1; -- ok\nDELETE FROM t",
    'WITH ... DELETE'                  => 'WITH x AS (SELECT 1) DELETE FROM t WHERE id IN (SELECT * FROM x)',
    'WITH ... UPDATE'                  => 'WITH x AS (SELECT 1) UPDATE t SET a = 1',
    'SELECT INTO OUTFILE'              => "SELECT * FROM t INTO OUTFILE '/tmp/x'",
    'SELECT INTO DUMPFILE'             => "SELECT 'x' INTO DUMPFILE '/tmp/x'",
    'LOAD_FILE'                        => "SELECT LOAD_FILE('/etc/passwd')",
    'EXPLAIN ANALYZE DELETE'           => 'EXPLAIN ANALYZE DELETE FROM t',
    'commentaire executable'           => 'SELECT 1 /*!50000 UNION SELECT 2 */',
    'commentaire executable MariaDB'   => 'SELECT 1 /*M!100000 UNION SELECT 2 */',
    'chaine non terminee'              => "SELECT 'abc",
    'commentaire non termine'          => 'SELECT 1 /* abc',
    'vide'                             => '',
    'commentaire seul'                 => '-- rien',
    'mot inconnu'                      => 'FOO BAR',
];

foreach ($writes as $label => $sql) {
    check("refuse : $label", false, Q::isReadOnly($sql));
}

// --- returnsRows -------------------------------------------------------------
check('returnsRows SELECT', true, Q::returnsRows('SELECT 1'));
check('returnsRows SHOW', true, Q::returnsRows('SHOW TABLES'));
check('returnsRows WITH lecture', true, Q::returnsRows('WITH x AS (SELECT 1) SELECT * FROM x'));
check('returnsRows WITH ecriture', false, Q::returnsRows('WITH x AS (SELECT 1) DELETE FROM t'));
check('returnsRows UPDATE', false, Q::returnsRows('UPDATE t SET a = 1'));
check('returnsRows apres commentaire', true, Q::returnsRows("-- x\nSELECT 1"));

// --- isDerivable -------------------------------------------------------------
check('derivable SELECT', true, Q::isDerivable('SELECT * FROM t'));
check('derivable WITH', true, Q::isDerivable('WITH x AS (SELECT 1) SELECT * FROM x'));
check('derivable (SELECT', true, Q::isDerivable('(SELECT 1) UNION (SELECT 2)'));
check('non derivable SHOW', false, Q::isDerivable('SHOW TABLES'));
check('non derivable DESCRIBE', false, Q::isDerivable('DESCRIBE t'));

// --- prepare / count / page --------------------------------------------------
check('prepare retire le point-virgule', 'SELECT 1', Q::prepare('SELECT 1;'));
check('prepare retire un commentaire de fin', 'SELECT 1', Q::prepare("SELECT 1 -- fin\n"));
check('prepare retire un commentaire de fin sans retour ligne', 'SELECT 1', Q::prepare('SELECT 1 -- fin'));
check('prepare retire # de fin', 'SELECT 1', Q::prepare("SELECT 1 # fin"));
check('prepare garde les chaines', "SELECT '-- pas un commentaire'", Q::prepare("SELECT '-- pas un commentaire';"));
check('prepare garde # dans une chaine', "SELECT 'a#b'", Q::prepare("SELECT 'a#b'"));
check('prepare garde les identifiants', 'SELECT `a--b` FROM t', Q::prepare('SELECT `a--b` FROM t'));
check('prepare garde les indications', 'SELECT /*+ NO_RANGE_OPTIMIZATION(t) */ 1', Q::prepare('SELECT /*+ NO_RANGE_OPTIMIZATION(t) */ 1;'));
check('prepare : -- sans espace n est pas un commentaire', 'SELECT 5--3', Q::prepare('SELECT 5--3'));

check('countQuery', "SELECT COUNT(*) FROM (\nSELECT * FROM t\n) AS jmm_cnt", Q::countQuery('SELECT * FROM t;'));
check('countQuery avec commentaire de fin', "SELECT COUNT(*) FROM (\nSELECT * FROM t\n) AS jmm_cnt", Q::countQuery('SELECT * FROM t -- note'));

check('pageQuery', "SELECT * FROM (\nSELECT * FROM t\n) AS jmm_page LIMIT 20, 10", Q::pageQuery('SELECT * FROM t', 20, 10));
check('pageQuery avec LIMIT deja present', "SELECT * FROM (\nSELECT * FROM t LIMIT 5\n) AS jmm_page LIMIT 0, 10", Q::pageQuery('SELECT * FROM t LIMIT 5', 0, 10));
check('pageQuery avec tri', "SELECT * FROM (\nSELECT 1 AS a\n) AS jmm_page ORDER BY `a` DESC LIMIT 0, 25", Q::pageQuery('SELECT 1 AS a', 0, 25, '`a` DESC'));
check('pageQuery borne offset et limite', "SELECT * FROM (\nSELECT 1\n) AS jmm_page LIMIT 0, 1", Q::pageQuery('SELECT 1', -5, 0));

// --- parseSorting ------------------------------------------------------------
check('tri simple', [['nom', 'ASC']], Q::parseSorting('nom'));
check('tri avec sens', [['nom', 'DESC'], ['ville', 'ASC']], Q::parseSorting('nom DESC, ville ASC'));
check('tri insensible a la casse', [['nom', 'DESC']], Q::parseSorting('nom desc'));
check('tri refuse les injections', [], Q::parseSorting('nom; DROP TABLE t'));
check('tri ignore les elements invalides', [['nom', 'ASC']], Q::parseSorting('nom, 1=1 --, `x`'));
check('tri vide', [], Q::parseSorting(''));

// --- TableModel (site), avec des doublures de Joomla et SQLite --------------------
require __DIR__ . '/TableModelTest.php';

fwrite(STDOUT, ($count - $failures) . "/$count tests reussis\n");
exit($failures > 0 ? 1 : 0);
