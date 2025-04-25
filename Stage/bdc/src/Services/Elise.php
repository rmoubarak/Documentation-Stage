<?php

namespace App\Services;

use SimpleXMLElement;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Elise
{
    public function __construct(
        private string              $application_id,
        private string              $application_key,
        private HttpClientInterface $client
    )
    {
    }

    public function getUserTokenByLogin(string $login): string|false
    {
        $response = $this->client->request('POST', 'https://elisetest.hautsdefrance.fr/GED/Elise/WebServiceApplication/EliseWebService.svc/soapOperation', [
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => 'Archimed.Elise.Application.Services/EliseWebService/UserIsAvailable'
            ],
            'body' => "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:arc=\"Archimed.Elise.Application.Services\" xmlns:arc1=\"http://schemas.datacontract.org/2004/07/Archimed.Elise.Application.Services\">
   <soapenv:Header/>
   <soapenv:Body>
      <arc:UserIsAvailable>
         <!--Optional:-->
         <arc:session>
            <!--Optional:-->
            <arc1:ApplicationID>" . $this->application_id . "</arc1:ApplicationID>
            <!--Optional:-->
            <arc1:ApplicationKey>" . $this->application_key . "</arc1:ApplicationKey>
            <!--Optional:-->
            <arc1:EliseVersionRequired>6.0</arc1:EliseVersionRequired>
            <!--Optional:-->
            <arc1:Instance>GED</arc1:Instance>
            <!--Optional:-->
           
            <!--Optional:-->
            <arc1:Password></arc1:Password>
            <!--Optional:-->
            <arc1:UserLogin>$login</arc1:UserLogin>
            <!--Optional:-->
            <arc1:VolatileToken></arc1:VolatileToken>
         </arc:session>
      </arc:UserIsAvailable>
   </soapenv:Body>
</soapenv:Envelope>",
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            return $this->getUserTokenByLoginParseXml($response->getContent());
        }

        return false;
    }

    private function getUserTokenByLoginParseXml(string $data)
    {
        // On ajoute les chemins absolus (non fournis par le XML d'Archimède)
        $data = $this->fixUri($data);

        $xml = new SimpleXMLElement($data);
        $xml->registerXPathNamespace('d', 'http://schemas.datacontract.org/2004/07/Archimed.Elise.Models');
        $name = $xml->xpath('//d:VolatileToken');

        return $name[0]->__toString();
    }

    public function getMailsByContactId(string $contactId, string $token): string|false
    {
        $response = $this->client->request('POST', 'https://elisetest.hautsdefrance.fr/GED/Elise/WebServiceApplication/EliseWebService.svc/soapOperation', [
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => 'Archimed.Elise.Application.Services/EliseWebService/GetContactMails'
            ],
            'body' => "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:arc=\"Archimed.Elise.Application.Services\" xmlns:arc1=\"http://schemas.datacontract.org/2004/07/Archimed.Elise.Application.Services\" xmlns:arr=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\" xmlns:arc2=\"http://schemas.datacontract.org/2004/07/Archimed.Elise.Models\">
   <soapenv:Header/>
   <soapenv:Body>
      <arc:GetContactMails>
         <!--Optional:-->
           <arc:session>
            <!--Optional:-->
            <arc1:ApplicationID>" . $this->application_id . "</arc1:ApplicationID>
            <!--Optional:-->
            <arc1:ApplicationKey>" . $this->application_key . "</arc1:ApplicationKey>
            <!--Optional:-->
            <arc1:EliseVersionRequired>6.0</arc1:EliseVersionRequired>
            <!--Optional:-->
            <arc1:Instance>GED</arc1:Instance>
            <!--Optional:-->
            <arc1:Language>FR-fr</arc1:Language>
            <!--Optional:-->           
            <!--Optional:-->
            <arc1:UserLogin></arc1:UserLogin>
            <arc1:VolatileToken>" . $token . "</arc1:VolatileToken>
         </arc:session>
         <!--Optional:-->
         <arc:searchFilter>
            <!--Optional:-->
            <arc2:ContactId>" . $contactId . "</arc2:ContactId>
            <!--Optional:-->
            <arc2:ContactType>LegalEntity</arc2:ContactType>
         </arc:searchFilter>
         <!--Optional:-->
         <arc:start>0</arc:start>
         <!--Optional:-->
         <arc:limit>100</arc:limit>
      </arc:GetContactMails>
   </soapenv:Body>
</soapenv:Envelope>",
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            return $response->getContent();
        }

        return false;
    }


    /**
     * Retourne un tableau de courriers
     *
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function parseMailsByContactId(string $data)
    {
        $data = $this->fixUri($data);

        $xml = new SimpleXMLElement($data);

        // enregistre le namespace 'b'
        $xml->registerXPathNamespace('b', 'http://schemas.datacontract.org/2004/07/Archimed.Elise.Models');

        // utilise XPath pour sélectionner les éléments CompleteEliseMail
        $completeEliseMails = $xml->xpath('//b:CompleteEliseMail');

        $messages = [];
        // récupère chaque enfant de l'élément CompleteEliseMail et le met dans un tableau, le fait récursivement
        $recursiveFetch = function ($element) use (&$recursiveFetch) {
            $message = [];
            $childs = $element->children('b', true);
            if ($childs) {
                foreach ($element->children('b', true) as $child) {
                    // vérifie si l'enfant est un tableau

                    if (!empty($child)) {
                        // si c'est le cas, récupère chaque enfant de l'enfant et le met dans un tableau
                        $message[$child->getName()] = $recursiveFetch($child);
                        continue;
                    }

                    $message[$child->getName()] = (string)$child;
                }
            } else {
                $message = (string)$element;
            }

            return $message;
        };

        foreach ($completeEliseMails as $completeEliseMail) {
            $messages[] = $recursiveFetch($completeEliseMail);
        }

        dd($messages);

        return $messages;
    }

    public function fixUri($data): array|string
    {
        // On ajoute les chemins absolus (non fournis par le XML d'Archimède)
        $data = str_replace('Archimed.Elise.Application.Services', 'http://schemas.datacontract.org/2004/07/Archimed.Elise.Application.Services', $data);
        $data = str_replace('Archimed.ServiceModel.Web.Data', 'http://schemas.datacontract.org/2004/07/Archimed.ServiceModel.Web.Data', $data);

        return $data;
    }
}