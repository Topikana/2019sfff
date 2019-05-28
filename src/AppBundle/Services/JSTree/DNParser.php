<?php

namespace AppBundle\Services\JSTree;

class DNParser
{
    /**
     * Converts DNs replacing blank spaces with '%20'.
     * Don't use URLEncode, as the '/'s are necessary and this function replaces them as well.
     */
    public function parseDN($userDN)
    {
        return str_replace(' ', '%20', $userDN);
    }
}
