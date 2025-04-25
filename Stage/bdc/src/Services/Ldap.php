<?php

namespace App\Services;

use App\Entity\Direction;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class Ldap
{
    private $rs;
    private $ds;

    /**
     * @param string $host
     * @param string $dn
     * @param string $baseDn
     * @param string $ldapPass
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private string $host,
        private string $dn,
        private string $baseDn,
        private string $ldapPass,
        private EntityManagerInterface $em
    )
    {
        $this->rs = ['uid', 'cn', 'sn', 'givenName', 'mail', 'employeeNumber', 'crnpdcatttelephoneintl', 'title', 'jpegphoto',
            'crnpdcattfonctionlibelle', 'crnpdcattasciiprenom', 'uid', 'crnpdcattdirectionsigle', 'mailroutingaddress'];

        $this->connect();
    }

    public function __destruct()
    {
        if ($this->ds) {
            ldap_close($this->ds);
        }
    }

    /**
     * Connexion au serveur AD
     */
    private function connect(): void
    {
        $this->ds = ldap_connect($this->host);

        if ($this->ds) {
            ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->ds, LDAP_OPT_REFERRALS, 0);
            ldap_bind($this->ds, $this->dn, $this->ldapPass);
        }
    }

    /**
     * Retourne les utilisateurs AD par le nom
     *
     * @param string $str
     * @param int $att 0: siège uniquement ; 1: ATT uniquement ; siège + ATT
     * @return array|string
     */
    public function findUsersByName(string $str, int $att = 0): array|string
    {
        if ($this->ds && $str) {
            if ($att == 0) {
                $filtre = sprintf('(&(sn=*%s*)(objectClass=crnpdcobjagent)(|(businessCategory=PICARD)(businessCategory=INTERNE)))', $str);
            } else if ($att == 1) {
                $filtre = sprintf('(&(sn=*%s*)(objectClass=crnpdcobjagent)(|(businessCategory=PICARDTOS)(businessCategory=TOS)))', $str);
            } else if ($att == 2) {
                $filtre = sprintf('(&(sn=*%s*)(objectClass=crnpdcobjagent)(|(businessCategory=PICARD)(businessCategory=INTERNE)(businessCategory=PICARDTOS)(businessCategory=TOS)))', $str);
            }

            $srdir = ldap_search($this->ds, $this->baseDn, $filtre, $this->rs);

            return ldap_get_entries($this->ds, $srdir);
        }

        return ldap_error($this->ds);
    }

    /**
     * Retourne un utilisateur AD par son login
     *
     * @param string $login
     * @return array|string
     */
    public function findUserByLogin($login): array|string
    {
        if ($this->ds && $login) {
            //$filtre = "(&(extensionAttribute5=$login)(objectCategory=organizationalPerson)(extensionAttribute5=*))";
            $filtre = sprintf('(uid=%s)', $login);
            $srdir = ldap_search($this->ds, 'ou=usagers,' . $this->baseDn, $filtre, $this->rs);

            return ldap_get_entries($this->ds, $srdir);
        }

        return ldap_error($this->ds);
    }

    public function findUserByEmail($email): array|string
    {
        if ($this->ds && $email) {
            $filtre = sprintf('(mail=%s)', $email);
            $srdir = ldap_search($this->ds, 'ou=usagers,' . $this->baseDn, $filtre, $this->rs);

            return ldap_get_entries($this->ds, $srdir);
        }

        return ldap_error($this->ds);
    }

    public function findServiceById($service_id): array|string
    {
        if ($this->ds && $service_id) {
            $filtre = sprintf('(cn=%s)', $service_id);
            $srdir = ldap_search($this->ds, 'ou=organisation,' . $this->baseDn, $filtre, $this->rs);

            return ldap_get_entries($this->ds, $srdir);
        }

        return ldap_error($this->ds);
    }

    /**
     * Renvoie le service d'un utilisateur (identifié par son DN)
     *
     * @param $dn
     * @return string
     */
    public function findServiceByDn($dn)
    {
        if ($this->ds && $dn) {
            $filtre = sprintf('(uniqueMember=%s)', $dn);
            $srdir = ldap_search($this->ds, 'ou=organisation,' . $this->baseDn, $filtre, $this->rs);

            return ldap_get_entries($this->ds, $srdir);
        }

        return '';
    }

    /**
     * Renvoie le code service d'un utilisateur (identifié par son DN)
     *
     * @param $dn
     * @return string
     */
    public function findServiceCodeByDn($dn)
    {
        $service = $this->findServiceByDn($dn);
        $service_dn = $this->getDn($service);

        if ($service_dn) {
            $parts = explode(',', $service_dn);

            return substr($parts[0], 3);
        }

        return '';
    }

    /**
     * Vérifie si un user a été retourné
     * @param $user
     * @return bool
     */
    public function checkUser($user): bool
    {
        return $user && $user != 'Success' && $user['count'] != 0;
    }

    /**
     * @param $service
     * @return string|null
     */
    public function getDn($service): ?string
    {
        if (isset($service[0]['dn'])) {
            return $service[0]['dn'];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getMatricule($user): ?string
    {
        if (isset($user[0]['employeenumber'][0])) {
            return $user[0]['employeenumber'][0];
        }
        if (isset($user['employeenumber'][0])) {
            return $user['employeenumber'][0];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getCivilite($user): ?string
    {
        if (isset($user[0]['title'])) {
            return $user[0]['title'][0];
        } else if (isset($user['title'])) {
            return $user['title'][0];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getNom($user): ?string
    {
        if (isset($user[0]['sn'])) {
            return $user[0]['sn'][0];
        } else if (isset($user['sn'])) {
            return $user['sn'][0];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getPrenom($user): ?string
    {
        if (isset($user[0]['givenname'])) {
            return ucfirst($user[0]['givenname'][0]);
        } else if (isset($user['givenname'])) {
            return $user['givenname'][0];
        } else if (isset($user[0]['crnpdcattasciiprenom'])) {
            return ucfirst($user[0]['crnpdcattasciiprenom'][0]);
        } else if (isset($user['crnpdcattasciiprenom'])) {
            return $user['crnpdcattasciiprenom'][0];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getEmail($user): ?string
    {
        if (isset($user[0]['mailroutingaddress'])) {
            return $user[0]['mailroutingaddress'][0];
        } else if (isset($user['mailroutingaddress'])) {
            return $user['mailroutingaddress'][0];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getTelephone($user): ?string
    {
        if (isset($user[0]['crnpdcatttelephoneintl'])) {
            return $user[0]['crnpdcatttelephoneintl'][0];
        } else if (isset($user['crnpdcatttelephoneintl'])) {
            return $user['crnpdcatttelephoneintl'][0];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getLogin($user): ?string
    {
        if (isset($user[0]['uid'])) {
            return $user[0]['uid'][0];
        } else if (isset($user['uid'])) {
            return $user['uid'][0];
        }

        return '';
    }

    /**
     * Retourne le login du owner
     * @param $user
     * @return string|null
     */
    public function getOwner($user): ?string
    {
        if (isset($user[0]['owner'])) {
            $owner = $user[0]['owner'][0];
            return $owner ? strtolower(substr($owner, strpos($owner, "uid=") + 4, strpos($owner, ",") - strpos($owner, "uid=") - 4)) : '';
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getFonction($user): ?string
    {
        if (isset($user[0]['crnpdcattfonctionlibelle'])) {
            return $user[0]['crnpdcattfonctionlibelle'][0];
        } else if (isset($user['crnpdcattfonctionlibelle'])) {
            return $user['crnpdcattfonctionlibelle'][0];
        }

        return '';
    }

    /**
     * @param $user
     * @return string|null
     */
    public function getDirectionSigle($user): ?string
    {
        if (isset($user[0]['crnpdcattdirectionsigle'])) {
            return ucfirst($user[0]['crnpdcattdirectionsigle'][0]);
        } else if (isset($user['crnpdcattdirectionsigle'])) {
            return ucfirst($user['crnpdcattdirectionsigle'][0]);
        }

        return '';
    }

    /**
     * @param $user
     *
     * @return null|string
     */
    public function getJpegPhoto($user): ?string
    {
        if (isset($user[0]['jpegphoto'])) {
            return base64_encode($user[0]['jpegphoto'][0]);
        } else if (isset($user['jpegphoto'])) {
            return $user['jpegphoto'][0];
        }

        return '';
    }
}