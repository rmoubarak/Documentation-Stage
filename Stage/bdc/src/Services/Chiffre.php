<?php

namespace App\Services;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Chiffrement et déchiffrement de texte grâce à la librairie defuse
 * La clef utilisée ici est stockée grâce au mécanisme de chiffrement asymétrique Secret de Symfony (Cryptographic Keys).
 *
 * Class Chiffre
 * @package App\Services
 */
class Chiffre
{
    private $encryption_key;

    public function __construct(string $encryption_key)
    {
        $this->encryption_key = Key::loadFromAsciiSafeString($encryption_key);
    }

    public function encrypt($secret): string
    {
        return Crypto::encrypt($secret, $this->encryption_key);
    }

    public function decrypt($ciphertext): string
    {
        try {
            return Crypto::decrypt($ciphertext, $this->encryption_key);
        } catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
            return $ex->getMessage();
        }
    }
}