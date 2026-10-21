<?php
namespace Saywhat49\Component\Jmm\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseFactory;

class JmmHelper
{
    /**
     * Types de colonnes proposes par le concepteur de tables, avec la forme
     * de longueur que chacun accepte :
     *
     *   none      aucune longueur (DATE, TEXT, BLOB, JSON...)
     *   int       un entier facultatif (largeur d'affichage des entiers)
     *   int_req   un entier obligatoire (VARCHAR, VARBINARY)
     *   decimal   (M) ou (M,D)  -- DECIMAL, FLOAT, DOUBLE
     *   fsp       precision des fractions de seconde, 0 a 6
     *   bit       un entier de 1 a 64
     *
     * @return array<string,string>
     */
    public static function getColumnTypes(): array
    {
        return [
            'INT'        => 'int',
            'TINYINT'    => 'int',
            'SMALLINT'   => 'int',
            'MEDIUMINT'  => 'int',
            'BIGINT'     => 'int',
            'BOOLEAN'    => 'none',
            'SERIAL'     => 'none',
            'DECIMAL'    => 'decimal',
            'NUMERIC'    => 'decimal',
            'FLOAT'      => 'decimal',
            'DOUBLE'     => 'decimal',
            'REAL'       => 'decimal',
            'BIT'        => 'bit',
            'CHAR'       => 'int',
            'VARCHAR'    => 'int_req',
            'TINYTEXT'   => 'none',
            'TEXT'       => 'none',
            'MEDIUMTEXT' => 'none',
            'LONGTEXT'   => 'none',
            'JSON'       => 'none',
            'BINARY'     => 'int',
            'VARBINARY'  => 'int_req',
            'TINYBLOB'   => 'none',
            'BLOB'       => 'none',
            'MEDIUMBLOB' => 'none',
            'LONGBLOB'   => 'none',
            'DATE'       => 'none',
            'YEAR'       => 'none',
            'DATETIME'   => 'fsp',
            'TIMESTAMP'  => 'fsp',
            'TIME'       => 'fsp',
        ];
    }

    /** Types acceptant AUTO_INCREMENT. */
    public static function getAutoIncrementTypes(): array
    {
        return ['INT', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT', 'SERIAL'];
    }

    public static function addSubmenu(string $vName = 'databases'): void
    {
        $input = Factory::getApplication()->getInput();
        $dbname = $input->getString('dbname', '');
        $urlSuffix = $dbname !== '' ? '&dbname=' . urlencode($dbname) : '';

        Sidebar::addEntry(
            Text::_('COM_JMM_DASHBOARD'),
            'index.php?option=com_jmm&view=dashboard' . $urlSuffix,
            $vName === 'dashboard'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_DATABASES'),
            'index.php?option=com_jmm&view=databases' . $urlSuffix,
            $vName === 'databases'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_TABLES'),
            'index.php?option=com_jmm&view=tables' . $urlSuffix,
            $vName === 'tables'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_SQL_QUERY'),
            'index.php?option=com_jmm&view=sql' . $urlSuffix,
            $vName === 'sql'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_CANNED_QUERY'),
            'index.php?option=com_jmm&view=cannedqueries' . $urlSuffix,
            $vName === 'cannedqueries' || $vName === 'cannedquery'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_SITE_TABLES'),
            'index.php?option=com_jmm&view=sitetables' . $urlSuffix,
            $vName === 'sitetables' || $vName === 'sitetable'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_CREATE_TABLE'),
            'index.php?option=com_jmm&view=createtable' . $urlSuffix,
            $vName === 'createtable'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_INSERT_DATA'),
            'index.php?option=com_jmm&view=insert' . $urlSuffix,
            $vName === 'insert'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_TEMPLATES'),
            'index.php?option=com_jmm&view=templates' . $urlSuffix,
            $vName === 'templates' || $vName === 'template'
        );
    }

    /**
     * Identifiant (base, table, colonne) accepte par le composant.
     *
     * Les lettres, chiffres, "_", "$" et "-" suffisent aux bases et aux
     * tables de Joomla et de ce composant. Un nom different est REFUSE, jamais
     * "nettoye" : retirer des caracteres en silence conduit a viser une autre
     * table ou une autre base que celle demandee.
     */
    public static function isValidIdentifier(string $identifier): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_$-]{1,64}$/', $identifier);
    }

    /**
     * Lit un identifiant dans la requete HTTP.
     *
     * Renvoie '' si le parametre est absent ou invalide. $valid vaut false
     * dans le second cas, pour que l'appelant puisse le signaler au lieu de
     * se rabattre sans le dire sur la base ou la table par defaut.
     */
    public static function requestIdentifier(string $name, ?bool &$valid = null): string
    {
        $value = trim((string) Factory::getApplication()->getInput()->get($name, '', 'raw'));
        $valid = $value === '' || self::isValidIdentifier($value);

        return $valid ? $value : '';
    }

    /** Connexions dediees deja ouvertes, indexees par parametres. */
    private static array $connections = [];

    /** Echecs deja constates dans la requete, pour ne pas retenter. */
    private static array $failures = [];

    /** Avertissements deja affiches, pour ne pas empiler le meme. */
    private static array $reported = [];

    private static function isManager(): bool
    {
        return Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_jmm');
    }

    /**
     * Retourne une connexion pointant reellement sur $targetDb.
     *
     * On n'emet PAS "USE <base>" sur le DatabaseDriver partage de Joomla :
     * la bascule n'est pas reversible pour le reste de la requete (session,
     * menus, journaux passeraient sur la mauvaise base) et elle echoue en
     * silence quand l'utilisateur MySQL n'a pas de droits sur la base cible.
     * On ouvre a la place une connexion distincte, mise en cache.
     *
     * Aucun repli silencieux : si la connexion demandee est impossible, on
     * leve une exception. Afficher les donnees de la base de Joomla a la
     * place de celle qu'on a demandee, c'est executer des requetes au mauvais
     * endroit sans que personne ne s'en apercoive.
     *
     * @throws \RuntimeException Nom de base invalide ou connexion impossible.
     */
    public static function getDatabaseConnection(?string $targetDb = null): DatabaseDriver
    {
        $app      = Factory::getApplication();
        $config   = $app->getConfig();
        $params   = ComponentHelper::getParams('com_jmm');
        $targetDb = trim((string) $targetDb);

        if ($targetDb !== '' && !self::isValidIdentifier($targetDb)) {
            throw new \RuntimeException(Text::_('COM_JMM_ERROR_INVALID_DATABASE_NAME'));
        }

        // Identifiants dedies saisis dans les options du composant.
        if ((int) $params->get('dbsettings', 0) === 1) {
            $database = $targetDb !== '' ? $targetDb : trim((string) $params->get('dbname', ''));

            return self::openConnection(
                [
                    'driver'   => 'mysqli',
                    'host'     => (string) $params->get('dbhost', 'localhost'),
                    'user'     => (string) $params->get('dbusername', ''),
                    'password' => (string) $params->get('dbpass', ''),
                    'database' => $database,
                    'prefix'   => (string) $params->get('dbprefix', ''),
                ],
                Text::_('COM_JMM_ERROR_CUSTOM_CONNECTION_FAILED')
            );
        }

        $shared = Factory::getContainer()->get('DatabaseDriver');

        // Pas de base demandee, ou base identique a celle de Joomla.
        if ($targetDb === '' || strcasecmp($targetDb, (string) $config->get('db', '')) === 0) {
            return $shared;
        }

        return self::openConnection(
            [
                'driver'   => (string) $config->get('dbtype', 'mysqli'),
                'host'     => (string) $config->get('host', 'localhost'),
                'user'     => (string) $config->get('user', ''),
                'password' => (string) $config->get('password', ''),
                'database' => $targetDb,
                'prefix'   => (string) $config->get('dbprefix', ''),
            ],
            Text::sprintf('COM_JMM_DB_SWITCH_FAILED', $targetDb)
        );
    }

    /**
     * Comme getDatabaseConnection(), mais renvoie null au lieu de lever une
     * exception, apres avoir prevenu les gestionnaires du composant. Pour les
     * listes (bases, tables) qui doivent rester affichables, vides.
     */
    public static function tryConnection(?string $targetDb = null): ?DatabaseDriver
    {
        try {
            return self::getDatabaseConnection($targetDb);
        } catch (\RuntimeException $e) {
            self::reportError($e->getMessage());

            return null;
        }
    }

    /** Affiche une erreur aux gestionnaires du composant, une seule fois. */
    public static function reportError(string $message): void
    {
        if (isset(self::$reported[$message]) || !self::isManager()) {
            return;
        }

        self::$reported[$message] = true;
        Factory::getApplication()->enqueueMessage(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), 'error');
    }

    /**
     * Ouvre (et met en cache) une connexion.
     *
     * Le message d'erreur du pilote peut contenir l'hote et le compte MySQL :
     * il n'est joint qu'a l'intention des gestionnaires du composant.
     *
     * @throws \RuntimeException
     */
    private static function openConnection(array $options, string $failureMessage): DatabaseDriver
    {
        $key = md5(json_encode([
            $options['driver'] ?? '',
            $options['host'] ?? '',
            $options['user'] ?? '',
            $options['database'] ?? '',
            $options['prefix'] ?? '',
        ]));

        if (isset(self::$connections[$key])) {
            return self::$connections[$key];
        }

        if (isset(self::$failures[$key])) {
            throw new \RuntimeException(self::$failures[$key]);
        }

        $driverName = ($options['driver'] ?? '') ?: 'mysqli';

        if ($driverName === 'mysql') {
            $driverName = 'mysqli';
        }

        $options['driver'] = $driverName;

        try {
            $driver = (new DatabaseFactory())->getDriver($driverName, $options);
            $driver->connect();
        } catch (\Throwable $e) {
            $message = $failureMessage;

            if (self::isManager()) {
                $message .= ' [' . $e->getMessage() . ']';
            }

            self::$failures[$key] = $message;

            throw new \RuntimeException($message, 0, $e);
        }

        self::$connections[$key] = $driver;

        return $driver;
    }

    public static function getDataBaseLists(?DatabaseDriver $db = null): array
    {
        $db = $db ?? self::tryConnection();

        if ($db === null) {
            return [];
        }

        try {
            $db->setQuery('SHOW DATABASES');
            $rows = $db->loadColumn();

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getTablesFromDB(?DatabaseDriver $db = null): array
    {
        $db = $db ?? self::tryConnection();

        if ($db === null) {
            return [];
        }

        try {
            $db->setQuery('SHOW TABLES');
            $rows = $db->loadColumn();

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Renvoie le nom reel de la table s'il existe dans la base, null sinon.
     * Le nom demande est confronte a SHOW TABLES : il ne sert jamais tel quel.
     */
    public static function resolveTable(string $table, DatabaseDriver $db): ?string
    {
        if (!self::isValidIdentifier($table)) {
            return null;
        }

        $tables = self::getTablesFromDB($db);

        if (in_array($table, $tables, true)) {
            return $table;
        }

        foreach ($tables as $name) {
            if (strcasecmp((string) $name, $table) === 0) {
                return (string) $name;
            }
        }

        return null;
    }

    /**
     * Description des colonnes (SHOW COLUMNS), indexee par nom de colonne :
     * Field, Type, Null, Key, Default, Extra.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function getColumnsInfo(string $table, ?DatabaseDriver $db = null): array
    {
        if (!self::isValidIdentifier($table)) {
            return [];
        }

        $db = $db ?? self::tryConnection();

        if ($db === null) {
            return [];
        }

        try {
            $db->setQuery('SHOW COLUMNS FROM ' . $db->quoteName($table));
            $rows = $db->loadAssocList();
        } catch (\Throwable $e) {
            return [];
        }

        $info = [];

        foreach ((array) $rows as $row) {
            if (isset($row['Field'])) {
                $info[(string) $row['Field']] = $row;
            }
        }

        return $info;
    }

    public static function getColumnsFromTable(string $table, ?DatabaseDriver $db = null): array
    {
        // Les cles numeriques deviennent des entiers en PHP : on rend des chaines.
        return array_map('strval', array_keys(self::getColumnsInfo($table, $db)));
    }
}
