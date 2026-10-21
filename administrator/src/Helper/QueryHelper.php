<?php
namespace Saywhat49\Component\Jmm\Administrator\Helper;

defined('_JEXEC') or die;

/**
 * Analyse et construction de requetes SQL, sans dependance a Joomla.
 *
 * La classe est volontairement pure : elle se teste en ligne de commande
 * (tests/run.php) et sert a la fois l'administration (qui decide qui peut
 * executer quoi) et le site (qui ne doit jamais ecrire en base).
 *
 * Elle ne remplace pas les droits MySQL. C'est une barriere de coherence :
 * un compte "Manager" du composant ne doit pas pouvoir lancer un DROP, et
 * une table de site ne doit jamais executer autre chose qu'une lecture.
 */
final class QueryHelper
{
    /** Mots-cles qui modifient les donnees ou le schema. */
    private const WRITE_KEYWORDS = '/\b(INSERT|REPLACE|UPDATE|DELETE|MERGE|CREATE|ALTER|DROP|TRUNCATE|RENAME|GRANT|REVOKE|CALL|LOAD|SET)\b(?!\s*\()/i';

    /** Instructions dont le resultat se lit comme un jeu de lignes. */
    private const ROW_RETURNING = ['SELECT', 'WITH', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN', 'ANALYZE'];

    /**
     * Parcourt la requete en separant le code des commentaires et des
     * litteraux.
     *
     * - 'code'   : requete sans commentaires, chaines et identifiants
     *              remplaces par un jeton neutre. Sert a classer l'instruction
     *              sans qu'un mot-cle dans une chaine ne la fausse.
     * - 'clean'  : requete sans commentaires, litteraux conserves. Sert a
     *              l'envelopper dans une sous-requete (un commentaire "--" en
     *              fin de ligne mangerait la parenthese fermante).
     * - 'exec'   : vrai si la requete contient un commentaire executable
     *              MySQL ou MariaDB (/*! ... *\/), dont le contenu est execute.
     * - 'broken' : vrai si un litteral ou un commentaire n'est pas termine.
     *
     * @return array{code:string,clean:string,exec:bool,broken:bool}
     */
    private static function scan(string $sql): array
    {
        $len    = strlen($sql);
        $code   = '';
        $clean  = '';
        $exec   = false;
        $broken = false;
        $i      = 0;

        while ($i < $len) {
            $c    = $sql[$i];
            $next = $i + 1 < $len ? $sql[$i + 1] : '';

            // Litteraux : 'chaine', "chaine", `identifiant`.
            if ($c === "'" || $c === '"' || $c === '`') {
                $start  = $i;
                $quote  = $c;
                $closed = false;
                $i++;

                while ($i < $len) {
                    if ($quote !== '`' && $sql[$i] === '\\') {
                        $i += 2;
                        continue;
                    }

                    if ($sql[$i] === $quote) {
                        if ($i + 1 < $len && $sql[$i + 1] === $quote) {
                            $i += 2;
                            continue;
                        }

                        $closed = true;
                        break;
                    }

                    $i++;
                }

                if (!$closed) {
                    $broken = true;
                }

                $i++;
                $clean .= substr($sql, $start, min($i, $len) - $start);
                $code  .= $quote === '`' ? ' _id_ ' : " '' ";
                continue;
            }

            // Commentaire "-- " (MySQL exige une espace ou un controle apres).
            if ($c === '-' && $next === '-' && ($i + 2 >= $len || ctype_space($sql[$i + 2]) || ord($sql[$i + 2]) < 32)) {
                $eol = strpos($sql, "\n", $i);
                $i   = $eol === false ? $len : $eol;
                $code  .= ' ';
                $clean .= ' ';
                continue;
            }

            // Commentaire "#".
            if ($c === '#') {
                $eol = strpos($sql, "\n", $i);
                $i   = $eol === false ? $len : $eol;
                $code  .= ' ';
                $clean .= ' ';
                continue;
            }

            // Commentaire bloc.
            if ($c === '/' && $next === '*') {
                $third = $i + 2 < $len ? $sql[$i + 2] : '';
                $four  = $i + 3 < $len ? $sql[$i + 3] : '';

                // /*! ... */ et /*M! ... */ : le contenu est execute.
                if ($third === '!' || ($third === 'M' && $four === '!')) {
                    $exec = true;
                }

                $end = strpos($sql, '*/', $i + 2);

                if ($end === false) {
                    $broken = true;
                    $i      = $len;
                    $code  .= ' ';
                    $clean .= ' ';
                    continue;
                }

                // Les indications d'optimiseur /*+ ... */ restent dans 'clean'.
                $clean .= $third === '+' ? substr($sql, $i, $end + 2 - $i) : ' ';
                $code  .= ' ';
                $i      = $end + 2;
                continue;
            }

            $code  .= $c;
            $clean .= $c;
            $i++;
        }

        return ['code' => $code, 'clean' => $clean, 'exec' => $exec, 'broken' => $broken];
    }

    /** Retire les espaces et les points-virgules de fin. */
    private static function trimTerminator(string $sql): string
    {
        return rtrim(trim($sql), " \t\r\n\0\x0B;");
    }

    /** Premier mot de la requete, en majuscules ('' s'il n'y en a pas). */
    private static function firstKeywordOf(string $code): string
    {
        $code = ltrim($code, " \t\r\n\0\x0B(");

        return preg_match('/^[A-Za-z]+/', $code, $m) ? strtoupper($m[0]) : '';
    }

    /** Premier mot de la requete, en majuscules. */
    public static function firstKeyword(string $sql): string
    {
        return self::firstKeywordOf(self::scan($sql)['code']);
    }

    /**
     * Classe une requete deja passee par scan().
     */
    private static function classifyReadOnly(string $code): bool
    {
        $code = self::trimTerminator($code);

        // Plusieurs instructions : on refuse.
        if ($code === '' || str_contains($code, ';')) {
            return false;
        }

        $first = self::firstKeywordOf($code);

        switch ($first) {
            case 'SELECT':
            case 'WITH':
                if (preg_match('/\bINTO\s+(OUTFILE|DUMPFILE)\b/i', $code)) {
                    return false;
                }

                if (preg_match('/\bLOAD_FILE\s*\(/i', $code)) {
                    return false;
                }

                // WITH peut introduire un UPDATE ou un DELETE.
                if ($first === 'WITH' && preg_match(self::WRITE_KEYWORDS, $code)) {
                    return false;
                }

                return true;

            case 'SHOW':
            case 'DESCRIBE':
            case 'DESC':
                return true;

            case 'EXPLAIN':
                // EXPLAIN seul n'execute rien ; EXPLAIN ANALYZE execute.
                if (!preg_match('/^\s*\(?\s*EXPLAIN\s+ANALYZE\b/i', $code)) {
                    return true;
                }

                $rest = preg_replace('/^\s*\(?\s*EXPLAIN\s+ANALYZE\s+(?:FORMAT\s*=\s*\w+\s+)?/i', '', $code);

                return self::classifyReadOnly((string) $rest);
        }

        return false;
    }

    /**
     * Vrai si la requete ne fait que lire : une seule instruction SELECT,
     * WITH (sans ecriture), SHOW, DESCRIBE ou EXPLAIN.
     */
    public static function isReadOnly(string $sql): bool
    {
        $scan = self::scan($sql);

        if ($scan['exec'] || $scan['broken']) {
            return false;
        }

        return self::classifyReadOnly($scan['code']);
    }

    /**
     * Vrai si le resultat de la requete se lit comme un jeu de lignes
     * (loadAssocList) plutot que comme un nombre de lignes affectees.
     */
    public static function returnsRows(string $sql): bool
    {
        $scan  = self::scan($sql);
        $first = self::firstKeywordOf($scan['code']);

        if (!in_array($first, self::ROW_RETURNING, true)) {
            return false;
        }

        // Un WITH qui ecrit ne renvoie pas de lignes.
        if ($first === 'WITH') {
            return self::classifyReadOnly($scan['code']);
        }

        return true;
    }

    /**
     * Vrai si la requete peut etre envelopee dans une sous-requete
     * (pagination, comptage, tri). SHOW, DESCRIBE et EXPLAIN ne le peuvent pas.
     */
    public static function isDerivable(string $sql): bool
    {
        return in_array(self::firstKeyword($sql), ['SELECT', 'WITH'], true);
    }

    /**
     * Requete nettoyee : sans commentaires, sans point-virgule final.
     * A utiliser avant d'envelopper ou de completer la requete.
     */
    public static function prepare(string $sql): string
    {
        return self::trimTerminator(self::scan($sql)['clean']);
    }

    /** SELECT COUNT(*) sur la requete, quelle que soit sa forme. */
    public static function countQuery(string $sql): string
    {
        return "SELECT COUNT(*) FROM (\n" . self::prepare($sql) . "\n) AS jmm_cnt";
    }

    /**
     * Une page de resultats, avec un tri facultatif.
     *
     * La requete est enveloppee plutot que prolongee : une requete qui porte
     * deja un LIMIT ou un ORDER BY, ou qui finit par un commentaire, ne
     * casse plus.
     *
     * @param string $orderBy Clause deja quotee, sans le mot ORDER BY.
     */
    public static function pageQuery(string $sql, int $offset, int $limit, string $orderBy = ''): string
    {
        $query = "SELECT * FROM (\n" . self::prepare($sql) . "\n) AS jmm_page";

        if ($orderBy !== '') {
            $query .= ' ORDER BY ' . $orderBy;
        }

        return $query . ' LIMIT ' . max(0, $offset) . ', ' . max(1, $limit);
    }

    /**
     * Valide un tri de la forme "colonne [ASC|DESC][, ...]" et renvoie la
     * liste des couples [colonne, sens]. Les elements invalides sont ignores.
     *
     * @return array<int,array{0:string,1:string}>
     */
    public static function parseSorting(string $sorting): array
    {
        $result = [];

        foreach (explode(',', $sorting) as $part) {
            if (!preg_match('/^\s*([A-Za-z0-9_]{1,64})(?:\s+(ASC|DESC))?\s*$/i', $part, $m)) {
                continue;
            }

            $result[] = [$m[1], strtoupper($m[2] ?? 'ASC')];
        }

        return $result;
    }
}
