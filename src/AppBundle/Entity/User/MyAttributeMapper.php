<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 11/04/17
 * Time: 14:57
 */

namespace AppBundle\Entity\User;



use LightSaml\SpBundle\Security\Authentication\Token\SamlSpResponseToken;
use LightSaml\SpBundle\Security\User\AttributeMapperInterface;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Protocol\Response;


class MyAttributeMapper implements  AttributeMapperInterface
{
    /**
     * @param SamlSpResponseToken $token
     *
     * @return array
     */
    public function getAttributes(SamlSpResponseToken $token)
    {
        $attributes = array();
        $response = $token->getResponse();

        $reader = $response->getFirstAssertion();
        $responseAttr=array();
        $Roles=array();

        $dn="AAI DN";


        foreach ($reader->getFirstAttributeStatement()->getAllAttributes() as $attribute) {


            if ($attribute->getName() == 'urn:oid:1.3.6.1.4.1.5923.1.1.1.13') {
                $id = $attribute->getFirstAttributeValue();
            }


            if ($attribute->getName() == 'urn:oid:0.9.2342.19200300.100.1.3') {
                $email = $attribute->getFirstAttributeValue();
            }


            if ($attribute->getName() == 'urn:oid:2.5.4.49') {
                $dn = $attribute->getFirstAttributeValue();
            }

            if ($attribute->getName() == 'urn:oid:2.16.840.1.113730.3.1.241') {
                $username = $attribute->getFirstAttributeValue();

            }


            if ($attribute->getName() == 'urn:oid:1.3.6.1.4.1.5923.1.1.1.7') {
                $responseAttr[] = $attribute->getAllAttributeValues();
                foreach ($attribute->getAllAttributeValues() as $key => $attributeValue) {

                    if (strstr($attributeValue, ":goc.egi.eu:")) {
                        $attributeValue2 = explode(":goc.egi.eu:", $attributeValue);
                        $tabRoles = explode(":", $attributeValue2[1]);
                        $entityName = $tabRoles[1];
                        $Role = str_replace("+", " ", substr($tabRoles[2], 0, strpos($tabRoles[2], '@egi.eu')));
                        $tabentityRole = explode("+", $tabRoles[2]);
                        $entityType = strtolower($tabentityRole[0]);
                        $Roles[$entityType][$entityName][] = $Role;

                    }


                }

            }

        }
            return [
                'username' => $username,
                'email' => $email,
                'dn' => $dn,
                'id' => $id,
                'roles' => $Roles
            ];






    }

    /**
     * @param Response $response
     * @return array
     */
    public function getAttributesFromResponse(Response $response)
    {
        $attributes = array();
        $assertions = $response->getBearerAssertions();

        foreach($assertions as $assertion) {
            $assertionItems = $assertion->getAllItems();
            $attributesPartial = $this->getAttributesFromAssertionItems($assertionItems);
            $attributes = array_merge($attributes, $attributesPartial);

        }

        return $attributes;
    }

    /**
     * Receives an array of LightSaml\Model\Assertion\AttributeStatement
     * Returns an array of attributes
     *
     * @param array $assertionItems
     * @return array
     */
    protected function getAttributesFromAssertionItems(array $assertionItems)
    {
        $attributes = array();

        foreach ($assertionItems as $item) {
            if ($item instanceof AttributeStatement) {
                foreach ($item->getAllAttributes() as $itemAttr) {
                    if($itemAttr->getName() !== null) {
                        $attributes[$itemAttr->getName()] = $itemAttr->getAllAttributeValues();
                    }
                }
            }
        }

        return $attributes;
    }
}